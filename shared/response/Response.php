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
     * @var array|null Contenu
     */
    private ?array $content;

    /**
     * @var array|null Paramètres manquants
     */
    private ?array $missing;

    /**
     * @var int Type de réponse
     * @see ResponseType
     */
    private int $responseType;

    /**
     * @var int Code réponse HTTP (200, 401...)
     * @see ResponseCode
     */
    private int $httpCode;

    /**
     * @var int Code d'erreur custom
     */
    private int $customCode;

    /**
     * @var string Message d'erreur
     */
    private string $message;

    /**
     * Constructeur
     *
     * @param int $httpCode Code réponse (200, 401...)
     * @param array|null $content Contenu de la réponse
     * @param int $customCode Code d'erreur
     * @param string $message Message d'erreur
     * @param int $responseType Type de réponse (JSON, XML...)
     */
    public function __construct(int $httpCode = ResponseCode::OK,
                                ?array $content = null,
                                int $customCode = 0,
                                string $message = "OK",
                                int $responseType = ResponseType::JSON) {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->customCode = $customCode;
        $this->message = $message;
        $this->responseType = $responseType;
    }

    /**
     * Envoie la réponse
     *
     * @param bool $exitAfter Si le programme doit se terminer ensuite ou non
     */
    public function send(bool $exitAfter = true) : void {
        http_response_code($this->httpCode);

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

        if ($exitAfter) {
            exit();
        }
    }

    /**
     * Convertit la réponse en JSON
     *
     * @return string JSON
     */
    private function toJSON() : string {
        $response = array();
        $response["errorCode"] = $this->customCode;
        $response["message"] = $this->message;
        $response["content"] = is_null($this->content) ? "" : $this->content;

        if (isset($this->missing)) {
            $response["missing"] = $this->missing;
        }

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

    public static function missingArgumentsFromArray(array $missing) : self {
        return self::missingArguments(...$missing);
    }

    public static function missingArguments(...$missing) : self {
        $response = new Response(ResponseCode::UNPROCESSABLE_ENTITY);
        $response->missing = $missing;
        $response->message = "Missing arguments";
        $response->customCode = 1;
        return $response;
    }
}
