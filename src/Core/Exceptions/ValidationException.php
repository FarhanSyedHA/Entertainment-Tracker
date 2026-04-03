<?php

namespace App\Core\Exceptions;

class ValidationException extends HttpException
{
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Validation failed', 422);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
