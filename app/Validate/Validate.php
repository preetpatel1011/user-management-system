<?php

namespace App\Validate;

class Validate {

    private array $errors = [];

    /**
     * Summary of required
     * @param string $field
     * @param mixed $value
     * @param mixed $message
     * @return Validate
     */
    public function required(string $field, mixed $value, ?string $message = null): self
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = $message ?? "{$field} is required.";
        }
        return $this;
    }

    /**
     * Summary of name
     * @param string $field
     * @param string $value
     * @param mixed $message
     * @return Validate
     */
    public function name(string $field, string $value, ?string $message = null): self
    {
        if (!empty($value) && !preg_match("/^[a-zA-Z\s'\-]+$/u", $value)) {
            $this->errors[$field] = $message ?? "Name must only contain letters, spaces, hyphens, or apostrophes.";
        }
        return $this;
    }
    
    /**
     * Summary of email
     * @param string $field
     * @param string $value
     * @param mixed $message
     * @return Validate
     */
    public function email(string $field, string $value, ?string $message = null): self
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Enter a valid email address.";
        }
        return $this;
    }

    /**
     * Summary of minLength
     * @param string $field
     * @param string $value
     * @param int $minLength
     * @param mixed $message
     * @return Validate
     */
    public function minLength(string $field, string $value, int $minLength, ?string $message = null): self
    {
        if (!empty($value) && strlen($value) < $minLength) {
            $this->errors[$field] = $message ?? "{$field} must be at least {$minLength} characters.";
        }
        return $this;
    }

    /**
     * Summary of matches
     * @param string $field
     * @param mixed $value
     * @param mixed $matchValue
     * @param mixed $message
     * @return Validate
     */
    public function matches(string $field, mixed $value, mixed $matchValue, ?string $message = null): self
    {
        if ($value !== $matchValue) {
            $this->errors[$field] = $message ?? "{$field} do not match.";
        }
        return $this;
    }

    /**
     * Summary of passwordStrength
     * @param string $field
     * @param string $value
     * @param mixed $message
     * @return Validate
     */
    public function passwordStrength(string $field, string $value, ?string $message = null): self
    {
        if (!empty($value)) {
            $hasUppercase = preg_match('/[A-Z]/', $value);
            $hasLowercase = preg_match('/[a-z]/', $value);
            $hasNumber = preg_match('/[0-9]/', $value);
            $hasSpecial = preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?]/', $value);
            $hasMinLength = strlen($value) >= 8;

            if (!($hasUppercase && $hasLowercase && $hasNumber && $hasSpecial && $hasMinLength)) {
                $this->errors[$field] = $message ?? "{$field} must be at least 8 characters and contain uppercase, lowercase, number, and special character.";
            }
        }
        return $this;
    }

    /**
     * Summary of fails
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Summary of getFirstError
     */
    public function getFirstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}