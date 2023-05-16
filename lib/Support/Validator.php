<?php

namespace Lib\Support;

class Validator
{
    protected $data;
    protected $rules;
    protected $errors = [];
    protected $customErrorMessages = [];
    protected static $ruleErrorMessages = [
        'required' => 'The :attribute field is required.',
        'min' => 'The :attribute must be at least :min characters.',
        'max' => 'The :attribute must be at most :max characters.',
        'between' => 'The :attribute must be between :min and :max characters.',
        'email' => 'The :attribute must be a valid email address.',
        'image' => 'The :attribute must be an image.'
    ];

    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data = [], array $rules = []): array
    {
        $validator = new Validator($data, $rules);
        return $validator->validate();
    }

    public function validate(): array
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $rulesList = $this->parseRules($fieldRules);

            foreach ($rulesList as $rule) {
                $this->validateRule($rule, $field, $value);
            }
        }

        return $this->errors;
    }

    protected function validateRule(string $rule, string $field, $value)
    {
        $ruleName = $this->parseRuleName($rule);
        $params = $this->parseRuleParameters($rule);

        if (method_exists($this, $ruleName)) {
            $this->$ruleName($field, $value, $params);
        } else {
            $this->addError($field, $this->getErrorMessage($ruleName, $field, ...$params));
        }
    }

    protected function required(string $field, $value)
    {
        if (empty($value)) {
            $this->addError($field, $this->getErrorMessage('required', $field));
        }
    }

    protected function min(string $field, $value, array $params)
    {
        $minLength = isset($params[0]) ? intval($params[0]) : null;

        if ($minLength !== null && strlen($value) < $minLength) {
            $this->addError($field, $this->getErrorMessage('min', $field, $minLength));
        }
    }

    protected function max(string $field, $value, array $params)
    {
        $maxLength = isset($params[0]) ? intval($params[0]) : null;

        if ($maxLength !== null && strlen($value) > $maxLength) {
            $this->addError($field, $this->getErrorMessage('max', $field, $maxLength));
        }
    }

    protected function between(string $field, $value, array $params)
    {
        $minLength = isset($params[0]) ? intval($params[0]) : null;
        $maxLength = isset($params[1]) ? intval($params[1]) : null;
        $length = strlen($value);

        if (($minLength !== null && $length < $minLength) || ($maxLength !== null && $length > $maxLength)) {
            $this->addError($field, $this->getErrorMessage('between', $field, $minLength, $maxLength));
        }
    }

    protected function email(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $this->getErrorMessage('email', $field));
        }
    }

    protected function image(string $field, $value)
    {
        if (!empty($value['tmp_name']) && !getimagesize($value['tmp_name'])) {
            $this->addError($field, $this->getErrorMessage('image', $field));
        }
    }

    protected function addError(string $field, string $errorMessage)
    {
        $this->errors[$field][] = $errorMessage;
    }

    protected function getErrorMessage(string $rule, string $field, ...$params): string
    {
        $customMessage = $this->customErrorMessages[$rule] ?? null;
        $message = $customMessage ?: self::$ruleErrorMessages[$rule];
        $message = str_replace(':attribute', $field, $message);

        if (isset($params[0])) {
            $message = str_replace(':min', $params[0], $message);
        }

        if (isset($params[1])) {
            $message = str_replace(':max', $params[1], $message);
        }

        return $message;
    }

    public function setCustomErrorMessage(string $rule, string $message)
    {
        $this->customErrorMessages[$rule] = $message;
    }

    public function getFormattedErrors(): array
    {
        $formattedErrors = [];

        foreach ($this->errors as $field => $errorMessages) {
            foreach ($errorMessages as $errorMessage) {
                $formattedErrors[] = "{$field}: {$errorMessage}";
            }
        }

        return $formattedErrors;
    }

    protected function parseRules($rules)
    {
        if (is_array($rules)) {
            return $rules;
        }

        if (is_string($rules)) {
            return explode('|', $rules);
        }

        return [];
    }

    protected function parseRuleName(string $rule): string
    {
        return strpos($rule, ':') !== false ? substr($rule, 0, strpos($rule, ':')) : $rule;
    }

    protected function parseRuleParameters(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            $parameters = substr($rule, strpos($rule, ':') + 1);
            return explode(',', $parameters);
        }

        return [];
    }
}
