<?php

namespace Zitec\ValidationBundle\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * The ServiceCallback constraint's validator.
 */
class ServiceCallbackValidator extends ConstraintValidator
{
    /**
     * The application's service container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The validator constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ServiceCallback) {
            throw new UnexpectedTypeException($constraint, ServiceCallback::class);
        }

        if (!$this->container->has($constraint->serviceId)) {
            throw new ConstraintDefinitionException(sprintf(
                'Couldn\'t identify the service "%s" targeted by the ServiceCallback constraint!',
                $constraint->serviceId
            ));
        }

        // The validation will be either performed by a method of the targeted service or directly by the service,
        // by invocation.
        $validator = $this->container->get($constraint->serviceId);
        if (null !== $constraint->method) {
            $validator = [$validator, $constraint->method];
            if (!is_callable($validator)) {
                throw new ConstraintDefinitionException(sprintf(
                    'The method "%s" of service "%s" targeted by the ServiceCallback constraint isn\'t callable!',
                    $constraint->method,
                    $constraint->serviceId
                ));
            }

            call_user_func($validator, $value, $this->context);
        } else {
            if (!is_callable($validator)) {
                throw new ConstraintDefinitionException(sprintf(
                    'The service "%s" targeted by the ServiceCallback constraint isn\'t invokable!',
                    $constraint->serviceId
                ));
            }

            call_user_func($validator, $value, $this->context);
        }
    }
}
