<?php declare(strict_types = 1);

namespace Blog\Foundation;

class Session {

    public const FLASH = '_flash';

    public const OLD = '_old';

    public const STATUS = '_status';

    public const ERRORS = '_errors';

    public static function init(): void {
        session_start();
    }

    public static function addValue(string $key, mixed $value, bool $isFlash = false): mixed {
        if($isFlash) {
            return $_SESSION[static::FLASH][$key] = $value;
        }

        return $_SESSION[$key] = $value;
    }

    public static function addFlash(string $key, mixed $value): mixed {
        return static::addValue($key, $value, true);       
    }
    
    public static function getValue(string $key, bool $isFlash = false): mixed {
        return $isFlash ? $_SESSION[static::FLASH][$key] ?? null : $_SESSION[$key] ?? null;
    }

    public static function getFlash(string $key): mixed {
        return static::getValue($key, true);       
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function resetFlash(): void {
        $_SESSION[static::FLASH] = [];
    }
}