<?php

namespace Tests\Zitec\SettingsBundle\DataCollector;

use Zitec\SettingsBundle\DataCollector\SettingsDataCollector;
use Zitec\SettingsBundle\Entity\Settings;

class SettingsDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test add function.
     * Add new settings in data collector.
     */
    public function testAdd()
    {
        $setting = new Settings();
        $settingsCollector = new SettingsDataCollector();

        $testCode = 'test.settings';
        $testName = 'Test settings';
        $testDescription = 'Test description settings';
        $testValue = 'value';

        $setting->setCode($testCode);
        $setting->setName($testName);
        $setting->setDescription($testDescription);
        $setting->setValue($testValue);

        $settingsCollector->add($setting);

        $result = $settingsCollector->getAll();

        $this->assertArrayHasKey($testCode,$result);
        $this->assertEquals($setting,$result[$testCode]);
    }

    public function testGetAll()
    {
        $settingsCollector = new SettingsDataCollector();

        $index = 5;
        $testCode = 'test.settings';
        $testName = 'Test settings';
        $testDescription = 'Test description settings';
        $testValue = 'value';
        $settings = array();

        for($i = 0; $i <= $index; $i++){
            $setting = new Settings();
            $setting->setCode($testCode.$i);
            $setting->setName($testName.$i);
            $setting->setDescription($testDescription.$i);
            $setting->setValue($testValue.$i);
            $settingsCollector->add($setting);
            array_push($settings, $setting);
        }

        $results = $settingsCollector->getAll();
        
        foreach($results as $index => $setting){
            $this->assertArrayHasKey($index,$results);
            $this->assertEquals($setting,$results[$index]);
        }
    }
}