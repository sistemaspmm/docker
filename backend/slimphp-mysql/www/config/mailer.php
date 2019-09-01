<?php
return [
	"smtp" => [
		"debug" => 0, // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
		"secure" => "tls", // padrão tls // ssl is depracated
		"auth" => true,

		// use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
		"host" => "", // webmail.manaus.am.gov.br", // ip 172.17.100.5
		"port" => "", // padrão 25, 587; // TLS only
		"username" => "",
		"password" => "",
		"from" => "",
		"from_name" => "",
	],
	"imap" => [],
	"pop3" => [],
	"options" => [
		"ssl" => [
			"verify_peer" => false,
			"verify_peer_name" => false,
			"allow_self_signed" => true
		],
	],

	"charset" => "UTF-8",
	"encoding" => "base64",
	"language" => "pt_br",

	"exceptions" => true, // PHPMailer enables exceptions
];
