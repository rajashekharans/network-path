<?php

namespace NetworkPath\DTO;

/**
 * Class to store Device Info
 */
class NetworkPathInfo
{
    /**
     * @var string
     */
    private $deviceFrom;

    /**
     * @var AdjacentNetworkPathInfo[]
     */
    private $deviceTo;

    /**
     * @return string
     */
    public function getDeviceFrom(): string
    {
        return $this->deviceFrom;
    }

    /**
     * @param string $deviceFrom
     */
    public function setDeviceFrom(string $deviceFrom): void
    {
        $this->deviceFrom = $deviceFrom;
    }

    /**
     * @return AdjacentNetworkPathInfo[]
     */
    public function getDeviceTo(): array
    {
        return $this->deviceTo;
    }

    /**
     * @param AdjacentNetworkPathInfo $deviceTo
     */
    public function setDeviceTo(AdjacentNetworkPathInfo $deviceTo): void
    {
        $this->deviceTo[] = $deviceTo;
    }
}