<?php

/**
 * Classe Employé
 *
 * @author Romain
 * @license GPL-3.0-or-later
 */
class Employee {
    /**
     * Nom de la table
     */
    private const TABLE_NAME = "employees";

    /**
     * @var int ID
     */
    private int $id;

    /**
     * @var string Prénom
     */
    private string $firstname;

    /**
     * @var string Nom
     */
    private string $lastname;

    /**
     * @var string Date de naissance
     */
    private string $birthdate;

    /**
     * Constructeur par ID
     *
     * @param int $id
     * @return Employee Employé
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
            $resourceData["firstname"],
            $resourceData["lastname"],
            $resourceData["birthdate"]
        );
    }

    /**
     * Constructeur
     *
     * @param int $id ID
     * @param string $firstname Prénom
     * @param string $lastname Nom
     * @param string $birthdate Date de naissance
     */
    private function __construct(int $id, string $firstname, string $lastname, string $birthdate) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthdate = $birthdate;
    }
}
