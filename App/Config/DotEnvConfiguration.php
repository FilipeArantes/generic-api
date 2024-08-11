<?php

namespace App\Config;

class DotEnvConfiguration
{
    public static function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception("O arquivo .env não foi encontrado: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            $value = trim($value, '"\'');

            putenv("{$key}={$value}"); // Define a variável de ambiente
            // $_ENV[$key] = $value; // Opcional: também define em $_ENV
            // $_SERVER[$key] = $value; // Opcional: também define em $_SERVER
        }
    }
}
