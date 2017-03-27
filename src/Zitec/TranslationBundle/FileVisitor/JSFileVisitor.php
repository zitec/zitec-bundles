<?php

namespace Zitec\TranslationBundle\FileVisitor;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Extracts translatable strings from JS and adds them in the translation catalogue. We consider all the strings passed
 * as the first argument to "trans" or "transChoice" methods to be translatable. We expect that in JS a translator
 * object will be used for translating and that object will implement the "trans" and "transChoice" methods of the PHP
 * TranslatorInterface.
 *
 * @see TranslatorInterface
 */
class JSFileVisitor implements FileVisitorInterface
{
    /**
     * The default translation domain used in JS.
     *
     * @var string
     */
    private $defaultJsTranslationDomain;

    /**
     * The file visitor constructor.
     *
     * @param string $defaultJsTranslationDomain
     */
    public function __construct($defaultJsTranslationDomain)
    {
        $this->defaultJsTranslationDomain = $defaultJsTranslationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue)
    {
        if ('.js' !== substr($file, -3)) {
            return;
        }

        $content = file_get_contents($file);
        if (empty($content)) {
            return;
        }

        preg_match_all('/\.(?:trans|transChoice)\s*\(\s*(?:"|\')(.*?)(?:"|\')/u', $content, $matches);
        foreach ($matches[1] as $match) {
            $message = new Message($match, $this->defaultJsTranslationDomain);
            $catalogue->add($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast)
    {
    }
}
