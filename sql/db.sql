CREATE TABLE mc_geminiai_config (
    id_gc smallint(1) UNSIGNED NOT NULL AUTO_INCREMENT,
    api_key_gc varchar(150) NOT NULL,
    date_register timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id_gc`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
