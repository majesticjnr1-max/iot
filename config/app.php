<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!function_exists('env')) {
	function env(string $key, $default = null)
	{
		if (array_key_exists($key, $_ENV)) {
			return $_ENV[$key];
		}

		$value = getenv($key);
		return $value === false ? $default : $value;
	}
}
