<?php

namespace Tests\Zitec\JSDataBundle\DataCollector;

use Zitec\JSDataBundle\DataCollector\DefaultDataCollector;

class DefaultDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test add function for default data collector.
     *
     * Test array key and array value.
     */
    public function testAdd()
    {
        $dataCollector = new DefaultDataCollector();

        $path = 'testPath';
        $value = 'testValue';

        $dataCollector->add("[$path]",$value);

        $this->assertArrayHasKey($path,$dataCollector->getAll());
        $this->assertEquals($value, $dataCollector->getAll()[$path]);
    }

    /**
     * Test merge function for default data collector.
     */
    public function testMerge()
    {
        $dataCollector = new DefaultDataCollector();
        $arrayIndex = 0;
        $arrayWithPaths = array('testFirstPath', 'testSecondPath');
        $arrayWithValues = array('testFirstValue', 'testSecondValue');

        $dataCollector->add("[$arrayWithPaths[$arrayIndex]]",$arrayWithValues[$arrayIndex]);
        $arrayIndex++;

        $dataCollector->merge(array($arrayWithPaths[$arrayIndex] => $arrayWithValues[$arrayIndex]));

        $result = $dataCollector->getAll();

        foreach($arrayWithPaths as $path){
            $this->assertArrayHasKey($path,$result);
        }

        foreach($arrayWithValues as $index => $value){
            $this->assertEquals($value, $result[$arrayWithPaths[$index]]);
        }
    }

    /**
     * Test get all function for default collector.
     * Test case:
     *      - add two element in data collector and test if exist in data collector.
     */
    public function testGetAll()
    {
        $dataCollector = new DefaultDataCollector();

        $arrayWithPaths = array('testFirstPath', 'testSecondPath');
        $arrayWithValues = array('testFirstValue', 'testSecondValue');

        foreach($arrayWithPaths as $index => $path){
            $dataCollector->add("[$path]",$arrayWithValues[$index]);
        }

        $result = $dataCollector->getAll();

        $this->assertEquals(count($arrayWithPaths), count($result));

        foreach($arrayWithPaths as $index => $path){
            $this->assertArrayHasKey($path,$result);
            $this->assertEquals($arrayWithValues[$index], $result[$arrayWithPaths[$index]]);
        }
    }
}