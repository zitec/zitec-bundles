<?php

namespace Zitec\ValidationBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Zitec\ValidationBundle\Validator\ImageChecker\ImageChecker;

/**
 * The EnhancedImage constraint validator.
 */
class EnhancedImageValidator extends ImageValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EnhancedImage) {
            throw new UnexpectedTypeException($constraint, EnhancedImage::class);
        }

        $violations = count($this->context->getViolations());

        parent::validate($value, $constraint);

        $failed = count($this->context->getViolations()) !== $violations;

        if ($failed || null === $value || '' === $value) {
            return;
        }

        if (empty($constraint->checkImageValidity)) {
            return;
        }

        $imagePath = $value instanceof File ? $value->getPathname() : (string) $value;
        if (ImageChecker::getInstance()->isValid($imagePath)) {
            return;
        }

        if ($this->context instanceof ExecutionContextInterface) {
            $this->context->buildViolation($constraint->checkImageValidityMessage)
                ->setCode(EnhancedImage::INVALID_IMAGE_ERROR)
                ->addViolation();
        } else {
            $this->buildViolation($constraint->checkImageValidityMessage)
                ->setCode(EnhancedImage::INVALID_IMAGE_ERROR)
                ->addViolation();
        }
    }
}
