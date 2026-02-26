<?php
/**
 * includes/env.php
 * Reads key=value pairs from the project-root .env file
 * and exposes them via getenv().  Call once at bootstrap.
 */

function loadEnv(string $path = __DIR__ . '/../../.env'): void
{
    if (!is_file($path)) {
        // Fall back to .env.example so the app can still start in dev
        $path = __DIR__ . '/../.env.example';
        if (!is_file($path)) return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments and empty lines
        if ($line === '' || $line[0] === '#') continue;

        // Split on the FIRST '=' only (values may contain '=')
        $pos = strpos($line, '=');
        if ($pos === false) continue;

        $key   = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        // Strip surrounding quotes if present
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') ||
                ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // Only set if not already defined (real env vars take priority)
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
        }
    }
}
