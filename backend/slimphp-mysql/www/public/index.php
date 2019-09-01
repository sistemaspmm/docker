<?php
if (PHP_SAPI == "cli-server") {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER["REQUEST_URI"]);
    $file = __DIR__ . $url["path"];
    if (is_file($file)) {
        return false;
    }
}

/**
 * Verifica se o autoload do composer existe.
 */
if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    require_once __DIR__ . "/../vendor/autoload.php";
} else {
    die("ERROR: A pasta vendor não foi encontrada! aplicação abortada.");
}

session_start();

/**
 * Carrega o arquivo de configuração
 */
$settings = require __DIR__ . "/../config/settings.php";

/**
 * Carrega o container
 */
$container = new \Slim\Container($settings);

/**
 * Carrega configuração do banco de dados
 */
if (file_exists(__DIR__ . "/../config/database.php")) {
    $container["database"] = require __DIR__ . "/../config/database.php";
}

/**
 * Carrega configuração do banco de dados
 */
if (file_exists(__DIR__ . "/../config/mailer.php")) {
    $container["mailer"] = require __DIR__ . "/../config/mailer.php";
}

/**
 * Carrega as dependencias no container
 */
if (file_exists(__DIR__ . "/../config/dependencies.php")) {
    require_once __DIR__ . "/../config/dependencies.php";
}

foreach (glob(__DIR__ . "/../src/*/*/dependencies*.php") as $file) {
    if (file_exists($file)) require_once $file;
}

/**
 * Inicializa a instancia da aplicação
 */
$app = new \Slim\App($container);

// timezone
date_default_timezone_set($settings["settings"]["timezone"]);

/**
 * Carrega os middlewares
 */
if (file_exists(__DIR__ . "/../config/middleware.php")) {
    require_once __DIR__ . "/../config/middleware.php";
}
foreach (glob(__DIR__ . "/../src/*/*/middleware*.php") as $file) {
    if (file_exists($file)) require_once $file;
}

/**
 * Carrega as rotas
 */

if (file_exists(__DIR__ . "/../config/routes.php")) {
    require_once __DIR__ . "/../config/routes.php";
}

/*
 * Inicializa a aplicação
 */
$app->run();
