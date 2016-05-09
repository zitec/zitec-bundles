<?php

namespace Zitec\ValidationBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Image;

/**
 * Constraint which extends the basic Symfony Image constraint and adds the possibility to check if an image is really
 * an image, as malicious users may modify the file headers and fool the MIME type check, which the base constraint
 * performs.
 * 
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class EnhancedImage extends Image
{
    const INVALID_IMAGE_ERROR = '557083e7-46fa-463b-b98a-289b2397024b';

    /**
     * {@inheritdoc}
     *
     * We also include the mapping from the base class.
     */
    protected static $errorNames = array(
        self::NOT_FOUND_ERROR => 'NOT_FOUND_ERROR',
        self::NOT_READABLE_ERROR => 'NOT_READABLE_ERROR',
        self::EMPTY_ERROR => 'EMPTY_ERROR',
        self::TOO_LARGE_ERROR => 'TOO_LARGE_ERROR',
        self::INVALID_MIME_TYPE_ERROR => 'INVALID_MIME_TYPE_ERROR',
        self::SIZE_NOT_DETECTED_ERROR => 'SIZE_NOT_DETECTED_ERROR',
        self::TOO_WIDE_ERROR => 'TOO_WIDE_ERROR',
        self::TOO_NARROW_ERROR => 'TOO_NARROW_ERROR',
        self::TOO_HIGH_ERROR => 'TOO_HIGH_ERROR',
        self::TOO_LOW_ERROR => 'TOO_LOW_ERROR',
        self::RATIO_TOO_BIG_ERROR => 'RATIO_TOO_BIG_ERROR',
        self::RATIO_TOO_SMALL_ERROR => 'RATIO_TOO_SMALL_ERROR',
        self::SQUARE_NOT_ALLOWED_ERROR => 'SQUARE_NOT_ALLOWED_ERROR',
        self::LANDSCAPE_NOT_ALLOWED_ERROR => 'LANDSCAPE_NOT_ALLOWED_ERROR',
        self::PORTRAIT_NOT_ALLOWED_ERROR => 'PORTRAIT_NOT_ALLOWED_ERROR',
        self::INVALID_IMAGE_ERROR => 'INVALID_IMAGE_ERROR',
    );

    public $checkImageValidity = false;

    public $checkImageValidityMessage = 'The file couldn\'t be recognized as an image.';
}
