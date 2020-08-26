<?php

require_once "shared/CastToArray.php";

/**
 * Feeling
 *
 * @author Romain
 * @license GPL-3.0-or-later
 */
class Feeling implements CastToArray {
    /**
     * Nom de la table
     */
    private const TABLE_NAME = "feeling";

    /**
     * @var int ID
     */
    private int $id;

    /**
     * @var int Note
     */
    private int $grade;

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
     * @return int Note
     */
    public function getGrade() : int {
        return $this->grade;
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
            "grade" => $this->grade,
            "creationDate" => $this->creationDate,
            "userId" => $this->userId
        );
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Constructeur
     *
     * @param int $id ID
     * @param int $grade Note
     * @param int $creationDate Date de création
     * @param int $userId User ID
     */
    private function __construct(int $id, int $grade, int $creationDate, int $userId) {
        $this->id = $id;
        $this->grade = $grade;
        $this->creationDate = $creationDate;
        $this->userId = $userId;
    }

    /**
     * Constructeur par ID
     *
     * @param int $id
     *
     * @return Feeling|null Feeling
     */
    public static function getById(int $id) : ?self {
        $sql = "SELECT * FROM " . Database::DB_NAME . "." . self::TABLE_NAME . " WHERE id = ?";
        $resourceData = Database::executeAndGetArray($sql, "i", $id);

        if (empty($resourceData)) {
            return null;
        }
        else {
            return new self(
                $resourceData["id"],
                $resourceData["grade"],
                $resourceData["creation_date"],
                $resourceData["user_id"]
            );
        }
    }
}
