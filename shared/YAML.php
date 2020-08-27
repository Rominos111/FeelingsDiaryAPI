<?php

require_once "__php__";

require_once "libs/YAML/Spyc.php";

/**
 * Classe ajoutant une couche d'abstraction pour gérer YAML
 */
abstract class YAML {
    /**
     * Charge un fichier YAML
     *
     * @param string $path Chemin relatif (ex: config/file.yml)
     *
     * @return array Fichier YAML chargé en array
     */
    public static function loadFromFile(string $path) : array {
        return spyc_load_file($path);
    }
}
