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
     * Getter de l'instance
     *
     * @throws DatabaseConnectionException Si la BDD n'arrive pas à se connecter
     */
    public static function getInstance() : mysqli {
        if (!self::isConnected()) {
            self::connect();
        }

        return self::$connection;
    }

    /**
     * Crée une connexion à la base de donnée
     *
     * @return void
     *
     * @throws DatabaseConnectionException Si la DB n'arrive pas à se connecter
     */
    public static function connect() : void {
        if (self::isConnected()) {
            echo "déjà connecté";
        }
        else {
            // Importation des identifiants de la base
            require_once("config/db-config.php");

            $host = $_SESSION["db"]["host"];
            $user = $_SESSION["db"]["user"];
            $password = $_SESSION["db"]["password"];
            $basename = $_SESSION["db"]["basename"];

            unset($_SESSION["db"]);

            self::$connection = mysqli_connect($host, $user, $password, $basename);

            if (self::$connection === false) {
                throw new DatabaseConnectionException("Database connection failed.", 1);
            } else {
                mysqli_set_charset(self::$connection, "utf8");
            }
        }
    }
}
