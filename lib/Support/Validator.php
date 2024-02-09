<?php

namespace Lib\Support;

/**
 * Class Validator
 *
 * Provides a simple data validation mechanism based on rules.
 */
class Validator
{
    /**
     * The data to validate.
     * 
     * @var array $data
     */
    protected $data;

    /**
     * The validation rules.
     * 
     * @var array $rules
     */
    protected $rules;

    /**
     * The array of validation errors.
     * 
     * @var array $errors
     */
    protected $errors = [];

    /**
     * The array of custom error messages.
     * 
     * @var array $customErrorMessages
     */
    protected $customErrorMessages = [];

    /**
     * The array of error messages for each rule.
     * 
     * @var array $ruleErrorMessages
     */
    protected static $ruleErrorMessages;

    /**
     * The array of custom attributes for error messages.
     * 
     * @var array $customAttributes
     */
    protected static $customAttributes;

    /**
     * Initialize the Validator class.
     * 
     * @param array $data The data to validate.
     * @param array $rules The validation rules to apply.
     */
    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        self::$ruleErrorMessages = require_once(lang_path() . '/' . config('app.locale') . '/validation.php');
        self::$customAttributes = self::$ruleErrorMessages['attributes'] ?? [];
    }

    /**
     * Create a new instance of the Validator class and perform validation.
     *
     * @param array $data The data to validate.
     * @param array $rules The validation rules to apply.
     * @return array The array of validation errors.
     */
    public static function make(array $data = [], array $rules = []): array
    {
        $validator = new Validator($data, $rules);
        return $validator->validate();
    }

    /**
     * Perform validation based on the provided rules.
     *
     * @return array The array of validation errors.
     */
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

    /**
     * Validate a specific rule for a given field.
     *
     * @param string $rule The validation rule.
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
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

    /**
     * Validate that a field is required (non-empty).
     *
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function required(string $field, $value)
    {
        if (empty($value)) {
            $this->addError($field, $this->getErrorMessage('required', $field));
        }
    }

    /**
     * Validate that a field has a minimum length.
     *
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function min(string $field, $value, array $params)
    {
        $minLength = isset($params[0]) ? intval($params[0]) : null;

        if ($minLength !== null && strlen($value) < $minLength) {
            $this->addError($field, $this->getErrorMessage('min', $field, $minLength));
        }
    }

    /**
     * Validate that a field has a maximum length.
     *
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function max(string $field, $value, array $params)
    {
        $maxLength = isset($params[0]) ? intval($params[0]) : null;

        if ($maxLength !== null && strlen($value) > $maxLength) {
            $this->addError($field, $this->getErrorMessage('max', $field, $maxLength));
        }
    }

    /**
     * Validate that a field's length is within a specific range.
     *
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function between(string $field, $value, array $params)
    {
        $minLength = isset($params[0]) ? intval($params[0]) : null;
        $maxLength = isset($params[1]) ? intval($params[1]) : null;
        $length = strlen($value);

        if (($minLength !== null && $length < $minLength) || ($maxLength !== null && $length > $maxLength)) {
            $this->addError($field, $this->getErrorMessage('between', $field, $minLength, $maxLength));
        }
    }

    /**
     * Validate that a field is a valid email address.
     *
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function email(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $this->getErrorMessage('email', $field));
        }
    }

    /**
     * Validate that a field contains an image.
     *
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function image(string $field, $value)
    {
        if (!empty($value['tmp_name']) && !getimagesize($value['tmp_name'])) {
            $this->addError($field, $this->getErrorMessage('image', $field));
        }
    }

    /**
     * Add an error message for a specific field.
     *
     * @param string $field The field being validated.
     * @param string $errorMessage The error message to add.
     */
    protected function addError(string $field, string $errorMessage)
    {
        $this->errors[$field][] = $errorMessage;
    }

    /**
     * Get the error message for a specific validation rule and field.
     *
     * @param string $rule The validation rule.
     * @param string $field The field being validated.
     * @param mixed ...$params The additional parameters for the error message.
     * @return string The formatted error message.
     */
    protected function getErrorMessage(string $rule, string $field, ...$params): string
    {
        $customMessage = $this->customErrorMessages[$rule] ?? null;
        $message = $customMessage ?: self::$ruleErrorMessages[$rule];
        $attributeName = $this->getAttributeName($field);

        $message = str_replace(':attribute', $attributeName, $message);

        if (isset($params[0])) {
            $message = str_replace(':min', $params[0], $message);
        }

        if (isset($params[1])) {
            $message = str_replace(':max', $params[1], $message);
        }

        return $message;
    }

    /**
     * Set a custom error message for a specific validation rule.
     *
     * @param string $rule The validation rule.
     * @param string $message The custom error message.
     */
    public function setCustomErrorMessage(string $rule, string $message)
    {
        $this->customErrorMessages[$rule] = $message;
    }

    /**
     * Get the formatted validation errors.
     *
     * @return array The array of formatted error messages.
     */
    public function getFormattedErrors(): array
    {
        $formattedErrors = [];

        foreach ($this->errors as $field => $errorMessages) {
            foreach ($errorMessages as $errorMessage) {
                $formattedErrors[] = "{$this->getAttributeName($field)}: {$errorMessage}";
            }
        }

        return $formattedErrors;
    }

    /**
     * Parse the validation rules into an array.
     *
     * @param mixed $rules The validation rules.
     * @return array The array of validation rules.
     */
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

    /**
     * Parse the validation rule name.
     *
     * @param string $rule The validation rule.
     * @return string The rule name.
     */
    protected function parseRuleName(string $rule): string
    {
        return strpos($rule, ':') !== false ? substr($rule, 0, strpos($rule, ':')) : $rule;
    }

    /**
     * Parse the validation rule parameters.
     *
     * @param string $rule The validation rule.
     * @return array The rule parameters.
     */
    protected function parseRuleParameters(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            $parameters = substr($rule, strpos($rule, ':') + 1);
            return explode(',', $parameters);
        }

        return [];
    }

    /**
     * Set custom attributes for error messages.
     *
     * @param array $attributes The array of custom attributes.
     */
    public static function setCustomAttributes(array $attributes)
    {
        self::$customAttributes = $attributes;
    }

    /**
     * Get the name of the attribute, considering custom names.
     *
     * @param string $field The field being validated.
     * @return string The attribute name.
     */
    protected function getAttributeName(string $field): string
    {
        return self::$customAttributes[$field] ?? $field;
    }
}
