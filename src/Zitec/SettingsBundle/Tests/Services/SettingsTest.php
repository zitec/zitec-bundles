<?php

namespace Tests\Zitec\SettingsBundle\Services;

use Doctrine\ORM\EntityManager;
use Zitec\SettingsBundle\Services\Settings;
use Zitec\SettingsBundle\Entity\Settings as SettingsEntity;
use Zitec\SettingsBundle\Entity\SettingsRepository;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $settingsRepoMock;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * Set up settings service.
     */
    protected function setUp()
    {
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $this->settingsRepoMock = $this->getMockBuilder(SettingsRepository::class)->disableOriginalConstructor()->getMock();

        $this->settings = new Settings($this->entityManagerMock);
    }

    /**
     * Test add function.
     */
    public function testGet()
    {
        $code = 'testCode';
        $value = 'testValue';

        $setting = $this->getMockBuilder(SettingsEntity::class)->disableOriginalConstructor()->getMock();
        $setting->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue($code));
        $setting->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($value));

        $settingsRepoMock = $this->getMockBuilder(SettingsRepository::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $settingsRepoMock->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($setting));


        $entityManagerMock = $this->getMockBuilder(EntityManager::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $entityManagerMock->expects($this->once())
                    ->method('getRepository')
                    ->will($this->returnValue($settingsRepoMock));


        $settings = new Settings($entityManagerMock);
        $this->assertEquals($setting->getValue(), $settings->get($code));
    }
}