<?php

namespace Zitec\ValidationBundle\Validator\ImageChecker;

/**
 * Image checker which relies on the Imagick extension.
 */
class ImagickImageChecker implements ImageCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($path)
    {
        try {
            new \Imagick($path);

            return true;
        } catch (\ImagickException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function isSupported()
    {
        return extension_loaded('imagick');
    }
}
