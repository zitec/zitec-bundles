<?php

namespace Zitec\ValidationBundle\Validator\ImageChecker;

/**
 * Image checker which relies on the GD extension.
 */
class GDImageChecker implements ImageCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($path)
    {
        // 'imagecreatefromstring' returns strange errors when a given file is not an image so it is better to not
        // rely on Symfony's error handler as it does some complex stuff which can result in a fatal error when
        // the analysed error is unusual.
        set_error_handler(function () {});
        $image = @imagecreatefromstring(file_get_contents($path));
        restore_error_handler();

        return is_resource($image);
    }

    /**
     * {@inheritdoc}
     */
    public static function isSupported()
    {
        return extension_loaded('gd');
    }
}
