<?php

namespace NetworkPath\Tests\Unit\Service;
use NetworkPath\Collection\NetworkPathInfoCollection;
use NetworkPath\DTO\AdjacentNetworkPathInfo;
use NetworkPath\DTO\NetworkPathInfo;
use NetworkPath\Service\NetworkPathService;
use PHPUnit\Framework\TestCase;

class NetworkPathServiceTest extends TestCase
{
    public function testSetUpInputParametersValidInputReturnTrue()
    {
        $networkPathInfoCollection = new NetworkPathInfoCollection();
        $networkPathService = new NetworkPathService($networkPathInfoCollection);

        $result = $networkPathService->setUpInputParameters('a b 10');

        $this->assertEquals(true, $result);
    }

    public function testSetUpInputParametersInvalidInputReturnFalse()
    {
        $networkPathInfoCollection = new NetworkPathInfoCollection();
        $networkPathService = new NetworkPathService($networkPathInfoCollection);

        $result = $networkPathService->setUpInputParameters('a b');

        $this->assertEquals(false, $result);
    }

    public function testSetUpInputParametersEmptyInputReturnFalse()
    {
        $networkPathInfoCollection = new NetworkPathInfoCollection();
        $networkPathService = new NetworkPathService($networkPathInfoCollection);

        $result = $networkPathService->setUpInputParameters('');

        $this->assertEquals(false, $result);
    }

    /**
     * @dataProvider getInputValues
     * @param string $inputValue
     * @param string $result
     * @return void
     */
    public function testEvaluateNetworkPath(string $inputValue, string $expectedResult)
    {
        $networkPathInfoCollection = $this->getNetworkPathInfoCollection();
        $networkPathService = new NetworkPathService($networkPathInfoCollection);

        $networkPathService->setUpInputParameters($inputValue);
        $actualResult = $networkPathService->evaluateNetworkPath();
        $this->assertEquals($expectedResult, $actualResult);
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

    public function getInputValues(): array
    {
        return [
            ['a f 1000', 'Path not found'],
            ['a f 1200', 'A => B => D => E => F => 1120'],
            ['A D 100', 'A => C => D => 50'],
            ['E A 400', 'E => D => B => A => 120'],
            ['E A 80', 'E => D => C => A => 60'],
            ['e c 400', 'E => D => B => A => C => 140'],
            ['d a 300', 'D => B => A => 110'],
            ['a e 70', 'A => C => D => E => 60'],
            ['a e 20', 'Path not found']
        ];
    }
}