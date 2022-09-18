<?php

namespace NetworkPath\DTO;

class AdjacentNetworkPathInfo
{
    /**
     * @var string
     */
    private $deviceFrom;

    /**
     * @var string
     */
    private $deviceTo;

    /**
     * @var int
     */
    private $latency;

    /**
     * @var bool
     */
    private $visited = false;

    /**
     * @return string
     */
    public function getDeviceTo(): string
    {
        return $this->deviceTo;
    }

    /**
     * @param string $deviceTo
     */
    public function setDeviceTo(string $deviceTo): void
    {
        $this->deviceTo = $deviceTo;
    }

    /**
     * @return int
     */
    public function getLatency(): int
    {
        return $this->latency;
    }

    /**
     * @param int $latency
     */
    public function setLatency(int $latency): void
    {
        $this->latency = $latency;
    }

    /**
     * @return bool
     */
    public function isVisited(): bool
    {
        return $this->visited;
    }

    /**
     * @param bool $visited
     */
    public function setVisited(bool $visited): void
    {
        $this->visited = $visited;
    }

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
}