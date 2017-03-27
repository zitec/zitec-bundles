<?php

namespace Zitec\TranslationBundle\Tests\FileVisitor;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use PHPUnit\Framework\TestCase;
use Zitec\TranslationBundle\FileVisitor\JSFileVisitor;

class JSFileVisitorTest extends TestCase
{
    const JS_TRANSLATION_DOMAIN = 'js';

    const JS_FILE = __DIR__.'/../Fixtures/translatable.js';

    /**
     * @var JSFileVisitor
     */
    private $jsFileVisitor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->jsFileVisitor = new JSFileVisitor(self::JS_TRANSLATION_DOMAIN);
    }

    public function testVisitFile()
    {
        $messageCatalogue = new MessageCatalogue();

        $this->jsFileVisitor->visitFile(new \SplFileInfo(self::JS_FILE), $messageCatalogue);

        /** @var Message[] $messages */
        $messages = $messageCatalogue->getDomain(self::JS_TRANSLATION_DOMAIN)->all();
        self::assertCount(2, $messages);
        self::assertArrayHasKey('test_js', $messages);
        self::assertArrayHasKey('{1} test_js |]1,Inf[ test_js %count%', $messages);
    }
}
