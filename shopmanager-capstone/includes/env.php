<?php
/**
 * includes/env.php
 * Reads key=value pairs from the project-root .env file.
 */

function loadEnv(string $path = __DIR__ . '/../.env'): void
{
    if (!is_file($path)) {
        $path = __DIR__ . '/../.env.example';
        if (!is_file($path)) return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;

        $pos = strpos($line, '=');
        if ($pos === false) continue;

        $key   = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        putenv("{$key}={$value}");
    }
}