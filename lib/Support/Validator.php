<?php

namespace Lib\Support;

class Validator
{
    protected $errors = [];
    protected $ruleErrorMessages = [
        'required' => 'The :attribute field is required.',
        'min' => 'The :attribute must be at least :min characters.',
        'max' => 'The :attribute must be at most :max characters.',
        'between' => 'The :attribute must be between :min and :max characters.',
        'email' => 'The :attribute must be a valid email address.',
        'image' => 'The :attribute must be an image.'
    ];

    public static function validate(array $data = [], array $rules = []): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $rulesList = explode('|', $fieldRules);

            foreach ($rulesList as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field][] = self::getErrorMessage('required', $field);
                }

                if (strpos($rule, 'min:') === 0) {
                    $minLength = intval(substr($rule, 4));
                    if (strlen($data[$field]) < $minLength) {
                        $errors[$field][] = self::getErrorMessage('min', $field, $minLength);
                    }
                }

                if (strpos($rule, 'max:') === 0) {
                    $maxLength = intval(substr($rule, 4));
                    if (strlen($data[$field]) > $maxLength) {
                        $errors[$field][] = self::getErrorMessage('max', $field, $maxLength);
                    }
                }

                if (strpos($rule, 'between:') === 0) {
                    $range = explode(',', substr($rule, 8));
                    $minLength = intval($range[0]);
                    $maxLength = intval($range[1]);
                    $length = strlen($data[$field]);
                    if ($length < $minLength || $length > $maxLength) {
                        $errors[$field][] = self::getErrorMessage('between', $field, $minLength, $maxLength);
                    }
                }

                if ($rule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = self::getErrorMessage('email', $field);
                }

                if ($rule === 'image' && !getimagesize($data[$field]['tmp_name'])) {
                    $errors[$field][] = self::getErrorMessage('image', $field);
                }
            }
        }

        return $errors;
    }

    protected static function getErrorMessage(string $rule, string $field, ...$params): string
    {
        $message = str_replace(':attribute', $field, self::$ruleErrorMessages[$rule]);
        return vsprintf($message, $params);
    }

    public static function addErrorMessage(string $rule, string $message)
    {
        static::$ruleErrorMessages[$rule] = $message;
    }

    public static function getFormattedErrors(array $errors): array
    {
        $formattedErrors = [];
        foreach ($errors as $field => $errorMessages) {
            foreach ($errorMessages as $errorMessage) {
                $formattedErrors[] = "{$field}: {$errorMessage}";
            }
        }
        return $formattedErrors;
    }
}
