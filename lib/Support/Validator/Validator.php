<?php

namespace Lib\Support\Validator;

use Lib\Database\DB;
use Lib\Support\Validator\Contracts\ValidatorInterface;

/**
 * Class Validator
 *
 * Provides a simple data validation mechanism based on rules.
 * 
 * @CodeError 33
 */
class Validator implements ValidatorInterface
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

        method_exists($this, $ruleName) ?
            $this->$ruleName($field, $value, $params) :
            $this->addError($field, $this->getErrorMessage($ruleName, $field, ...$params));
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
     * Validate that a field is a string.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function string(string $field, $value)
    {
        if (!is_string($value)) {
            $this->addError($field, $this->getErrorMessage('string', $field));
        }
    }

    /**
     * Validate that a field is numeric.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function numeric(string $field, $value)
    {
        if (!is_numeric($value)) {
            $this->addError($field, $this->getErrorMessage('numeric', $field));
        }
    }

    /**
     * Validate that a field is a boolean.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function boolean(string $field, $value)
    {
        if (!is_bool($value)) {
            $this->addError($field, $this->getErrorMessage('boolean', $field));
        }
    }

    /**
     * Validate that a field is an integer.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function integer(string $field, $value)
    {
        if (!is_int($value)) {
            $this->addError($field, $this->getErrorMessage('integer', $field));
        }
    }

    /**
     * Validate that a field's value is greater than a specified value.
     *
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function gt(string $field, $value, array $params)
    {
        $threshold = isset($params[0]) ? $params[0] : null;

        if ($threshold !== null && $value <= $threshold) {
            $this->addError($field, $this->getErrorMessage('gt', $field, $threshold));
        }
    }

    /**
     * Validate that a field's value is less than a specified value.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function lt(string $field, $value, array $params)
    {
        $threshold = isset($params[0]) ? $params[0] : null;

        if ($threshold !== null && $value >= $threshold) {
            $this->addError($field, $this->getErrorMessage('lt', $field, $threshold));
        }
    }

    /**
     * Validate that a field's value is in a specified list of values.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function in(string $field, $value, array $params)
    {
        if (!in_array($value, $params)) {
            $this->addError($field, $this->getErrorMessage('in', $field, ...$params));
        }
    }

    /**
     * Validate that a field's value is not in a specified list of values.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function exists(string $field, $value, array $params)
    {
        if (DB::table($params[0])->where($params[1], $value)->exists()) {
            $this->addError($field, $this->getErrorMessage('exists', $field, ...$params));
        }
    }

    /**
     * Validate that a field's value is unique in a specified table.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     * @param array $params The validation parameters.
     */
    protected function unique(string $field, $value, array $params)
    {
        if (DB::table($params[0])->where($params[1], $value)->unique()) {
            $this->addError($field, $this->getErrorMessage('unique', $field, ...$params));
        }
    }

    /**
     * Validate that a field's value is a valid IP address.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function ip(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            $this->addError($field, $this->getErrorMessage('ip', $field));
        }
    }

    /**
     * Validate that a field's value is a valid URL.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function url(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, $this->getErrorMessage('url', $field));
        }
    }

    /**
     * Validate that a field's value is a valid IPv4 address.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function ipv4(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->addError($field, $this->getErrorMessage('ipv4', $field));
        }
    }

    /**
     * Validate that a field's value is a valid IPv6 address.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function ipv6(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->addError($field, $this->getErrorMessage('ipv6', $field));
        }
    }

    /**
     * Validate that a field's value is the same as another field.
     * 
     * @param string $field The field being validated.
     * @param mixed $value The field value.
     */
    protected function same(string $field, $value, array $params)
    {
        if ($value !== $params[0]) {
            $this->addError($field, $this->getErrorMessage('same', $field));
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

        switch ($rule) {
            case 'min':
            case 'max':
                $message = str_replace(':min', $params[0], $message);
                $message = str_replace(':max', $params[0], $message);
                break;

            case 'between':
                $message = str_replace(':min', $params[0], $message);
                $message = str_replace(':max', $params[1], $message);
                break;

            case 'gt':
            case 'lt':
                $message = str_replace(':threshold', $params[0], $message);
                break;

            case 'in':
                $message = str_replace(':values', implode(', ', $params), $message);
                break;

            case 'exists':
            case 'unique':
                $message = str_replace(':table', $params[0], $message);
                break;

            case 'same':
                $message = str_replace(':other', $params[0], $message);
                break;

            case 'ip':
            case 'url':
            case 'ipv4':
            case 'ipv6':
                break;

            default:
                break;
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
