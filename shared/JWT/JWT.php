<?php

require_once "__php__";

require_once "shared/YAML.php";
require_once "utils/random.php";

require_once "libs/JWT/Builder.php";
require_once "libs/JWT/Parser.php";
require_once "libs/JWT/Signer/Key.php";
require_once "libs/JWT/Signer/Hmac.php";
require_once "libs/JWT/Signer/Hmac/Sha256.php";

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use \Lcobucci\JWT\Token;

/**
 * Classe JWT pour rajouter une couche d'abstraction à la lib
 */
abstract class JWT {
    /**
     * @var int Type refresh token
     */
    public const TOKEN_REFRESH = 1;

    /**
     * @var int Type request token
     */
    public const TOKEN_REQUEST = 2;

    /**
     * @var Hmac Signer
     */
    private static Hmac $signer;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Initialisation
     */
    private static function init() : void {
        if (!isset(self::$signer)) {
            self::$signer = new Sha256();
        }
    }

    /**
     * Récupère la configuration JWT
     *
     * @return array Configuration JWT
     */
    private static function getConfig() : array {
        $config = YAML::loadFromFile($_ENV["ROOT_PATH"] . "config/jwt_config.yaml");
        $config["iss"] = empty($config["iss"]) ? $_SERVER["HTTP_HOST"] : $config["iss"];
        $config["aud"] = empty($config["aud"]) ? $_SERVER["HTTP_USER_AGENT"] : $config["aud"];
        $config["sub"] = empty($config["sub"]) ? $_SERVER["REMOTE_ADDR"] : $config["sub"];
        return $config;
    }

    /**
     * Récupère un objet Token
     *
     * @param string $token Token
     *
     * @return Token|null Objet Token, null si erreur de syntaxe
     */
    private static function getTokenObject(string $token) : ?Token {
        try {
            return (new Parser())->parse($token);
        }
        catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Récupère un token
     *
     * @param int $uid User ID
     * @param int $tokenType Type de token, refresh ou request
     * @return string Token
     *
     * @see JWT::TOKEN_REFRESH
     * @see JWT::TOKEN_REQUEST
     */
    public static function getToken(int $uid, int $tokenType = JWT::TOKEN_REQUEST) : string {
        self::init();

        $time = time();
        $jti = randomStr(16);
        $config = self::getConfig();

        if ($tokenType === self::TOKEN_REFRESH) {
            $key = new Key($config["refresh"]["key"]);
            $exp = $config["refresh"]["exp"];
        }
        else {
            $key = new Key($config["request"]["key"]);
            $exp = $config["request"]["exp"];
        }

        $token = (new Builder())
            ->issuedBy($config["iss"])
            ->permittedFor($config["aud"])
            ->relatedTo($config["sub"])
            ->identifiedBy($jti, true)
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($time + $exp)
            ->withClaim("uid", $uid)
            ->withClaim("type", $tokenType)
            ->getToken(self::$signer, $key);

        return (string) $token;
    }

    /**
     * Syntaxe valide ou non
     *
     * @param string $token Token
     *
     * @return bool Syntaxe valide ou non
     */
    public static function isTokenSyntaxValid(string $token) : bool {
        return !is_null(self::getTokenObject($token));
    }

    /**
     * Token périmé ou non
     *
     * @param string $token Token
     *
     * @return bool|null Token périmé ou non, null si erreur de syntaxe
     */
    public static function isTokenExpired(string $token) : ?bool {
        self::init();
        $token = self::getTokenObject($token);
        return is_null($token) ? null : $token->isExpired();
    }

    /**
     * IP du token conforme ou non
     *
     * @param string $token Token
     *
     * @return bool|null IP conforme ou non, null si erreur de syntaxe
     */
    public static function isTokenIPValid(string $token) : ?bool {
        self::init();
        $token = self::getTokenObject($token);
        $config = self::getConfig();

        if (is_null($token)) {
            return null;
        }
        else {
            return ($token->getClaim("sub") === $config["sub"]);
        }
    }

    /**
     * User Agent du token conforme ou non
     *
     * @param string $token Token
     *
     * @return bool|null User Agent conforme ou non, null si erreur de syntaxe
     */
    public static function isTokenUserAgentValid(string $token) : ?bool {
        self::init();
        $token = self::getTokenObject($token);
        $config = self::getConfig();

        if (is_null($token)) {
            return null;
        }
        else {
            return ($token->getClaim("aud") === $config["aud"]);
        }
    }

    /**
     * Signature conforme ou non
     *
     * @param string $token Token
     * @param int|null $type Type du token à vérifier, si null alors vérification selon le type du token
     *
     * @return bool|null Signature conforme ou non, null si erreur de syntaxe
     */
    public static function isTokenSignatureValid(string $token, ?int $type = null) : ?bool {
        self::init();
        $config = self::getConfig();
        $type = is_null($type) ? self::getTokenType($token) : $type;
        $key = new Key(($type === self::TOKEN_REFRESH) ? $config["refresh"]["key"] : $config["request"]["key"]);
        $token = self::getTokenObject($token);
        return is_null($token) ? null : $token->verify(self::$signer, $key);
    }

    /**
     * Token valide ou non
     *
     * @param string $token Token à vérifier
     * @param int|null $type Type du token à vérifier, si null alors vérification selon le type du token
     *
     * @return bool Token est valide ou non
     */
    public static function isTokenValid(string $token, ?int $type = null) : bool {
        return self::isTokenSyntaxValid($token)
            && self::isTokenIPValid($token)
            && self::isTokenUserAgentValid($token)
            && self::isTokenSignatureValid($token, $type)
            && !self::isTokenExpired($token);
    }

    /**
     * Récupère le type de token
     *
     * @param string $token Token
     * @return int Type de token
     *
     * @see JWT::TOKEN_REFRESH
     * @see JWT::TOKEN_REQUEST
     */
    public static function getTokenType(string $token) : int {
        return self::getTokenObject($token)->getClaim("type");
    }

    /**
     * Récupère l'Id de l'utilisateur dans un token
     *
     * @param string $token Token
     * @return int User ID
     */
    public static function getUserID(string $token) : int {
        return self::getTokenObject($token)->getClaim("uid");
    }
}
