<?php

namespace App\Validation;

use Psr\Http\Message\UploadedFileInterface;

class Validator
{
    private const MIME_TYPE = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf'
    ];

    /**
     * @var array
     */
    private $params;

    /**
     * @var string[]
     */
    private $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Check if the element is in the array (capture empty fields)
     * @param string ...$keys
     * @return Validator
     */
    public function required(...$keys): self
    {
        if (is_array($keys[0])) {
            $keys=$keys[0];
        }
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * Check that the field is not empty
     * @param string ...$keys
     * @return Validator
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    /**
     * Check that the field is empty (honeypot)
     * @param string $key
     * @return Validator
     */
    public function mustBeEmpty(string $key): self
    {
        $value = $this->getValue($key);
        if (!empty($value)) {
            $this->addError($key, 'must_be_empty');
        }
        return $this;
    }

    /**
     * Check text size with parameters
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return Validator
     */
    public function textLength(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) &&
            $length < $min
        ) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) &&
            $length > $max
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }
        return $this;
    }

    /**
     * Check if the value is a valid slug
     * @param string $key
     * @return Validator
     */
    public function isSlug(string $key): self
    {
        $value = $this->getValue($key);

        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $this->params[$key])) {
            $this->addError($key, 'slug');
        }
        return $this;
    }


    /**
     * Check that the element is numeric
     * @param string $key
     * @return Validator
     */
    public function isNumeric(string $key):self
    {
        $value = $this->getValue($key);
        if (!empty($value)) {
            if (!is_numeric($value)) {
                $this->addError($key, 'numeric');
            }
        }
        return $this;
    }

    /**
     * Check if the element is numeric and it is in the defined interval
     * @param string $key
     * @param int $min
     * @param int $max
     * @return Validator
     */
    public function numericRange(string $key, int $min, int $max):self
    {
        $value = $this->getValue($key);
        if (!empty($value)) {
            if (!is_numeric($value) || $value < $min) {
                $this->addError($key, 'numeric_range', [$min, $max]);
            } elseif (!is_numeric($value) || $value > $max) {
                $this->addError($key, 'numeric_range', [$min, $max]);
            }
        }
        return $this;
    }

    /**
     * Check if the full date has the correct format (MYSQL freindly)
     * @param string $key
     * @param string $format
     * @return Validator
     */
    public function isDateTime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    /**
     * Check if the short date has the correct format (MYSQL freindly)
     * @param string $key
     * @param string $format
     * @return Validator
     */
    public function isDate(string $key, string $format = 'Y-m-d'): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'date', [$format]);
        }
        return $this;
    }

    /**
     * Allow an empty date, otherwise a date in the correct format (MYSQL freindly
     * @param string $key
     * @param string $format
     * @return Validator
     */
    public function emptyOrIsDate(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        if ($value === '') {
            return $this;
        }
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'date', [$format]);
        }
        return $this;
    }

    /**
     * Check if the file's extension is in the table of allowed extensions
     * @param string $key
     * @param array $extensions
     * @return Validator
     */
    public function extension(string $key, array $extensions): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPE[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addError($key, 'filetype', [join(', ', $extensions)]);
            }
        }
        return $this;
    }


    /**
     * Check if the file has been uploaded
     * @param string $key
     * @return Validator
     */
    public function uploaded(string $key): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if (is_string($file)) {
            return $this;
        }
        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }

    /**
     * Checks the validity of an email address
     * @param string $key
     * @return Validator
     */
    public function email(string $key): self
    {
        $value = $this->getValue($key);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($key, 'email');
        }
        return $this;
    }

    /**
     * Check if 2 fields have the same value (ex: confirm password)
     * @param string $key
     * @return $this
     */
    public function confirm(string $key): self
    {
        $value = $this->getValue($key);
        $valueConfirm = $this->getValue($key . '_confirm');
        if ($valueConfirm !== $value) {
            $this->addError($key, 'confirm');
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * @param string $message
     * @return array
     */
    public function sendSuccess(string $message): array
    {
        return ['success' => $message];
    }

    /**
     * Add error
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    /**
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}