<?php
declare(strict_types=1);

namespace Plugins\GeminiAI\src\Api;

use Magepattern\Component\Debug\Logger;

class GeminiApiService
{
    // L'URL de base de l'API Gemini
    private const API_BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';

    // Le modèle recommandé par Google pour les tâches rapides et le traitement de texte
    private const DEFAULT_MODEL = 'gemini-flash-latest';

    public function __construct(
        private readonly string $apiKey,
        private readonly Logger $logger
    ) {}

    /**
     * Génère du contenu via l'API Gemini
     * * @param string $prompt La demande de l'utilisateur
     * @param string $context Le texte source ou contexte éventuel (ex: texte à traduire ou à corriger)
     * @param array $options Options de génération (action_type, language, tone, length)
     * @return array Tableau associatif ['success' => bool, 'data' => string|null, 'message' => string|null]
     */
    public function generate(string $prompt, string $context = '', array $options = []): array
    {
        $action = $options['action_type'] ?? 'write';
        $lang   = $options['language'] ?? 'français';
        $tone   = $options['tone'] ?? 'professionnel';
        $length = $options['length'] ?? 'standard';

        // 1. SYSTEM INSTRUCTION NATIVE (Directive absolue pour le comportement de l'IA)
        $systemInstruction = "Tu es un assistant de rédaction intégré à l'éditeur TinyMCE d'un CMS. "
            . "RÈGLE ABSOLUE : Réponds UNIQUEMENT avec le contenu du corps de texte, au format HTML sémantique. "
            . "INTERDICTION : Ne génère JAMAIS de balises <html>, <head>, <body>, ou <!DOCTYPE>. "
            . "FORMAT : Utilise uniquement des balises (<p>, <h2>, <h3>, <strong>, <ul>, <li>, etc.). "
            . "Ne mets JAMAIS ton code dans un bloc Markdown (comme ```html). Pas de commentaires HTML.";

        // 2. PRÉPARATION DU PROMPT UTILISATEUR
        if ($action === 'translate') {
            $userPrompt = "MISSION : Traduire le texte en {$lang}. Garde le balisage HTML parfaitement intact.\nTon : {$tone}.\n\nTexte à traduire :\n{$context}\n\nNotes de l'utilisateur : {$prompt}";
        } else {
            $userPrompt = "MISSION : Rédaction/Correction.\nTon : {$tone}.\nLongueur attendue : {$length}.\n\n"
                . (!empty($context) ? "Texte source : {$context}\n\n" : "")
                . "Demande de l'utilisateur : {$prompt}";
        }

        // 3. CONSTRUCTION DU PAYLOAD JSON
        $payload = [
            "systemInstruction" => [
                "parts" => [["text" => $systemInstruction]]
            ],
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [["text" => $userPrompt]]
                ]
            ],
            "safetySettings" => [
                ["category" => "HARM_CATEGORY_HARASSMENT", "threshold" => "BLOCK_ONLY_HIGH"],
                ["category" => "HARM_CATEGORY_HATE_SPEECH", "threshold" => "BLOCK_ONLY_HIGH"],
                ["category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold" => "BLOCK_ONLY_HIGH"],
                ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => "BLOCK_ONLY_HIGH"]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "maxOutputTokens" => 2048
            ]
        ];

        $endpoint = self::DEFAULT_MODEL . ":generateContent";
        return $this->callApi($endpoint, $payload);
    }

    /**
     * Exécute la requête cURL vers l'API Google
     */
    private function callApi(string $endpoint, array $payload): array
    {
        $url = self::API_BASE_URL . $endpoint . "?key=" . $this->apiKey;

        $ch = curl_init($url);

        try {
            $jsonPayload = json_encode($payload, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->log("Gemini JSON Encode Error: " . $e->getMessage(), 'error');
            return ['success' => false, 'message' => "Erreur interne lors de la préparation des données."];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        // Gestion des erreurs de connexion HTTP
        if ($err) {
            $this->logger->log("Gemini API Curl Error: " . $err, 'error');
            return ['success' => false, 'message' => "Erreur de connexion à l'API : " . $err];
        }

        $result = json_decode((string)$response, true);

        // Gestion des erreurs renvoyées par Google (Clé invalide, quota dépassé, etc.)
        if (isset($result['error'])) {
            $errorMsg = $result['error']['message'] ?? 'Erreur inconnue';
            $this->logger->log("Gemini API Error: " . $errorMsg, 'error');
            return ['success' => false, 'message' => "Erreur API Google : " . $errorMsg];
        }

        // Filtres de sécurité déclenchés par Google
        if (isset($result['candidates'][0]['finishReason']) && $result['candidates'][0]['finishReason'] === 'SAFETY') {
            return ['success' => false, 'message' => "Erreur : Le contenu a été bloqué par les filtres de sécurité de Google."];
        }

        // Succès : Extraction et nettoyage final du texte généré
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $rawHtml = $result['candidates'][0]['content']['parts'][0]['text'];
            return ['success' => true, 'data' => $this->cleanHtml($rawHtml)];
        }

        return ['success' => false, 'message' => "L'IA n'a pas renvoyé de réponse exploitable."];
    }

    /**
     * Nettoyage chirurgical du HTML pour TinyMCE
     */
    private function cleanHtml(string $html): string
    {
        // 1. Supprime les blocs de code Markdown (```html ... ```)
        $html = preg_replace('/```(?:html)?(.*?)```/is', '$1', $html);

        // 2. Suppression des commentaires HTML
        $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);

        // 3. Suppression des balises structurelles interdites
        $forbidden = ['<!DOCTYPE html>', '<html>', '</html>', '<head>', '</head>', '<body>', '</body>'];
        $html = str_ireplace($forbidden, '', $html);

        // 4. Suppression des attributs de style/ID/JS (pour garder la main sur le CSS du site)
        $html = preg_replace('/\s+(style|class|id|on[a-z]+)="[^"]*"/i', '', $html);

        return trim($html);
    }

    /**
     * Debug : Liste les modèles
     */
    public function getModelList(): array
    {
        $ch = curl_init(self::API_BASE_URL . "?key=" . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        $res = json_decode((string)$response, true);
        return $res['models'] ?? [];
    }

    /**
     * Debug : Test de connexion
     */
    public function testConnection(): array
    {
        $payload = ["contents" => [["parts" => [["text" => "Réponds OK"]]]]];
        return $this->callApi(self::DEFAULT_MODEL . ":generateContent", $payload);
    }
}