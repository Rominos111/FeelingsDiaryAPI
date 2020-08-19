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
     * @var ResponseType Type de réponse
     * @see ResponseType
     */
    private ResponseType $responseType;

    /**
     * @var ResponseCode Code réponse
     */
    private ResponseCode $code;

    /**
     * Constructeur
     *
     * @param array $content Contenu de la réponse
     * @param ResponseType $responseType Type de réponse (JSON, XML...)
     * @param ResponseCode $code Code réponse (200, 401...)
     */
    public function __construct(array $content, ResponseType $responseType, ResponseCode $code) {
        $this->content = $content;
        $this->responseType = $responseType;
        $this->code = $code;
    }

    public function send() : void {
        http_response_code($this->code);

        switch ($this->responseType) {
            case ResponseType::JSON:
            default:
                echo($this->toJSON());
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
    private function toJSON() : string {
        $response = array("content" => $this->content);
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
     * @param array $content Contenu à ajouter
     */
    public function addContent(array $content) : void {
        array_push($this->content, $content);
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

        $this->addContent(array("missing" => $missing));
    }
}
