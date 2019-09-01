<?php

// monolog
$container["logger"] = function ($container) {
	$settings = $container->get("settings")["logger"];
	$logger = new \Monolog\Logger($settings["name"]);
	$logger->pushProcessor(new \Monolog\Processor\UidProcessor());
	// $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings["path"], $settings["level"]));
	/**
	 * Serviço de Logging em Arquivo
	 */
	$stream = new \Monolog\Handler\StreamHandler($settings["path"], $settings["level"]);
	$logger->pushHandler(new \Monolog\Handler\FingersCrossedHandler($stream, Monolog\Logger::INFO));
	return $logger;
};

$container["secretkey"] = $container->get("settings")["secretKey"];
$container["tokenExpires"] = $container->get("settings")["tokenExpires"];
$container["phpErrorHandler"] = function ($container) {
	return $container["errorHandler"];
};
$container["errorHandler"] = function ($container) {
	return function ($request, $response, $exception) use ($container) {
		$msg = $exception->getMessage();
		$code = $exception->getCode();
		$statusCode = (!is_integer($code) || $code < 100 || $code > 599) ? 500 : $code;

		// if (is_string($code)) {
		$msg = $msg . " File: " . $exception->getFile() . ", Line: " . $exception->getLine() . ", Code errorHandler: " . $exception->getCode();
		// }

		$data = [
			"code"    => $statusCode,
			"method"  => $request->getMethod(),
			"return"  => false,
			"status"  => "error",
			"message" => "Erro! " . $msg,
			"date"    => date("Y-m-d H:i:s"),
			"data"    => []
		];
		$container["logger"]->error($data["message"], $data);
		return $container["response"]->withJson($data, $data["code"]);
	};
};

$container["notFoundHandler"] = function ($container) {
	return function ($request, $response) use ($container) {
		$data = [
			"code"    => 404,
			"method"  => $request->getMethod(),
			"return"  => false,
			"status"  => "fail",
			"message" => "Página não encontrada!",
			"date"    => date("Y-m-d H:i:s"),
			"data"    => []
		];
		$container["logger"]->warning($data["message"], $data);
		return $container["response"]->withJson($data, $data["code"]);
		// return $container["response"]->withJson($data, 200);
	};
};

$container["notAllowedHandler"] = function ($container) {
	return function ($request, $response, $methods) use ($container) {
		$data = [
			"code"    => 405,
			"method"  => $request->getMethod(),
			"return"  => false,
			"status"  => "fail",
			"message" => "Método não permitido; O método deve ser um dos seguintes: " . implode(", ", $methods),
			"date"    => date("Y-m-d H:i:s"),
			"data"    => []
		];
		$container["logger"]->warning($data["message"], $data);
		return $container["response"]->withJson($data, $data["code"])
			->withHeader("Allow", implode(", ", $methods))
			->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
	};
};

$container["pdo"] = function ($container) {
	// Database Driver MySQL, PostgreSQL & Oracle
	$configs = $container->get("database");
	$opt = [
		\PDO::ATTR_ERRMODE => $configs["errmode"]["exception"],
		\PDO::ATTR_CASE => $configs["case"]["lower"],
		\PDO::ATTR_DEFAULT_FETCH_MODE => $configs["fetch"]["assoc"],
		\PDO::ATTR_ORACLE_NULLS => $configs["nulls"]["to_string"],
		\PDO::ATTR_PERSISTENT => false,
		\PDO::ATTR_EMULATE_PREPARES => false,
		// \PDO::ATTR_STRINGIFY_FETCHES => true,
		// \PDO::ATTR_AUTOCOMMIT => true,
	];

	$driver = $configs["default"];

	if (in_array(gethostname(), ["productionServer"])) {
		$db = $configs[$driver]["production"];
		$usr = $db["username"];
		$pwd = $db["password"];
	} else if (in_array(gethostname(), ["ratifyServer"])) {
		$db = $configs[$driver]["ratify"];
		$usr = $db["username"];
		$pwd = $db["password"];
	} else {
		$db = $configs[$driver]["development"];
		$usr = $db["username"];
		$pwd = $db["password"];
	}

	if ($driver == "oracle") {
		$conf = "(SDU=4096)(SEND_BUF_SIZE=11784)(RECV_BUF_SIZE=11784)";
		$tns = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=" . $db["host"] . ")(PORT=" . $db["port"] . "))(CONNECT_DATA=(SERVICE_NAME=" . $db["dbname"] . ")(SERVER=DEDICATED)))";
		$dsn = $db["driver"] . ":dbname=" . $tns . ";charset=" . $db["charset"];
		// $pdo = new \PDO($dsn, $usr, $pwd, $opt);
		// $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd, $opt);
		$pdo = new \FaaPz\PDO\Database($dsn, $usr, $pwd, $opt);
	} else if ($driver == "mysql") {
		$dsn = $db["driver"] . ":host=" . $db["host"] . ";dbname=" . $db["dbname"] . ";charset=" . $db["charset"];
		// $pdo = new \PDO($dsn, $usr, $pwd, $opt);
		// $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd, $opt);
		$pdo = new \FaaPz\PDO\Database($dsn, $usr, $pwd, $opt);
	}

	return $pdo;
};

$container["mail"] = function ($container) {
	$configs = $container->get("mailer");

	$mail = new PHPMailer\PHPMailer\PHPMailer($configs["exceptions"]);
	$mail->CharSet = $configs["charset"];
	$mail->Encoding = $configs["encoding"];
	$mail->setLanguage($configs["language"]);

	//Server settings
	$mail->isSMTP();
	$mail->SMTPDebug = $configs["smtp"]["debug"];
	$mail->SMTPOptions = $configs["options"];
	$mail->SMTPSecure = $configs["smtp"]["secure"];
	$mail->SMTPAuth = $configs["smtp"]["auth"];
	$mail->Host = $configs["smtp"]["host"];
	$mail->Port = $configs["smtp"]["port"];
	$mail->Username = $configs["smtp"]["username"];
	$mail->Password = $configs["smtp"]["password"];
	$mail->setFrom($configs["smtp"]["from"], $configs["smtp"]["from_name"]);

	return $mail;
};
