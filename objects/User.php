<?php

require_once "shared/CastToArray.php";

/**
 * Utilisateur
 *
 * @author Romain
 * @license GPL-3.0-or-later
 */
class User implements CastToArray {
    /**
     * Nom de la table
     */
    private const TABLE_NAME = "user";

    /**
     * @var int ID
     */
    private int $id;

    /**
     * @var string Nom d'utilisateur
     */
    private string $username;

    /**
     * @var int Date d'inscription
     */
    private int $registrationDate;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return int ID
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string Nom d'utilisateur
     */
    public function getUsername() : string {
        return $this->username;
    }

    /**
     * @return int Date d'inscription
     */
    public function getRegistrationDate() : int {
        return $this->registrationDate;
    }

    /**
     * @return array Classe castée en array
     */
    public function toArray() : array {
        return array(
            "id" => $this->id,
            "username" => $this->username,
            "registrationDate" => $this->registrationDate
        );
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Constructeur
     *
     * @param int $id ID
     * @param string $username Nom d'utilisateur
     * @param int $registrationDate Date d'inscription
     */
    private function __construct(int $id, string $username, int $registrationDate) {
        $this->id = $id;
        $this->username = $username;
        $this->registrationDate = $registrationDate;
    }

    /**
     * Constructeur par ID
     *
     * @param int $id
     *
     * @return User|null User
     */
    public static function getById(int $id) : ?self {
        $sql = "SELECT id, username, registration_date FROM " . self::TABLE_NAME . " WHERE id = ?";
        $resourceData = Database::executeAndGetArray($sql, "i", $id);

        if (empty($resourceData)) {
            return null;
        }
        else {
            return new self(
                $resourceData["id"],
                $resourceData["username"],
                $resourceData["registration_date"],
            );
        }
    }

    public static function getByUsername(string $username) : ?self {
        $sql = "SELECT id, username, registration_date FROM " . self::TABLE_NAME . " WHERE username = ?";
        $resourceData = Database::executeAndGetArray($sql, "s", $username);

        if (empty($resourceData)) {
            return null;
        }
        else {
            return new self(
                $resourceData["id"],
                $resourceData["username"],
                $resourceData["registration_date"],
            );
        }
    }

    public static function canConnect(string $username, string $password) : bool {
        $sql = "SELECT hash FROM " . self::TABLE_NAME . " WHERE username = ?";
        $data = Database::executeAndGetArray($sql, "s", $username);

        return password_verify($password, $data["hash"]);
    }
}
