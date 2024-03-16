<?php

namespace Lib\Templates;

/**
 * Class Templates
 *
 * Provides a simple mechanism for rendering error templates.
 */
class Templates
{
    /**
     * The path to the error template file.
     */
    protected $templateFile;

    /**
     * Initialize the file path to the error template.
     */
    public function __construct()
    {
        $this->templateFile = __DIR__ . "/Errors/template.html";
    }

    /**
     * Render the error template with the provided data.
     *
     * @param array $data The data to replace placeholders in the template.
     * 
     * @return void
     */
    public function render($data = []): void
    {
        $templateContent = file_get_contents($this->templateFile);

        foreach ($data as $key => $value) {
            $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
        }

        ($data['ERROR_CODE'] instanceof int) ?
            http_response_code($data['ERROR_CODE']) :
            http_response_code(500);

        echo $templateContent;
        die();
    }
}
