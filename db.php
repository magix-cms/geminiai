<?php
class plugins_geminiai_db
{
    /**
     * @param $config
     * @param bool $params
     * @return mixed|null
     * @throws Exception
     */
    /**
     * @var debug_logger $logger
     */
    protected debug_logger $logger;

    /**
     * @param array $config
     * @param array $params
     * @return array|bool
     */
    public function fetchData(array $config, array $params = []) {
        if ($config['context'] === 'all') {
            switch ($config['type']) {
                case 'data':
                    $query = 'SELECT mo.* FROM mc_geminiai_config AS mo';
                    break;
                default:
                    return false;
            }

            try {
                return component_routing_db::layer()->fetchAll($query, $params);
            }
            catch (Exception $e) {
                if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            }
        }
        elseif ($config['context'] === 'one') {
            switch ($config['type']) {
                case 'root':
                    $query = 'SELECT * FROM mc_geminiai_config ORDER BY id_gc  DESC LIMIT 0,1';
                    break;
                default:
                    return false;
            }

            try {
                return component_routing_db::layer()->fetch($query, $params);
            }
            catch (Exception $e) {
                if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            }
        }
        return false;
    }
    /**
     * @param string $type
     * @param array $params
     * @return bool
     */
    public function insert(string $type, array $params = []): bool {
        switch ($type) {
            case 'config':
                $query = 'INSERT INTO mc_geminiai_config (api_key_gc, date_register)
                VALUE(:api_key_gc, NOW())';
                break;
            default:
                return false;
        }

        try {
            component_routing_db::layer()->insert($query,$params);
            return true;
        }
        catch (Exception $e) {
            if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
            $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            return false;
        }

    }

    /**
     * @param string $type
     * @param array $params
     * @return bool
     */
    public function update(string $type, array $params = []): bool {
        switch ($type) {
            case 'config':
                $query = 'UPDATE mc_geminiai_config
                    SET 
                       api_key_gc=:api_key_gc
                    WHERE id_gc=:id';
                break;
            default:
                return false;
        }

        try {
            component_routing_db::layer()->update($query,$params);
            return true;
        }
        catch (Exception $e) {
            if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
            $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            return false;
        }
    }
}
?>