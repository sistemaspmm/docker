<?php
// force docker
$_ENV['docker'] = true;

return [
    "settings" => [
        "displayErrorDetails" => true,
        "addContentLengthHeader" => true,
        "determineRouteBeforeAppMiddleware" => true,
        "logger" => [
            "name" => "slim-app",
            "path" => isset($_ENV["docker"]) ? "php://stdout" : __DIR__ . "/../logs/app.log",
            "level" => \Monolog\Logger::DEBUG,
        ],
        "timezone" => "America/Manaus",
        "secretKey" => "cbe5faf8c437db0c918c5c32f619f7b2",
        "tokenExpires" => 360,
    ],
];
