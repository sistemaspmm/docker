<?php

$app->add(function ($request, $response, $next) {
	$response = $next($request, $response);
	return $response;
});

$trailing = function ($request, $response, $next) {
	$uri = $request->getUri();
	$path = $uri->getPath();
	if ($path != "/" && substr($path, -1) == "/") {
		// redirecionar permanentemente os caminhos com uma barra à sua contraparte que não está à direita
		$uri = $uri->withPath(substr($path, 0, -1));
		if ($request->getMethod() == "GET") {
			return $response->withRedirect((string)$uri, 301);
		} else {
			return $next($request->withUri($uri), $response);
		}
	}
	$response = $next($request, $response);
	return $response;
};
$app->add($trailing);

$app->add(new Clickjacking\Middleware\XFrameOptions());

$app->add(new Tuupola\Middleware\CorsMiddleware([
	"logger"         => $container["logger"],
	"origin"         => ["*"],
	"methods"        => ["GET", "POST", "PUT", "PATCH", "DELETE"],
	"headers.allow"  => ["X-Requested-With", "Content-Type", "Accept", "Origin", "Authorization", "Token"],
	// "origin.server"  => "https://example.com"
	"credentials"    => true,
	"cache"          => 60,
	"error"          => function ($request, $response, $arguments) {
		$data = [
			"code"    => 401,
			"return"  => false,
			"status"  => "error",
			"message" => $arguments["message"],
			"data"    => []
		];
		// return $container["response"]->withJson($data, $data["code"]);
		return $response->withJson($data, $data["code"]);
	}
]));
