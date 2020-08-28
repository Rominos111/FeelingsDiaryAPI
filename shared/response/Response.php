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
    private ?array $payload = null;

    /**
     * @var array|null Paramètres manquants
     */
    private ?array $missing = null;

    /**
     * @var int Type de réponse (JSON, XML...)
     * @see ResponseType
     */
    private int $responseType = ResponseType::JSON;

    /**
     * @var int Code réponse HTTP (200, 401...)
     * @see ResponseCode
     */
    private int $httpCode = ResponseCode::OK;

    /**
     * @var int Code d'erreur custom
     */
    private int $customCode = 0;

    /**
     * @var string Message d'erreur
     */
    private string $message = "OK";

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Builder
     *
     * @return self Réponse
     */
    public static function builder() : self {
        return new self();
    }

    /**
     * Constructeur
     */
    public function __construct() {}

    /**
     * Set le code réponse HTTP
     *
     * @param int $httpCode Code réponse (200, 401...)
     * @param bool $editCustomCode Édite aussi le code custom ou non
     *
     * @return $this
     */
    public function setHttpCode(int $httpCode, bool $editCustomCode = true) : self {
        $this->httpCode = $httpCode;

        if ($editCustomCode && $this->customCode === 0 && intdiv($httpCode, 100) !== 2) {
            $this->customCode = 1;
        }

        return $this;
    }

    /**
     * Set le contenu de la réponse
     *
     * @param array $payload Contenu de la réponse
     *
     * @return $this This
     */
    public function setPayload(array $payload) : self {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Set le message
     *
     * @param string $message Message d'erreur
     *
     * @return $this This
     */
    public function setMessage(string $message) : self {
        $this->message = $message;
        return $this;
    }

    /**
     * Set le code d'erreur custom
     *
     * @param int $customCode Code d'erreur
     * @return $this
     */
    public function setCustomCode(int $customCode) : self {
        $this->customCode = $customCode;
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Envoie la réponse
     *
     * @param bool $exitAfter Si le programme doit se terminer ensuite ou non
     */
    public function send(bool $exitAfter = true) : void {
        http_response_code($this->httpCode);

        $response = array();
        $response["errorCode"] = $this->customCode;
        $response["message"] = $this->message;
        $response["payload"] = is_null($this->payload) ? "" : $this->payload;

        if (!is_null($this->missing)) {
            $response["missing"] = $this->missing;
        }

        switch ($this->responseType) {
            case ResponseType::JSON:
            default:
                echo($this->toJSON($response));
                break;

            case ResponseType::XML:
            case ResponseType::HTML:
            case ResponseType::YAML:
                break;

        }

        if ($exitAfter) {
            exit();
        }
    }

    /**
     * Convertit la réponse en JSON
     *
     * @param array $response Réponse à envoyer
     *
     * @return string JSON
     */
    private function toJSON(array $response) : string {
        return json_encode($response);
    }

    /**
     * Ajoute du contenu dans la réponse
     *
     * @param string $name Nom de la catégorie à ajouter
     * @param array $content Contenu à ajouter
     *
     * @return self This
     */
    public function addContent(string $name, array $content) : self {
        $this->payload[$name] = $content;
        return $this;
    }

    /**
     * Lorsqu'un ou plusieurs arguments manquent
     *
     * @param array $missing Arguments manquants
     *
     * @return Response Réponse
     */
    public static function missingArgumentsFromArray(array $missing) : self {
        return self::missingArguments(...$missing);
    }

    /**
     * Lorsqu'un ou plusieurs arguments manquent
     *
     * @param mixed ...$missing Arguments manquants
     *
     * @return Response Réponse
     */
    public static function missingArguments(...$missing) : self {
        return self::builder()
            ->setHttpCode(ResponseCode::UNPROCESSABLE_ENTITY)
            ->setMessage("Missing arguments")
            ->setPayload(array("missing" => $missing));
    }

    /**
     * Type d'argument invalide
     *
     * @param string $varName Nom de la variable
     * @param mixed $var Variable invalide
     * @param mixed $required Exemple d'un type valide (ex: "", 0, array()...)
     *
     * @return static Réponse
     */
    public static function wrongDataType(string $varName, $var, $required) : self {
        return self::builder()
            ->setHttpCode(ResponseCode::UNPROCESSABLE_ENTITY)
            ->setMessage("Wrong data types")
            ->setPayload(array(
                "received" => array(
                    $varName => gettype($var)
                ),
                "expected" => array(
                    $varName => gettype($required)
                )
            ));
    }
}
