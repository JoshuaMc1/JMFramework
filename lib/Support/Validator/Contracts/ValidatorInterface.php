<?php

namespace Lib\Support\Validator\Contracts;

interface ValidatorInterface
{
    public static function make(array $data = [], array $rules = []): array;

    public function validate(): array;

    public function setCustomErrorMessage(string $rule, string $message);

    public function getFormattedErrors(): array;

    public static function setCustomAttributes(array $attributes);
}
