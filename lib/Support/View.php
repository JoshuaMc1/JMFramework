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
        $loader = new FilesystemLoader(config('view.paths')[0]);

        $this->twig = new Environment($loader, [
            'cache' => config('view.compiled'),
            'debug' => config('view.debug'),
            'auto_reload' => config('view.auto_reload'),
            'autoescape' => config('view.autoescape'),
            'optimizations' => config('view.optimizations'),
            'strict_variables' => config('view.strict_variables'),
            'extensions' => config('view.extensions'),
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
                ->render($view . config('view.extensions')[0], $data);
        } catch (\Throwable $th) {
            ExceptionHandler::handleException(new CustomException(0101, lang('view_error'), $th->getMessage()));
        }
    }
}
