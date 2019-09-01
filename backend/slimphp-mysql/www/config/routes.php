<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->options("/{routes:.+}", function (Request $request, Response $response, array $args) {
	return $response;
});
// info
$app->get("/info[/{id:[0-9]+}]", function (Request $request, Response $response, array $args) {
	// Sample log message
	$this->logger->info("Slim '/info' route");

	/**
	 *
	 * Opções phpinfo()
	 * Nome (constant)		Valor 	Descrição
	 * INFO_GENERAL			1		A linha de configuração, localização do php.ini data de construção, Servidor Web, Sistema e mais.
	 * INFO_CREDITS			2		Créditos do PHP 4. Veja também phpcredits().
	 * INFO_CONFIGURATION	4		Valores locais e principais para as diretivas de configuração do PHP. Veja também ini_get().
	 * INFO_MODULES			8		Módulos carregados e suas respectivas configurações. Veja também get_loaded_modules().
	 * INFO_ENVIRONMENT		16		Informação das variáveis de ambiente que também esta disponível em $_ENV.
	 * INFO_VARIABLES		32		Mostra todas as variáveis pré-definidas de EGPCS (Environment, GET, POST, Cookie, Server).
	 * INFO_LICENSE			64		Informação sobre a Licença do PHP. Veja também o » faq sobre a licença.
	 * INFO_ALL				-1		Mostra tudo acima. Este é o valor padrão.
	 *
	 */

	// print_r($args);
	if (!is_array($args) || empty($args)) {
		phpinfo();
	} else {
		phpinfo($args["id"]);
	}
});
// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "/{routes:.+}", function (Request $request, Response $response, array $args) {
	$handler = $this->notFoundHandler; // handle using the default Slim page not found handler
	return $handler($request, $response);
});
