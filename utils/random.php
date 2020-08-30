<?php

/**
 * @param int $length Taille demandée
 * @return string|null Renvoie une chaine de caractères aléatoires,
 *                     null s'il n'existe aucune source d'entropie (comme /dev/urandom)
 */
function randomStr(int $length) : ?string {
    try {
        return bin2hex(random_bytes(ceil($length/2)));
    }
    catch (Exception $e) {
        return null;
    }
}
