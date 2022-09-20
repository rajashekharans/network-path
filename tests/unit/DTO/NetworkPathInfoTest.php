<?php

namespace NetworkPath\Tests\Unit\DTO;

use NetworkPath\DTO\AdjacentNetworkPathInfo;
use NetworkPath\DTO\NetworkPathInfo;
use PHPUnit\Framework\TestCase;

class NetworkPathInfoTest extends TestCase
{
    public function testGetSetNetworkPathInfoTest()
    {
        $adjacentNetWorkPathInfo = new AdjacentNetworkPathInfo();
        $adjacentNetWorkPathInfo->setDeviceFrom('A');
        $adjacentNetWorkPathInfo->setDeviceTo('B');
        $adjacentNetWorkPathInfo->setLatency(100);
        $adjacentNetWorkPathInfo->setVisited(true);
        $adjacentNetWorkPathInfo->setHashKey('testHashKey');

        $networkPathInfo = new NetworkPathInfo();
        $networkPathInfo->setDeviceFrom('A');
        $networkPathInfo->setDeviceTo($adjacentNetWorkPathInfo);

        $this->assertEquals('A', $networkPathInfo->getDeviceFrom());
        $this->assertEquals('A', $networkPathInfo->getDeviceTo()[0]->getDeviceFrom());
        $this->assertEquals('B', $networkPathInfo->getDeviceTo()[0]->getDeviceTo());
        $this->assertEquals(100, $networkPathInfo->getDeviceTo()[0]->getLatency());
        $this->assertEquals(true, $networkPathInfo->getDeviceTo()[0]->isVisited());
        $this->assertEquals('testHashKey', $networkPathInfo->getDeviceTo()[0]->getHashKey());
    }
}