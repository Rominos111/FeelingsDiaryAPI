<?php

/**
 * Type de réponse
 *
 * @author Romain
 * @license GPL-3.0-or-later
 */
abstract class ResponseType {
    /**
     * JSON
     */
    public const JSON = 0;

    /**
     * HTML
     *
     * @deprecated Pas encore implémenté
     */
    public const HTML = 1;

    /**
     * XML
     *
     * @deprecated Pas encore implémenté
     */
    public const XML = 2;

    /**
     * YAML
     *
     * @deprecated Pas encore implémenté
     */
    public const YAML = 3;
}
