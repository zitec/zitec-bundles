<?php

namespace Zitec\ValidationBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Constraint which delegates the actual validation to a service.
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY", "METHOD", "ANNOTATION"})
 */
class ServiceCallback extends Constraint
{
    /**
     * The id of the service to which the validation will get delegated. The user will either specify a service
     * that can be directly invoked or a method of the service which will get called.
     *
     * @var string
     */
    public $serviceId;

    /**
     * The method of the above specified service which will perform the validation.
     *
     * @var string|null
     */
    public $method;

    /**
     * The constraint constructor.
     *
     * @param mixed|null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        // Validate this constraint's specific options.
        if (null === $this->serviceId) {
            throw new MissingOptionsException(
                sprintf(
                    'You should provide the id of the service which will perform the validation for constraint %s!',
                    __CLASS__
                ),
                ['serviceId']
            );
        }
        if (!is_string($this->serviceId) || (null !== $this->method && !is_string($this->method))) {
            throw new InvalidOptionsException(
                sprintf(
                    'The "serviceId" and "method" (if provided) options of constraint %s must have string values!',
                    __CLASS__
                ),
                ['serviceId', 'method']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
