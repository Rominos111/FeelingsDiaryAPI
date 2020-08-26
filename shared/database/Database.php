<?php

/**
 * Class Database, singleton
 *
 * @author Romain
 * @author Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @license GPL-3.0-or-later
 * @see https://github.com/ythepaut/info406_serveur/blob/master/config/Database.php
 */
class Database {
    /**
     * Nom de la BDD
     */
    const DB_NAME = "FeelingsDiary";

    /**
     * @var mysqli|null Connexion mysqli
     */
    private static ?mysqli $connection = null;

    /**
     * DB connectée ou non
     *
     * @return bool Connectée ou non
     */
    public static function isConnected() : bool {
        return self::$connection !== null;
    }

    /**
     * Getter privé de l'instance
     *
     * @return mysqli Instance SQL
     */
    private static function getInstance() : mysqli {
        if (!self::isConnected()) {
            self::connect();
        }

        return self::$connection;
    }

    /**
     * Crée une connexion à la base de donnée
     *
     * @return void
     */
    public static function connect() : void {
        if (!self::isConnected()) {
            // Importation des identifiants de la base
            require_once("config/db-config.php");

            $host = $_SESSION["db"]["host"];
            $user = $_SESSION["db"]["user"];
            $password = $_SESSION["db"]["password"];
            $basename = $_SESSION["db"]["basename"];

            unset($_SESSION["db"]);

            self::$connection = new mysqli($host, $user, $password, $basename);

            if (self::$connection->connect_errno) {
                error_log("Database connection failed.", 0);
                trigger_error("Failed to connect to MySQL: " . self::$connection->connect_error, E_USER_ERROR);
            }
            else {
                self::$connection->set_charset("utf8");
            }
        }
    }

    /**
     * Exécute une requête SQL et retourne une liste
     *
     * @param string $query Query SQL
     * @param string|null $types Types à binder
     * @param mixed ...$values Valeurs à binder
     *
     * @example executeAndGetArray("SELECT * FROM table_name WHERE arg1 = ? AND arg2 = ?", "ii", arg1, arg2);
     * @example executeAndGetArray("SELECT * FROM table_name");
     *
     * @return array|null Liste, null si la requête a échoué
     */
    public static function executeAndGetArray(string $query, ?string $types = null, ...$values) : ?array {
        $db = Database::getInstance();

        $query = $db->prepare($query);

        if (!is_null($types) && !is_null($values)) {
            $query->bind_param($types, ...$values);
        }

        $query->execute();

        $result = $query->get_result();
        $query->close();

        if ($result === false) {
            return null;
        }
        else {
            $data = $result->fetch_assoc();
            return is_null($data) ? array() : $data;
        }
    }

    /**
     * Exécute une requête SQL
     *
     * @param string $query Query SQL
     *
     * @return bool
     */
    public static function executeOnly(string $query) : bool {
        $db = Database::getInstance();

        $query = $db->prepare($query);
        $query->execute();

        $result = $query->get_result();
        $query->close();

        return true;
    }
}
