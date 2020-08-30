<?php

require_once "shared/CastToArray.php";

require_once "objects/User.php";
require_once "objects/exceptions/UserNotFoundException.php";

/**
 * Classe Entry
 *
 * @author Romain
 * @license GPL-3.0-or-later
 */
class Entry implements CastToArray {
    /**
     * Nom de la table
     */
    private const TABLE_NAME = "entry";

    /**
     * @var int ID
     */
    private int $id;

    /**
     * @var string Contenu du message
     */
    private string $content;

    /**
     * @var int Date de création
     */
    private int $creationDate;

    /**
     * @var int User ID
     */
    private int $userId;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return int ID
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string Contenu
     */
    public function getContent() : string {
        return $this->content;
    }

    /**
     * @return int Date de création
     */
    public function getCreationDate() : int {
        return $this->creationDate;
    }

    /**
     * @return int User ID
     */
    public function getUserId() : int {
        return $this->userId;
    }

    /**
     * @return array Classe castée en array
     */
    public function toArray() : array {
        return array(
            "id" => $this->id,
            "content" => $this->content,
            "creationDate" => $this->creationDate,
            "userId" => $this->userId
        );
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Constructeur
     *
     * @param int $id ID
     * @param string $content Contenu du message
     * @param int $creationDate Date de création
     * @param int $userId User ID
     */
    private function __construct(int $id, string $content, int $creationDate, int $userId) {
        $this->id = $id;
        $this->content = $content;
        $this->creationDate = $creationDate;
        $this->userId = $userId;
    }

    /**
     * Constructeur par ID
     *
     * @param int $id
     *
     * @return Entry|null Entry
     */
    public static function getById(int $id) : ?self {
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?";
        $resourceData = Database::executeAndGetArray($sql, "i", $id);

        if (empty($resourceData)) {
            return null;
        }
        else {
            return new self(
                $resourceData["id"],
                $resourceData["content"],
                $resourceData["creation_date"],
                $resourceData["user_id"]
            );
        }
    }

    /**
     * Liste les entrées selon l'utilisateur
     *
     * @param int $uid User ID
     *
     * @return array Entrées, sous la forme [["id" => 1], ["id" => 4], ["id" => 7]]
     */
    public static function listByUserId(int $uid) : array {
        $sql = "SELECT id FROM " . self::TABLE_NAME . " WHERE user_id = ?";
        return Database::executeAndGetArray($sql, "i", $uid);
    }

    /**
     * Crée une entrée
     *
     * @param string $content Contenu
     * @param int $uid User ID
     *
     * @return int ID
     *
     * @throws UserNotFoundException
     */
    public static function create(string $content, int $uid) : ?int {
        if (is_null(User::getById($uid))) {
            throw new UserNotFoundException();
        }

        $sql = "INSERT INTO " . self::TABLE_NAME . " (content, user_id) VALUES (?, ?)";
        return Database::executeAndGetID($sql, "si", $content, $uid);
    }
}
