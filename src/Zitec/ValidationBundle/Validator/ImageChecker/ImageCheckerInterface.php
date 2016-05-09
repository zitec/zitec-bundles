<?php

namespace Zitec\ValidationBundle\Validator\ImageChecker;

/**
 * An interface all image checkers must implement. The image checker is a helper used to detect if a file is a valid
 * image.
 */
interface ImageCheckerInterface
{
    /**
     * Checks if the file located at the given path is a valid image.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isValid($path);

    /**
     * Determines if the checker is supported by current system.
     *
     * @return bool
     */
    public static function isSupported();
}
