<?php
declare(strict_types=1);

namespace Plugins\GeminiAI\src;

use App\Backend\Controller\BaseController;
use Magepattern\Component\Tool\SmartyTool;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\FormTool;
use Plugins\GeminiAI\db\BackendDb;
use Plugins\GeminiAI\src\Api\GeminiApiService;

class BackendController extends BaseController
{
    public function run(): void
    {
        SmartyTool::addTemplateDir('admin', ROOT_DIR . 'plugins' . DS . 'GeminiAI' . DS . 'views' . DS . 'admin');

        $action = $_GET['action'] ?? 'index';

        // 🔒 Action d'écriture (sauvegarde) : on garde la vérification CSRF dans la méthode
        if ($action === 'saveKey' && Request::isMethod('POST')) {
            $this->processSaveKey();
            return;
        }

        //  Action de lecture (API) : le BaseController s'est déjà assuré que c'est un Admin.
        if ($action === 'generate' && Request::isMethod('POST')) {
            $this->processGenerateContent();
            return;
        }

        if ($action === 'checkStatus') {
            $this->checkStatus();
            return;
        }

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->index();
        }
    }

    private function index(): void
    {
        $db = new BackendDb();
        $apiKey = $db->getKey();

        $this->view->assign([
            'title_plugin' => 'Configuration Gemini AI',
            'api_key_gc'   => $apiKey,
            'hashtoken'    => $this->session->getToken()
        ]);

        $this->view->display('config.tpl');
    }

    private function processSaveKey(): void
    {
        // Ici on garde le CSRF car on modifie la base de données !
        $token = $_POST['hashtoken'] ?? '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $apiKey = FormTool::simpleClean($_POST['api_key_gc'] ?? '');

        $db = new BackendDb();
        if ($db->saveKey($apiKey)) {
            $this->jsonResponse(true, 'La clé API Gemini a été sauvegardée avec succès.', ['type' => 'update']);
        } else {
            $this->jsonResponse(false, 'Erreur lors de la sauvegarde de la clé API.');
        }
    }

    /**
     * Reçoit la demande de TinyMCE, appelle l'API Google et renvoie le JSON formaté
     */
    private function processGenerateContent(): void
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true) ?? $_POST;

        // PLUS DE VÉRIFICATION CSRF ICI.
        // Si on arrive là, c'est que le BaseController a validé la session Admin.

        $db = new BackendDb();
        $apiKey = $db->getKey();

        if (empty($apiKey)) {
            $this->jsonResponse(false, 'La clé API Gemini n\'est pas configurée.');
        }

        $prompt  = FormTool::simpleClean($data['prompt'] ?? '');
        $context = $data['context'] ?? '';
        $options = $data['options'] ?? [];

        if (empty($prompt) && empty($context)) {
            $this->jsonResponse(false, 'La requête envoyée à l\'IA est vide.');
        }

        $apiService = new GeminiApiService($apiKey, $this->logger);
        $result = $apiService->generate($prompt, $context, $options);

        if ($result['success']) {
            $this->jsonResponse(true, 'Contenu généré avec succès.', [
                'content' => $result['data']
            ]);
        } else {
            $this->jsonResponse(false, $result['message']);
        }
    }
    private function checkStatus(): void
    {
        $db = new BackendDb();
        $apiKey = $db->getKey();

        if (empty($apiKey)) {
            $this->jsonResponse(false, 'Clé manquante');
            return;
        }

        $apiService = new GeminiApiService($apiKey, $this->logger);
        $result = $apiService->testConnection();

        if ($result['success']) {
            $this->jsonResponse(true, 'Connecté');
        } else {
            $this->jsonResponse(false, $result['message']);
        }
    }
}