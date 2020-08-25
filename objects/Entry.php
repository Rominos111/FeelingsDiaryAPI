<?php

/**
 * Classe Entry
 *
 * @author Romain
 * @license GPL-3.0-or-later
 */
class Entry {
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
    private int $date;

    /**
     * @var int User ID
     */
    private int $userId;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Constructeur par ID
     *
     * @param int $id
     * @return Entry Entry
     * @throws DatabaseConnectionException En cas d'erreur de BDD
     */
    public static function getByID(int $id) : self {
        $db = Database::getInstance();

        $query = $db->prepare("SELECT * FROM" . Database::DB_NAME . "."  . self::TABLE_NAME . "WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $resourceData = $result->fetch_assoc();

        return new self(
            $resourceData["id"],
            $resourceData["content"],
            $resourceData["date"],
            $resourceData["user_id"]
        );
    }

    /**
     * Constructeur
     *
     * @param int $id ID
     * @param string $content Contenu du message
     * @param int $date Date de création
     * @param int $userId User ID
     */
    private function __construct(int $id, string $content, int $date, int $userId) {
        $this->id = $id;
        $this->content = $content;
        $this->date = $date;
        $this->userId = $userId;
    }
}
