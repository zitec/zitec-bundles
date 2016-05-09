<?php

namespace Zitec\ValidationBundle\Validator\ImageChecker;

/**
 * An image checker which acts as an aggregator. Basically, simple image checkers get attached to it and when checking
 * if a file is an image, it will consult all the registered checkers.
 */
class ImageChecker implements ImageCheckerInterface
{
    /**
     * The unique singleton instance.
     *
     * @var static
     */
    private static $instance;

    /**
     * The registered image checkers.
     *
     * @var ImageCheckerInterface[]
     */
    protected $imageCheckers = [];

    /**
     * The image checker constructor. It registers the default image checkers.
     */
    protected function __construct()
    {
        if (GDImageChecker::isSupported()) {
            $this->register(new GDImageChecker());
        }
        if (ImagickImageChecker::isSupported()) {
            $this->register(new ImagickImageChecker());
        }
    }

    /**
     * The unique instance fetcher.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Registers a new image checker. New image checkers have priority over the old ones.
     *
     * @param ImageCheckerInterface $imageChecker
     */
    public function register(ImageCheckerInterface $imageChecker)
    {
        array_unshift($this->imageCheckers, $imageChecker);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($path)
    {
        if (empty($this->imageCheckers)) {
            throw new \RuntimeException(
                'No image checker available! You must have at least one of the PHP image processing extensions '
                . '(GD or Imagick) enabled or you must register your own image checker.'
            );
        }

        // We perform an "optimistic" check. This is because some extensions, such as GD2, know to work with
        // a small number of image formats, so the checker corresponding to GD2 may report a false positive.
        // Therefore, we run through all checkers before making a final decision.
        foreach ($this->imageCheckers as $imageChecker) {
            if ($imageChecker->isValid($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function isSupported()
    {
        return true;
    }
}
