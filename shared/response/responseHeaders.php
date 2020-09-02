<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Headers généraux

// Authentification nécessaire, Bearer = via token, realm = description
// TODO:
// header("WWW-Authenticate: Bearer, charset='UTF-8'");

// TODO: header "Age:"
// TODO: Ajouter un age + gestion public / privé + storable ou non
header("Cache-Control: no-cache, must-revalidate");
// TODO: header "Expires:"
// Header générique de cache
header("Pragma: no-cache");

// TODO: header "ETag:" + header "If-Match:"

// Garde la connexion ouverte une fois terminée pour d'éventuelles autres requêtes
header("Connection: keep-alive");
// Garde la connexion pour au moins 5 secondes et pour au plus 100 requêtes par connexion
header("Keep-Alive: timeout=5, max=100");

// Headers HTTP autorisés
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization");
// Le client ne doit accepter que depuis ce serveur
header("Access-Control-Allow-Origin: " . $_SERVER["SERVER_NAME"]);
// Cache maximal
header("Access-Control-Max-Age: 3600");
// Header set plus tard
header("Access-Control-Allow-Methods: *");
// Origine
header("Origin: " . (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]);

// Tracking, '!" : under construction, 'N' : not tracking, 'T' : tracking
header("Tk: !");

// Langage du contenu
header("Content-Language: en");
// Type de contenu
header("Content-Type: application/json; charset=UTF-8");

// Host
header("Host: " . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]);
// TODO: header "User-Agent:"

// Méthodes autorisées
header("Allow: *");

// Protège des failles XSS et autres en n'autorisant que depuis ce serveur. Variante : "default-src 'self'"
header("Content-Security-Policy: default-src " . $_SERVER["SERVER_NAME"]);
// Le client ne doit accepter que depuis ce serveur
header("Cross-Origin-Resource-Policy: same-site");
// Force l'utilisation de HTTPS pendant encore 2 ans et bloque HTTP
header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload");
// Le type de contenu est bien le bon même si le type MIME ne correspond pas
header("X-Content-Type-Options: nosniff");
// Refuse l'intégration dans des iframe
header("X-Frame-Options: DENY");
// Bloque toutes les requêtes XSS
header("X-XSS-Protection: 1; mode=block");



assert(!is_null($_ENV["EXPECTED"]));
assert(!is_null($_ENV["EXPECTED"]["methods"]));

$methods = "";

if (is_array($_ENV["EXPECTED"]["methods"])) {
    foreach ($_ENV["EXPECTED"]["methods"] as $value) {
        $methods .= $value . ", ";
    }
}
else if (is_string($_ENV["EXPECTED"]["methods"])) {
    $ok = ($_ENV["EXPECTED"]["methods"] === $_SERVER["REQUEST_METHOD"]);
    $methods .= $_ENV["EXPECTED"]["methods"] . ", ";
}

$methods = substr($methods, 0, -2);

// Méthodes autorisées
header("Allow: $methods", true);
// Méthodes autorisées
header("Access-Control-Allow-Methods: $methods", true);
