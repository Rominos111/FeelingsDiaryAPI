<?php

require_once "__php__";

require_once "shared/YAML.php";

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
     * @var string Nom de la BDD
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
     * Récupère un objet Résultat d'une query
     *
     * @param string $query Query
     * @param string|null $types Types à binder
     * @param mixed ...$values Valeurs à binder
     * @param mysqli|null $db Connexion à utiliser
     *
     * @return mysqli_result|null Objet Résultat, null si erreur dans la query
     */
    private static function getStatementResult(string $query,
                                               ?string $types,
                                               array $values,
                                               mysqli $db=null) : ?mysqli_result {
        if (is_null($db)) {
            $db = Database::getInstance();
        }

        $stmt = $db->prepare($query);

        if (!is_null($types) && !is_null($values)) {
            $stmt->bind_param($types, ...$values);
        }

        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        return ($result === false) ? null : $result;
    }

    /**
     * Crée une connexion à la base de donnée
     *
     * @return void
     */
    public static function connect() : void {
        if (!self::isConnected()) {
            // Importation des identifiants de la base

            $config = YAML::loadFromFile($_ENV["ROOT_PATH"] . "config/db_config.yaml");

            $host = $config["host"];
            $user = $config["user"];
            $password = $config["password"];
            // self::$DB_NAME = $config["basename"];

            self::$connection = new mysqli($host, $user, $password, self::DB_NAME);

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
        $result = self::getStatementResult($query, $types, $values);

        if (is_null($result)) {
            return null;
        }
        else {
            $res = array();

            while ($data = $result->fetch_assoc()) {
                $res[] = $data;
            }

            if (is_null($res)) {
                return null;
            }
            else if (count($res) === 1) {
                return $res[0];
            }
            else {
                return $res;
            }
        }
    }

    /**
     * Exécute une requête SQL
     *
     * @param string $query Query SQL
     * @param string|null $types Types à binder
     * @param mixed ...$values Valeurs à binder
     *
     * @return bool OK ou non
     */
    public static function executeOnly(string $query, ?string $types = null, ...$values) : bool {
        return is_null(self::getStatementResult($query, $types, $values));
    }

    /**
     * Exécute une requête SQL
     *
     * @param string $query Query SQL
     * @param string|null $types Types à binder
     * @param mixed ...$values Valeurs à binder
     *
     * @return int ID
     */
    public static function executeAndGetID(string $query, ?string $types = null, ...$values) : ?int {
        $db = Database::getInstance();
        self::getStatementResult($query, $types, $values, $db);
        return $db->insert_id;
    }
}
