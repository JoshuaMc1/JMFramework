<?php

namespace Lib\Templates;

/**
 * Class Templates
 *
 * Provides a simple mechanism for rendering error templates.
 */
class Templates
{
    protected $templateFile;

    public function __construct()
    {
        // Initialize the template file path.
        $this->templateFile = __DIR__ . "/Errors/template.html";
    }

    /**
     * Render the error template with the provided data.
     *
     * @param array $data The data to replace placeholders in the template.
     */
    public function render($data = [])
    {
        // Read the content of the template file.
        $templateContent = file_get_contents($this->templateFile);

        // Replace placeholders in the template with provided data.
        foreach ($data as $key => $value) {
            $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
        }

        // Set the HTTP response code based on the provided error code.
        http_response_code($data['ERROR_CODE']);

        // Output the rendered template content and terminate the script.
        echo $templateContent;
        die();
    }
}
