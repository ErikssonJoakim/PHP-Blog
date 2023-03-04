<?php declare(strict_types = 1);

namespace Blog\Foundation;

use Blog\Foundation\Config;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;

class View {
    public static function render(string $view, array $data = []): void {
        $view = str_replace(search: '.', replace: '/', subject: $view);

        if(!static::viewExists(view: $view)) {
            throw new \InvalidArgumentException(
                message: sprintf("The view %s doesn't exists", $view));
        }

        $twig = static::initTwig();
        echo $twig->render(
            name: sprintf( '%s.%s', $view, Config::get(config: 'twig.template_extension')),
            context: $data
        );
    }

    protected static function viewExists(string $view): bool {
        return file_exists(
            sprintf('%s/resources/views/%s.%s', 
            ROOT, 
            $view, 
            Config::get(config: 'twig.template_extension')
            )
        );
    }

    protected static function initTwig(): Environment {
        $loader = new FilesystemLoader(paths: ROOT.'/resources/views');
        $twig = new Environment(loader: $loader, options: [
            'cache' => ROOT.'/cache/twig',
            'auto_reload' => true
        ]);

        foreach (Config::get(config: 'twig.functions') as $function) {
            $twig->addFunction(function: new TwigFunction(name: $function, callable: $function));
        }

        return $twig;
    }

    public static function csrfField(): string {
        return sprintf('<input type="hidden" name="_token" value="%s">', 
               Session::getValue('_token'));
    }

    public static function methodField(string $httpMethod): string {
        return sprintf('<input type="hidden" name="_method" value="%s">', 
               $httpMethod);
    }

    public static function oldValues(string $key, mixed $default = null): mixed {
        $old = Session::getFlash(key: Session::OLD);

        return $old[$key] ?? $default;
    }
}
