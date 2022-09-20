<?php

namespace NetworkPath\Tests\Unit;

use NetworkPath\Collection\NetworkPathInfoCollection;
use NetworkPath\DTO\AdjacentNetworkPathInfo;
use NetworkPath\DTO\NetworkPathInfo;
use PHPUnit\Framework\TestCase;

class NetworkPathInfoCollectionTest extends TestCase
{
    public function testAddToCollection()
    {
        $adjacentNetworkPathInfoAB = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoAB->setDeviceFrom('A');
        $adjacentNetworkPathInfoAB->setDeviceTo('B');
        $adjacentNetworkPathInfoAB->setLatency(10);

        $adjacentNetworkPathInfoAC = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoAC->setDeviceFrom('A');
        $adjacentNetworkPathInfoAC->setDeviceTo('C');
        $adjacentNetworkPathInfoAC->setLatency(20);

        $networkPathInfoDeviceA = new NetworkPathInfo();
        $networkPathInfoDeviceA->setDeviceFrom('A');
        $networkPathInfoDeviceA->setDeviceTo($adjacentNetworkPathInfoAB);
        $networkPathInfoDeviceA->setDeviceTo($adjacentNetworkPathInfoAC);

        $networkPathInfoCollection = new NetworkPathInfoCollection();
        $networkPathInfoCollection->add($networkPathInfoDeviceA);

        $actualResult = $networkPathInfoCollection->getCollection();
        $this->assertEquals('A', $actualResult[0]->getDeviceFrom());
        $this->assertEquals('A', $actualResult[0]->getDeviceTo()[0]->getDeviceFrom());
        $this->assertEquals('B', $actualResult[0]->getDeviceTo()[0]->getDeviceTo());
        $this->assertEquals(10, $actualResult[0]->getDeviceTo()[0]->getLatency());
        $this->assertEquals('A', $actualResult[0]->getDeviceTo()[1]->getDeviceFrom());
        $this->assertEquals('C', $actualResult[0]->getDeviceTo()[1]->getDeviceTo());
        $this->assertEquals(20, $actualResult[0]->getDeviceTo()[1]->getLatency());

    }

    public function testfindDeviceFrom()
    {
        $networkPathInfoCollection = $this->getNetworkPathInfoCollection();
        $actualResult = $networkPathInfoCollection->findDeviceFrom('A');

        $this->assertEquals('A', $actualResult->getDeviceFrom());
        $this->assertEquals('A', $actualResult->getDeviceTo()[0]->getDeviceFrom());
        $this->assertEquals('B', $actualResult->getDeviceTo()[0]->getDeviceTo());
        $this->assertEquals(10, $actualResult->getDeviceTo()[0]->getLatency());
        $this->assertEquals('A', $actualResult->getDeviceTo()[1]->getDeviceFrom());
        $this->assertEquals('C', $actualResult->getDeviceTo()[1]->getDeviceTo());
        $this->assertEquals(20, $actualResult->getDeviceTo()[1]->getLatency());
    }

    public function testAddToAdjacentNodeHash()
    {
        $adjacentNetworkPathInfoAB = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoAB->setDeviceFrom('A');
        $adjacentNetworkPathInfoAB->setDeviceTo('B');
        $adjacentNetworkPathInfoAB->setLatency(10);

        $networkPathInfoCollection = new NetworkPathInfoCollection();
        $networkPathInfoCollection->addToAdjacentNodeHash('testHashKey', $adjacentNetworkPathInfoAB);

        $actualResult = $networkPathInfoCollection->getAdjacentNodeInfoFromHash('testHashKey');

        $this->assertEquals('A', $actualResult->getDeviceFrom());
        $this->assertEquals('B', $actualResult->getDeviceTo());
        $this->assertEquals(10, $actualResult->getLatency());
    }

    public function getNetworkPathInfoCollection()
    {
        $adjacentNetworkPathInfoAB = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoAB->setDeviceFrom('A');
        $adjacentNetworkPathInfoAB->setDeviceTo('B');
        $adjacentNetworkPathInfoAB->setLatency(10);

        $adjacentNetworkPathInfoAC = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoAC->setDeviceFrom('A');
        $adjacentNetworkPathInfoAC->setDeviceTo('C');
        $adjacentNetworkPathInfoAC->setLatency(20);

        $adjacentNetworkPathInfoBD = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoBD->setDeviceFrom('B');
        $adjacentNetworkPathInfoBD->setDeviceTo('D');
        $adjacentNetworkPathInfoBD->setLatency(100);

        $adjacentNetworkPathInfoDC = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoDC->setDeviceFrom('D');
        $adjacentNetworkPathInfoDC->setDeviceTo('C');
        $adjacentNetworkPathInfoDC->setLatency(30);

        $adjacentNetworkPathInfoDE = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoDE->setDeviceFrom('D');
        $adjacentNetworkPathInfoDE->setDeviceTo('E');
        $adjacentNetworkPathInfoDE->setLatency(10);

        $adjacentNetworkPathInfoEF = new AdjacentNetworkPathInfo();
        $adjacentNetworkPathInfoEF->setDeviceFrom('E');
        $adjacentNetworkPathInfoEF->setDeviceTo('F');
        $adjacentNetworkPathInfoEF->setLatency(1000);

        $networkPathInfoDeviceA = new NetworkPathInfo();
        $networkPathInfoDeviceA->setDeviceFrom('A');
        $networkPathInfoDeviceA->setDeviceTo($adjacentNetworkPathInfoAB);
        $networkPathInfoDeviceA->setDeviceTo($adjacentNetworkPathInfoAC);

        $networkPathInfoDeviceB = new NetworkPathInfo();
        $networkPathInfoDeviceB->setDeviceFrom('B');
        $networkPathInfoDeviceB->setDeviceTo($adjacentNetworkPathInfoAB);
        $networkPathInfoDeviceB->setDeviceTo($adjacentNetworkPathInfoBD);

        $networkPathInfoDeviceC = new NetworkPathInfo();
        $networkPathInfoDeviceC->setDeviceFrom('C');
        $networkPathInfoDeviceC->setDeviceTo($adjacentNetworkPathInfoAC);
        $networkPathInfoDeviceC->setDeviceTo($adjacentNetworkPathInfoDC);

        $networkPathInfoDeviceD = new NetworkPathInfo();
        $networkPathInfoDeviceD->setDeviceFrom('D');
        $networkPathInfoDeviceD->setDeviceTo($adjacentNetworkPathInfoBD);
        $networkPathInfoDeviceD->setDeviceTo($adjacentNetworkPathInfoDC);
        $networkPathInfoDeviceD->setDeviceTo($adjacentNetworkPathInfoDE);


        $networkPathInfoDeviceE = new NetworkPathInfo();
        $networkPathInfoDeviceE->setDeviceFrom('E');
        $networkPathInfoDeviceE->setDeviceTo($adjacentNetworkPathInfoDE);
        $networkPathInfoDeviceE->setDeviceTo($adjacentNetworkPathInfoEF);

        $networkPathInfoDeviceF = new NetworkPathInfo();
        $networkPathInfoDeviceF->setDeviceFrom('F');
        $networkPathInfoDeviceF->setDeviceTo($adjacentNetworkPathInfoEF);

        $networkPathInfoCollection = new NetworkPathInfoCollection();
        $networkPathInfoCollection->add($networkPathInfoDeviceA);
        $networkPathInfoCollection->add($networkPathInfoDeviceB);
        $networkPathInfoCollection->add($networkPathInfoDeviceC);
        $networkPathInfoCollection->add($networkPathInfoDeviceD);
        $networkPathInfoCollection->add($networkPathInfoDeviceE);
        $networkPathInfoCollection->add($networkPathInfoDeviceF);

        return $networkPathInfoCollection;
    }
}