<?php

namespace Tests\Zitec\JSDataBundle\DataCollector;

use Zitec\JSDataBundle\DataCollector\DefaultDataCollector;

class DefaultDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $dataCollector = new DefaultDataCollector();

        $path = 'testPath';
        $value = 'testValue';

        $dataCollector->add("[path]",$value);

//        $result = $dataCollector->getAll();

        $this->arrayHasKey($path);
    }
}