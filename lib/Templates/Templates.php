<?php

namespace Lib\Templates;

class Templates
{
    protected $templateFile;

    public function __construct()
    {
        $this->templateFile = __DIR__ . "/Errors/template.html";
    }

    public function render($data = [])
    {
        $templateContent = file_get_contents($this->templateFile);

        foreach ($data as $key => $value) {
            $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
        }

        http_response_code($data['ERROR_CODE']);
        echo $templateContent;
        die();
    }
}
