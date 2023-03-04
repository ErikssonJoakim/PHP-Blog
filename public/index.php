<?php declare(strict_types = 1);

define('ROOT', str_replace(search:'/public',replace: '',subject: __DIR__));

require_once ROOT.'/vendor/autoload.php';

$app = new Blog\Foundation\App();

$app->render();