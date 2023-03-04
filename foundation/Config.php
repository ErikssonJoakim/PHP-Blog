<?php declare(strict_types = 1);

namespace Blog\Foundation;

class Config {
    public static function get(string $config): mixed {
        [$file, $key] = static::getFileAndKey(config: $config);
        $path = static::getPath($file);
        $config = require $path;

        return $config[$key];
    }

    protected static function getFileAndKey(string $config): array {
        if(!preg_match(pattern: '/^[a-z_]+\.[a-z_]+$/i', subject: $config)) {
            throw new \InvalidArgumentException(
                message: sprintf('Bad format (%s instead of file.key. Only letters and _ are accepted)', $config)
            );
        }
        
        return explode(separator: '.', string: $config);
    }

    protected static function getPath(string $file): string {
        $path = sprintf('%s/config/%s.php', ROOT, $file);

        if(!file_exists(filename: $path)) {
            throw new \InvalidArgumentException(message: 'The config file does not exist');
        }

        return $path;
    }
}