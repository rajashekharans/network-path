<?php

namespace NetworkPath\Tests\Unit\DTO;

use NetworkPath\DTO\AdjacentNetworkPathInfo;
use PHPUnit\Framework\TestCase;

class AdjacentNetworkPathInfoTest extends TestCase
{
    public function testGetSetNetworkPathInfoTest()
    {
        $adjacentNetWorkPathInfo = new AdjacentNetworkPathInfo();
        $adjacentNetWorkPathInfo->setDeviceFrom('A');
        $adjacentNetWorkPathInfo->setDeviceTo('B');
        $adjacentNetWorkPathInfo->setLatency(100);
        $adjacentNetWorkPathInfo->setVisited(true);
        $adjacentNetWorkPathInfo->setHashKey('testHashKey');

        $this->assertEquals('A', $adjacentNetWorkPathInfo->getDeviceFrom());
        $this->assertEquals('B', $adjacentNetWorkPathInfo->getDeviceTo());
        $this->assertEquals(100, $adjacentNetWorkPathInfo->getLatency());
        $this->assertEquals(true, $adjacentNetWorkPathInfo->isVisited());
        $this->assertEquals('testHashKey', $adjacentNetWorkPathInfo->getHashKey());
    }
}