CREATE TABLE mc_geminiai_config (
    id_gc tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT,
    api_key_gc varchar(255) NOT NULL,
    date_register timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id_gc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;