<?php

require_once "__php__";

require_once "shared/YAML.php";

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
     * @var int Token valide
     */
    public const VALID_TOKEN = 0;

    /**
     * @var int Token invalide, clé invalide
     */
    public const INVALID_TOKEN_KEY = 1;

    /**
     * @var int Token invalide, user-agent différents
     */
    public const INVALID_TOKEN_USER_AGENT = 3;

    /**
     * @var int Token invalide, IP différentes
     */
    public const INVALID_TOKEN_IP = 2;

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
     * Si un token est invalide ou non
     *
     * @param string $token Token à vérifier
     * @param int|null $type Type du token à vérifier, si null alors vérification selon le type du token
     *
     * @return int Si le token est invalide ou non
     *
     * @see JWT::VALID_TOKEN
     * @see JWT::INVALID_TOKEN_USER_AGENT
     * @see JWT::INVALID_TOKEN_IP
     * @see JWT::INVALID_TOKEN_KEY
     */
    public static function isInvalidToken(string $token, ?int $type = null) : int {
        self::init();

        $config = self::getConfig();

        $type = is_null($type) ? self::getTokenType($token) : $type;
        $token = (new Parser())->parse($token);
        $key = new Key(($type === self::TOKEN_REFRESH) ? $config["refresh"]["key"] : $config["request"]["key"]);

        if ($token->verify(self::$signer, $key)) {
            if ($token->getClaim("sub") === $config["sub"]) {
                if ($token->getClaim("aud") === $config["aud"]) {
                    return self::VALID_TOKEN;
                }
                else {
                    return self::INVALID_TOKEN_USER_AGENT;
                }
            }
            else {
                return self::INVALID_TOKEN_IP;
            }
        }
        else {
            return self::INVALID_TOKEN_KEY;
        }
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
        $token = (new Parser())->parse($token);
        return $token->getClaim("type");
    }

    /**
     * Récupère l'Id de l'utilisateur dans un token
     *
     * @param string $token Token
     * @return int User ID
     */
    public static function getUserID(string $token) : int {
        $token = (new Parser())->parse($token);
        return $token->getClaim("uid");
    }
}
