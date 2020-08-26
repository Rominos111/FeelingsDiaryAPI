<?php

/**
 * Réponse du serveur
 *
 * @author Romain
 * @author Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @license GPL-3.0-or-later
 * @see https://github.com/ythepaut/info406_serveur/blob/master/shared/libs/Response.php
 */
class Response {
    /**
     * @var array Contenu
     */
    private array $content;

    /**
     * @var int Type de réponse
     * @see ResponseType
     */
    private int $responseType;

    /**
     * @var int Code réponse
     */
    private int $code;

    /**
     * Constructeur
     *
     * @param array $content Contenu de la réponse
     * @param int $code Code réponse (200, 401...)
     * @param int $responseType Type de réponse (JSON, XML...)
     */
    public function __construct(array $content, int $code, int $responseType = ResponseType::JSON) {
        $this->content = $content;
        $this->code = $code;
        $this->responseType = $responseType;
    }

    /**
     * Envoie la réponse
     *
     * @param int $code Code d'erreur
     * @param string $message Message d'erreur
     */
    public function send(int $code = 0, string $message = "OK") : void {
        http_response_code($this->code);

        switch ($this->responseType) {
            case ResponseType::JSON:
            default:
                echo($this->toJSON($code, $message));
                break;

            case ResponseType::XML:
                echo($this->toXML());
                break;

            case ResponseType::HTML:
                echo($this->toHTML());
                break;
        }
    }

    /**
     * Convertit la réponse en JSON
     *
     * @return string JSON
     */
    private function toJSON(int $code, string $message) : string {
        $response = array();
        $response["errorCode"] = $code;
        $response["message"] = $message;
        $response["content"] = $this->content;
        return json_encode($response);
    }


    /**
     * TODO Convertit la réponse en XML
     *
     * @return string XML
     * @deprecated Pas encore implémenté
     */
    private function toXML() : string {
        return "";
    }

    /**
     * TODO Convertit la réponse en HTML
     *
     * @return string HTML
     * @deprecated Pas encore implémenté
     */
    private function toHTML() : string {
        return "";
    }

    /**
     * Ajoute du contenu dans la réponse
     *
     * @param string $name Nom de la catégorie à ajouter
     * @param array $content Contenu à ajouter
     */
    public function addContent(string $name, array $content) : void {
        $this->content[$name] = $content;
    }

    /**
     * Methode qui ajoute en contenu les arguments manquants de la requête
     *
     * @param array $required Liste des arguments nécessaires à la requête
     * @param array $given Liste des arguments fournis
     */
    public function addMissingArguments(array $required, array $given) : void {
        $missing = array();

        foreach ($required as $arg) {
            if (empty($given[$arg])) {
                array_push($missing, $arg);
            }
        }

        $this->addContent("missing", $missing);
    }
}
