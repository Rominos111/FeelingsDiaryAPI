<?php

require_once "shared/CastToArray.php";

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
     * @return Entry|null Entry
     */
    public static function getByID(int $id) : ?self {
        $db = Database::getInstance();

        $query = $db->prepare("SELECT * FROM " . Database::DB_NAME . "."  . self::TABLE_NAME . " WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $resourceData = $result->fetch_assoc();

        if ($resourceData === null) {
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
}
