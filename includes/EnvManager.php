<?php

class EnvManager {
    /**
     * Load environment variables from .env file
     * 
     * @param string|null $path Custom path to .env file
     * @return bool True if .env was loaded successfully
     */
    public static function load($path = null) {
        $envPath = $path ?? __DIR__ . '/../.env';
        
        if (!file_exists($envPath)) {
            return false;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                if (!empty($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
        return true;
    }

    /**
     * Get environment variable with fallback
     * 
     * @param string $key Environment variable name
     * @param mixed $default Default value if not found
     * @return mixed Environment variable value or default
     */
    public static function get($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}