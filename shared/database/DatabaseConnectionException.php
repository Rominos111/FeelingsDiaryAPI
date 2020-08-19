<?php

/**
 * Exception lors d'une connextion à une BDD
 *
 * @author Romain
 * @author Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @license GPL-3.0-or-later
 * @see https://github.com/ythepaut/info406_serveur/blob/master/shared/exceptions/DatabaseConnectionException.php
 */
class DatabaseConnectionException extends Exception {
    /**
     * Constructeur
     *
     * @param string|null $message Message
     * @param int $code Code
     */
    public function __construct(string $message = null, int $code = 0) {
        if ($message === null) {
            $message = "Database connection failed.";
        }

        parent::__construct($message, $code);
    }

    /**
     * Représentation en string de l'exception
     *
     * @return string Exception en string
     */
    public function __toString() : string {
        return get_class($this) . " '" . $this->getMessage() . "' in " . $this->getFile() . "(" . $this->getLine() . ")\n" . $this->getTraceAsString();
    }
}
