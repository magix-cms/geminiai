{extends file="layout.tpl"}

{block name='head:title'}Configuration Gemini AI{/block}

{block name='article'}
    {* --- EN-TÊTE DE LA PAGE --- *}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-stars text-primary me-2"></i> Intelligence Artificielle
        </h1>
        {* Badge de statut dynamique *}
        <div id="api-status-container">
            <span class="badge bg-secondary" id="api-status-badge">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Vérification...
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">

            {* --- CARTE PRINCIPALE --- *}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-gear-fill me-2"></i> Configuration Google Gemini
                    </h6>
                </div>

                <div class="card-body p-4 bg-light">

                    {* --- ALERTE INFORMATIVE --- *}
                    <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-start">
                        <i class="bi bi-info-circle-fill fs-4 text-info me-3"></i>
                        <div>
                            <strong>Important :</strong> Ce plugin utilise l'API de Google Gemini pour l'assistance à la rédaction.<br>
                            Assurez-vous de générer une clé d'API valide dans votre <a href="https://aistudio.google.com/app/apikey" target="_blank" class="alert-link text-decoration-underline">console Google AI Studio</a> pour activer ces fonctionnalités.
                        </div>
                    </div>

                    {* --- FORMULAIRE MAGIX --- *}
                    <form id="geminiai_form" action="index.php?controller=GeminiAI&action=saveKey" method="post" class="validate_form bg-white p-4 rounded border shadow-sm">

                        {* Jeton de sécurité obligatoire *}
                        <input type="hidden" name="hashtoken" value="{$hashtoken|default:''}">

                        {* CHAMP UNIQUE : CLÉ API *}
                        <div class="mb-4">
                            <label for="api_key_gc" class="form-label fw-bold text-dark">
                                Clé API <span class="badge bg-danger ms-1 fw-normal">API Key</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                <input type="password" id="api_key_gc" name="api_key_gc" class="form-control border-start-0 ps-0" value="{$api_key_gc|escape:'html'}" placeholder="Exemple : AIzaSy..." required>
                                <button class="btn btn-outline-secondary bg-light" type="button" id="toggleApiKey" title="Afficher/Masquer la clé">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            <div class="form-text mt-2 text-danger small fw-medium">
                                <i class="bi bi-exclamation-triangle me-1"></i> Ne partagez jamais cette clé. Elle sert à la communication sécurisée entre votre serveur et Google.
                            </div>
                        </div>

                        {* BOUTON DE SAUVEGARDE *}
                        <hr class="my-4 text-muted opacity-25">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Enregistrer la configuration
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            {* --- CARTE DE STATUT / AIDE --- *}
            <div class="card shadow-sm border-0 border-start border-success border-4 mt-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle text-success fs-4 me-3"></i>
                        <div class="text-muted small">
                            <strong>Fonctionnement :</strong> Une fois votre clé validée (badge "Connecté" en haut), l'assistant s'active dans TinyMCE.
                            Si le statut indique une erreur, vérifiez que votre clé n'a pas de restrictions d'IP ou de domaine dans Google AI Studio.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
{/block}

{block name='javascripts' append}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleApiKey');
            const apiKeyInput = document.getElementById('api_key_gc');
            // Correction de l'ID ici (tiret au lieu d'underscore)
            const statusBadge = document.getElementById('api-status-badge');

            // 1. Logique Afficher/Masquer la clé
            if (toggleBtn && apiKeyInput) {
                toggleBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (apiKeyInput.type === 'password') {
                        apiKeyInput.type = 'text';
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    } else {
                        apiKeyInput.type = 'password';
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    }
                });
            }

            // 2. Fonction de vérification du statut (Vanilla JS)
            function checkApiStatus() {
                if (!statusBadge) return;

                fetch('index.php?controller=GeminiAI&action=checkStatus')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            statusBadge.className = 'badge bg-success';
                            statusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Connecté';
                        } else {
                            statusBadge.className = 'badge bg-warning text-dark';
                            statusBadge.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + data.message;
                        }
                    })
                    .catch(error => {
                        statusBadge.className = 'badge bg-danger';
                        statusBadge.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Erreur de liaison';
                    });
            }

            // Lancer la vérification au chargement
            checkApiStatus();

            // 3. Remplacement de jQuery pour rafraîchir le badge après sauvegarde
            // On écoute la soumission du formulaire pour relancer le test après un court délai
            const form = document.getElementById('geminiai_form');
            if (form) {
                form.addEventListener('submit', function() {
                    // On attend 1.5s (le temps que Magix traite l'AJAX de sauvegarde)
                    setTimeout(checkApiStatus, 1500);
                });
            }
        });
    </script>
{/block}