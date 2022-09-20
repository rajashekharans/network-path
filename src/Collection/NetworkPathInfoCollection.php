<?php

namespace NetworkPath\Collection;

use NetworkPath\DTO\AdjacentNetworkPathInfo;
use NetworkPath\DTO\NetworkPathInfo;

/**
 * Devices and its path are stored as adjacency list
 */
class NetworkPathInfoCollection
{
    /**
     * @var NetworkPathInfo[]
     */
    private $networkPathCollection;

    /**
     * @var AdjacentNetworkPathInfo[]
     */
    private $adjacentNetworkPathInfoCollection;

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

    /**
     * @param string $hashKey
     * @param AdjacentNetworkPathInfo $adjacentNetworkInfo
     * @return void
     */
    public function addToAdjacentNodeHash(string $hashKey, AdjacentNetworkPathInfo $adjacentNetworkInfo)
    {
        $this->adjacentNetworkPathInfoCollection[$hashKey] = $adjacentNetworkInfo;
    }

    /**
     * @param string $hashKey
     * @return AdjacentNetworkPathInfo|null
     */
    public function getAdjacentNodeInfoFromHash(string $hashKey): ?AdjacentNetworkPathInfo
    {
        return (isset($this->adjacentNetworkPathInfoCollection) &&
            array_key_exists($hashKey, $this->adjacentNetworkPathInfoCollection)) ?
            $this->adjacentNetworkPathInfoCollection[$hashKey] : null;
    }

    /**
     * @return AdjacentNetworkPathInfo[]
     */
    public function getAdjacentNodeArray(): array
    {
        return $this->adjacentNetworkPathInfoCollection;
    }

    /**
     * @param string $networkNode
     * @param AdjacentNetworkPathInfo $adjacentNetworkPathInfo
     * @return string
     */
    public function getAdjacentNode(
        string $networkNode,
        AdjacentNetworkPathInfo $adjacentNetworkPathInfo
    ): string {
        return $networkNode === $adjacentNetworkPathInfo->getDeviceTo() ?
            $adjacentNetworkPathInfo->getDeviceFrom() : $adjacentNetworkPathInfo->getDeviceTo();
    }

    /**
     * @return void
     */
    public function printNetworkInfoCollection(): void
    {
        foreach($this->networkPathCollection as $node) {
            echo $node->getDeviceFrom() . " -> ";
            foreach($node->getDeviceTo() as $neighbour) {
                echo " [ ".$neighbour->getDeviceFrom(). ", ". $neighbour->getDeviceTo(). ", ". $neighbour->getLatency(). ", ". $neighbour->isVisited()."[".$this->getAdjacentNode($node->getDeviceFrom(), $neighbour)."]]";
            }
            echo PHP_EOL;
        }
    }
}