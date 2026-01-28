<?php

class GeminiClient {
    private $apiKey;
    private $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent";

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Génère du contenu via Gemini
     */
    public function generate(string $prompt, string $context = '', array $options = []) {
        $action = $options['action_type'] ?? 'write';
        $lang   = $options['language'] ?? 'français';
        $tone   = $options['tone'] ?? 'professionnel';
        $length = $options['length'] ?? 'standard';

        // 1. SYSTEM INSTRUCTION (Renforcée pour éviter le HTML complet)
        $systemInstruction = "Tu es un assistant Magix CMS. "
            . "RÈGLE ABSOLUE : Réponds UNIQUEMENT avec le contenu du corps de texte. "
            . "INTERDICTION : Ne génère JAMAIS de balises <html>, <head>, <body>, ou <!DOCTYPE>. "
            . "FORMAT : Utilise uniquement des balises de contenu comme <p>, <h2>, <h3>, <strong>, <ul>, <li>. "
            . "STRUCTURE : Pas de Markdown (```html), pas de commentaires HTML .";

        if ($action === 'translate') {
            $mission = "MISSION : Traduire le texte en $lang. Garder le balisage HTML à l'identique. Ton : $tone.";
            $content = "Texte à traduire :\n$context\n\nNotes : $prompt";
        } else {
            $mission = "MISSION : Rédaction/Correction. Ton : $tone. Longueur : $length.";
            $content = ($context ? "Texte source : $context\n\n" : "") . "Demande : $prompt";
        }

        $payload = [
            "contents" => [
                ["parts" => [["text" => $systemInstruction . "\n" . $mission . "\n\n" . $content]]]
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

        return $this->callApi($payload);
    }

    /**
     * Appel à l'API Google
     */
    private function callApi($payload) {
        $ch = curl_init($this->apiUrl . "?key=" . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) return "Erreur de connexion : " . $err;

        $result = json_decode($response, true);

        // Gestion des erreurs API
        if (isset($result['error'])) {
            return "Erreur API : " . ($result['error']['message'] ?? 'Erreur inconnue');
        }

        // Extraction et nettoyage
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $this->cleanHtml($result['candidates'][0]['content']['parts'][0]['text']);
        }

        if (isset($result['candidates'][0]['finishReason']) && $result['candidates'][0]['finishReason'] === 'SAFETY') {
            return "Erreur : Le contenu a été bloqué par les filtres de sécurité.";
        }

        return "L'IA n'a pas pu générer de réponse.";
    }

    /**
     * Nettoyage chirurgical du HTML pour TinyMCE
     */
    private function cleanHtml($html) {
        // 1. Supprime les blocs de code Markdown (```html ... ```)
        $html = preg_replace('/```(?:html)?(.*?)```/is', '$1', $html);

        // 2. Supprime les commentaires HTML ()
        $html = preg_replace('//', '', $html);

        // 3. Supprime les balises de structure de page entière
        $forbidden = [
            '<!DOCTYPE html>', '<html>', '</html>',
            '<head>', '</head>', '<body>', '</body>',
            '<title>', '</title>', '<meta'
        ];

        // Supprime les balises elles-mêmes (mais pas forcément le contenu entre title par exemple)
        $html = str_ireplace($forbidden, '', $html);

        // 4. Supprime les attributs de style injectés par l'IA (inline CSS)
        $html = preg_replace('/ (style|class|id|onclick|onerror)="[^"]*"/', '', $html);

        return trim($html);
    }

    /**
     * @return void
     */
    public function modelList() :void {
        $apiKey = $this->apiKey;
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;                $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response, true);
        foreach($res['models'] as $m) {
            echo $m['name'] . "<br>";
        }
    }

    /**
     * @return void
     */
    public function connexionDebug() :void{
        $apiKey = $this->apiKey;
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $apiKey;

        $data = [
            "contents" => [
                ["parts" => [["text" => "Réponds 'OK' si tu reçois ce message."]]]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $result = json_decode($response, true);

        echo "<pre>";
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            echo "SUCCÈS ! Réponse de l'IA : " . $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            print_r($result);
        }
        echo "</pre>";
        curl_close($ch);
    }
}
?>