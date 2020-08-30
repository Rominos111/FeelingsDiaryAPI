<?php

/**
 * Code réponse
 *
 * @author Romain
 * @license GPL-3.0-or-later
 */
abstract class ResponseCode {
    /**
     * Continue
     */
    public const CONTINUE = 100;

    /**
     * Si une requête est trop lente, permet d'éviter au client de time-out
     */
    public const PROCESSING = 102;

    /**
     * Réponse précédent le message HTTP final
     */
    public const EARLY_HINTS = 103;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * OK
     */
    public const OK = 200;

    /**
     * Ressource créée
     */
    public const CREATED = 201;

    /**
     * Requête acceptée mais traitement non terminé
     */
    public const ACCEPTED = 202;

    /**
     * 200_OK mais il n'y a rien à répondre
     */
    public const NO_CONTENT = 204;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Plusieurs choix
     */
    public const MULTIPLE_CHOICES = 300;

    /**
     * Déplacement permanent
     */
    public const MOVED_PERMANENTLY = 301;

    /**
     * Déplacement temporaire
     */
    public const MOVED_TEMPORARILY = 302;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Requête malformée, syntaxe incorrecte
     */
    public const BAD_REQUEST = 400;

    /**
     * Requête non autorisée, doit être recommencée (jeton périmé)
     */
    public const UNAUTHORIZED = 401;

    /**
     * Requête interdite, ne doit pas être recommencée
     */
    public const FORBIDDEN = 403;

    /**
     * Non trouvé
     */
    public const NOT_FOUND = 404;

    /**
     * Méthode non autorisée (utilisation de PATCH sur une ressource read-only par exemple)
     */
    public const METHOD_NOT_ALLOWED = 405;

    /**
     * Le client n'a pas répondu assez rapidement
     */
    public const REQUEST_TIMEOUT = 408;

    /**
     * Conflit entre plusieurs requêtes simultanées par exemple
     */
    public const CONFLICT = 409;

    /**
     * La ressource existait mais n'existe plus
     */
    public const GONE = 410;

    /**
     * Charge trop volumineuse
     */
    public const PAYLOAD_TOO_LARGE = 413;

    /**
     * Type de contenu incompris
     */
    public const UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * Requête valide mais condition supplémentaire invalide (argument manquant, mot de passe trop court...)
     */
    public const UNPROCESSABLE_ENTITY = 422;

    /**
     * Trop de requêtes
     */
    public const TOO_MANY_REQUESTS = 429;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Erreur interne
     */
    public const INTERNAL_SERVER_ERROR = 500;

    /**
     * Fonction non implémentée
     */
    public const NOT_IMPLEMENTED = 501;

    /**
     * Service indisponible
     */
    public const SERVICE_UNAVAILABLE = 503;

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Récupère le nom d'une variable selon le code
     *
     * @param int $code Code
     *
     * @return string Nom de la variable associée
     */
    public static function getCodeName(int $code) : string {
        // Réflexion
        $vars = (new ReflectionClass(self::class))->getConstants();

        foreach ($vars as $key => $value) {
            if ($value === $code) {
                return $key;
            }
        }

        return "";
    }
}
