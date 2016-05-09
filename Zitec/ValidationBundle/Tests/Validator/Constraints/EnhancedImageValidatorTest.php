<?php

namespace Zitec\ValidationBundle\Tests\Validator\Constraints;

use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Zitec\ValidationBundle\Validator\Constraints\EnhancedImage;
use Zitec\ValidationBundle\Validator\Constraints\EnhancedImageValidator;
use Zitec\ValidationBundle\Validator\ImageChecker\ImagickImageChecker;

class EnhancedImageValidatorTest extends AbstractConstraintValidatorTest
{
    protected $image;
    protected $imageInvalid;
    protected $imageTiff;

    /**
     * {@inheritdoc}
     */
    protected function createValidator()
    {
        return new EnhancedImageValidator();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->image = __DIR__ . '/../../Fixtures/image.png';
        $this->imageInvalid = __DIR__ . '/../../Fixtures/image_invalid.jpg';
        $this->imageTiff = __DIR__ . '/../../Fixtures/image_tiff.tif';
    }

    public function testWithValidImage()
    {
        $constraint = new EnhancedImage([
            'checkImageValidity' => true,
            'checkImageValidityMessage' => 'testMessage',
        ]);

        $this->validator->validate($this->image, $constraint);

        $this->assertNoViolation();
    }

    public function testWithInvalidImage()
    {
        $constraint = new EnhancedImage([
            'checkImageValidity' => true,
            'checkImageValidityMessage' => 'testMessage',
        ]);

        $this->validator->validate($this->imageInvalid, $constraint);

        $this->buildViolation('testMessage')
            ->setCode(EnhancedImage::INVALID_IMAGE_ERROR)
            ->assertRaised();
    }

    public function testWithTiffImage()
    {
        $constraint = new EnhancedImage([
            'checkImageValidity' => true,
            'checkImageValidityMessage' => 'testMessage',
        ]);

        $this->validator->validate($this->imageTiff, $constraint);

        // Tiff images cannot be detected by GD, so we expect a validation error if Imagick is missing.
        if (ImagickImageChecker::isSupported()) {
            $this->assertNoViolation();
        } else {
            $this->buildViolation('testMessage')
                ->setCode(EnhancedImage::INVALID_IMAGE_ERROR)
                ->assertRaised();
        }
    }

    public function testFileNotFound()
    {
        // Check that the logic from parent validator still works.
        $constraint = new EnhancedImage([
            'notFoundMessage' => 'testMessage',
        ]);

        $this->validator->validate('test', $constraint);

        $this->buildViolation('testMessage')
            ->setParameter('{{ file }}', '"test"')
            ->setCode(EnhancedImage::NOT_FOUND_ERROR)
            ->assertRaised();
    }
}
