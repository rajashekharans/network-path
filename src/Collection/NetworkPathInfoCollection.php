<?php

namespace NetworkPath\Collection;

use NetworkPath\DTO\AdjacentNetworkPathInfo;
use NetworkPath\DTO\NetworkPathInfo;

class NetworkPathInfoCollection
{
    /**
     * @var NetworkPathInfo[]
     */
    private $networkPathCollection;

    /**
     * @param NetworkPathInfo $networkPathInfo
     * @return void
     */
    public function add(NetworkPathInfo $networkPathInfo)
    {
        $this->networkPathCollection[] = $networkPathInfo;
    }

    /**
     * @return NetworkPathInfo[]
     */
    public function getCollection(): ?array
    {
        return $this->networkPathCollection;
    }

    /**
     * @param string $deviceFrom
     * @return NetworkPathInfo|null
     */
    public function findDeviceFrom(string $deviceFrom): ?NetworkPathInfo
    {
        if (!empty($this->networkPathCollection)) {
            foreach ($this->networkPathCollection as $networkPathInfo) {
                if ($networkPathInfo->getDeviceFrom() === $deviceFrom) {
                    return $networkPathInfo;
                }
            }
        }
        return null;
    }
}