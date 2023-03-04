<?php declare(strict_types = 1);

namespace Blog\Foundation;

class Internationalization {

    public const FLASH = '_flash';

   public static function getLocale(): string {
    $localeLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    return in_array($localeLanguage, ['fr', 'en']) ? $localeLanguage : 'en';
   }

   public static function getTranslation(string $subject, string $key): string {
    $validation = require sprintf('%s/resources/lang/%s/%s_%s.php', ROOT, $subject, $subject, static::getLocale());

    return $validation[$key] ?? '';    
   }
}