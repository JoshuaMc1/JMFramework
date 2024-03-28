<?php

namespace Lib\Support;

use Lib\Exception\{
    CustomException,
    ExceptionHandler
};
use Lib\Extensions\GlobalFunctionsExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class View
 * 
 * Provides methods to render views using the Twigs template engine.
 * 
 * @CodeError 01
 */
class View
{
    protected $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(config('view.paths'));

        $this->twig = new Environment($loader, [
            'cache' => config('view.compiled'),
            'auto_reload' => config('view.auto_reload', true),
            'autoescape' => config('view.autoescape', false),
            'optimizations' => config('view.optimizations', -1),
            'extensions' => config('view.extensions', ['.twig']),
            'charset' => config('view.charset', 'utf-8'),
        ]);

        $this->twig->addExtension(new GlobalFunctionsExtension());
    }

    /**
     * Renders a view using the Twigs template engine.
     *
     * @param string $view The name of the view file.
     * @param array $data Data to be passed to the view.
     *
     * @return string Rendered HTML.
     */
    public function render(string $view, array $data = []): string
    {
        try {
            return $this->twig
                ->render(str_replace('.', DIRECTORY_SEPARATOR, $view) . config('view.extensions')[0], $data);
        } catch (\Throwable $th) {
            ExceptionHandler::handleException(new CustomException(0101, lang('exception.view_error'), $th->getMessage()));
        }
    }
}
