<?php declare(strict_types = 1);

namespace Blog\Foundation;

use Illuminate\Database\Capsule\Manager as Capsule;
use Valitron\Validator as ValitronValidator;
use Blog\Foundation\Authentication;
use Blog\Foundation\Internationalization as Language;

class Validator {
    public static function get(array $data): ValitronValidator {
        $language = Language::getLocale();
        $validationPath = sprintf('%s/resources/lang/validation/validation_%s.php', ROOT, $language);

        $validator = new ValitronValidator(data: $data, lang: $language);
        $validator->labels(labels: require $validationPath);
        
        static::addCustomRules($validator);
        
        return $validator;
    }

    protected static function addCustomRules(ValitronValidator $validator): void {
        $validator->addRule(name: 'unique', callback: function(string $field, mixed $value, array $params, array $fields): bool {
            return !Capsule::table(table: $params[1])->where(column: $params[0], operator: $value)->exists();
        }, message: sprintf('{field} %s', Language::getTranslation(subject: 'validation' ,key: 'invalid')));

        $validator->addRule(name: 'password', callback: function(string $field, mixed $value, array $params, array $fields): bool {
            $user = Authentication::getUser();
            return password_verify(password: $value, hash: $user->password);

        }, message: sprintf('{field} %s', Language::getTranslation(subject: 'validation' ,key: 'wrong')));
        
        $validator->addRule('required_file', function (string $field, mixed $value, array $params, array $fields) {
            return isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK;
        }, sprintf('{field} %s', Language::getTranslation(subject: 'validation' ,key: 'required')));

        $validator->addRule('image', function (string $field, mixed $value, array $params, array $fields) {
            return isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK 
            ? str_starts_with($_FILES[$field]['type'], 'image/') 
            : false;
        }, sprintf('{field} %s', Language::getTranslation(subject: 'validation' ,key: 'image')));

        $validator->addRule('squared_image', function (string $field, mixed $value, array $params, array $fields) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                [$width, $height] = getimagesize($_FILES[$field]['tmp_name']);
                return $width === $height;
            }
            return false;
        }, sprintf('{field} %s', Language::getTranslation(subject: 'validation' ,key: 'squared')));
    }
}