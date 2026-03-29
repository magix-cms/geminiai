<?php
declare(strict_types=1);

namespace Plugins\GeminiAI\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class BackendDb extends BaseDb
{
    /**
     * Récupère la clé API de Gemini
     */
    public function getKey(): string
    {
        $qb = new QueryBuilder();
        $qb->select('api_key_gc')
            ->from('mc_geminiai_config')
            ->where('id_gc = 1');

        $result = $this->executeRow($qb);

        return $result['api_key_gc'] ?? '';
    }

    /**
     * Sauvegarde la clé API de Gemini (Upsert)
     */
    public function saveKey(string $apiKey): bool
    {
        // 1. On vérifie si la ligne de configuration existe déjà
        $checkQb = new QueryBuilder();
        $checkQb->select('id_gc')
            ->from('mc_geminiai_config')
            ->where('id_gc = 1');

        $exists = $this->executeRow($checkQb);

        $qb = new QueryBuilder();

        // 2. On met à jour ou on insère selon le résultat
        if ($exists) {
            $qb->update('mc_geminiai_config', [
                'api_key_gc' => $apiKey
            ])->where('id_gc = 1');

            return $this->executeUpdate($qb);
        } else {
            $qb->insert('mc_geminiai_config', [
                'id_gc' => 1,
                'api_key_gc' => $apiKey
            ]);

            return $this->executeInsert($qb) !== false;
        }
    }
}