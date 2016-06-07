<?php

namespace Zitec\ValidationBundle\Tests\Mocks;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Represents a mock implementation of a service to which the validation will be delegated from the ServiceCallback
 * constraint validator.
 */
class ValidatorServiceMock
{
    protected $raiseError;

    protected $errorMessage;

    public function __construct($raiseError = true, $errorMessage = '')
    {
        $this->raiseError = $raiseError;
        $this->errorMessage = $errorMessage;
    }

    protected function performValidation($value, ExecutionContextInterface $context)
    {
        if (false === $this->raiseError) {
            return;
        }

        $context->addViolation($this->errorMessage);
    }

    public function validate($value, ExecutionContextInterface $context)
    {
        $this->performValidation($value, $context);
    }

    public function __invoke($value, ExecutionContextInterface $context)
    {
        $this->performValidation($value, $context);
    }
}
