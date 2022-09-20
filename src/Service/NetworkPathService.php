<?php

namespace NetworkPath\Service;

use NetworkPath\Collection\NetworkPathInfoCollection;
use NetworkPath\DTO\AdjacentNetworkPathInfo;
use NetworkPath\DTO\NetworkPathInfo;
use \Exception;

class NetworkPathService
{
    private const MAX_ROW_LENGTH = 250;
    private const FILE_DELIMITER = ",";
    private const COLUMN_DEVICE_FROM = 0;
    private const COLUMN_DEVICE_TO = 1;
    private const COLUMN_LATENCY = 2;
    private const MESSAGE_PATH_NOT_FOUND = 'Path not found';

    /**
     * @var NetworkPathInfoCollection
     */
    private $networkPathInfoCollection;

    /**
     * @var mixed|string
     */
    private $deviceFrom;

    /**
     * @var mixed|string
     */
    private $deviceTo;

    /**
     * @var mixed|string
     */
    private $latency;


    /**
     * @param NetworkPathInfoCollection $networkPathInfoCollection
     */
    public function __construct(NetworkPathInfoCollection $networkPathInfoCollection)
    {
        $this->networkPathInfoCollection = $networkPathInfoCollection;
    }

    /**
     * @param string $filePath
     * @return void
     */
    public function createNetworkPathCollection(string $filePath): void
    {
        try {
            if (($fp = fopen($filePath, "r")) !== FALSE) {

                while (($row = fgetcsv($fp, self::MAX_ROW_LENGTH, self::FILE_DELIMITER)) !== FALSE) {
                    if (count($row) < 3) {
                        //skip the row, if row has less than three columns
                        continue;
                    }

                    $this->addToCollection(
                        strtoupper($row[self::COLUMN_DEVICE_FROM]),
                        strtoupper($row[self::COLUMN_DEVICE_TO]),
                        $row[self::COLUMN_LATENCY]
                    );

                    $this->addToCollection(
                        strtoupper($row[self::COLUMN_DEVICE_TO]),
                        strtoupper($row[self::COLUMN_DEVICE_FROM]),
                        $row[self::COLUMN_LATENCY]
                    );
                }
                fclose($fp);
            }
        } catch (Exception $exception) {
           echo ("Exception Occurred".$exception->getMessage());
        }
    }

    /**
     * @param string $inputValue
     * @return string
     */
    public function evaluateNetworkPath(): string
    {
        $latency[] = null;
        $result[] = $this->deviceFrom;

        //Setting all paths as not visited
        foreach ($this->networkPathInfoCollection->getCollection() as $adj) {
            foreach ($adj->getDeviceTo() as $nwInfo) {
                $nwInfo->setVisited(false);
            }
        }

        //Visit the source device
        $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($this->deviceFrom);
        if (!empty($networkInfo)) {
            return $this->traverseTheGraph($networkInfo, $latency, $result);
        }

        return self::MESSAGE_PATH_NOT_FOUND;
    }


    /**
     * @param $networkInfo
     * @param $latency
     * @param $result
     * @return string
     */
    private function traverseTheGraph($networkInfo, $latency, $result): string
    {
        if (!empty($networkInfo)){
            foreach($networkInfo->getDeviceTo() as $adjacentNetworkInfo){//C,b,e
                $adjacentNode = $this->networkPathInfoCollection
                    ->getAdjacentNode($networkInfo->getDeviceFrom(), $adjacentNetworkInfo);
                if ($adjacentNetworkInfo->isVisited()){
                    continue;
                }
                $adjacentNetworkInfo->setVisited(true);
                if(!in_array($adjacentNode, $result)) {
                    $latency[] = $adjacentNetworkInfo->getLatency();
                    $result[] = $adjacentNode;
                    if ($adjacentNode == $this->deviceTo) {
                        if (array_sum($latency) <= $this->latency) {
                            return sprintf('%s => %s', implode(' => ', $result), array_sum($latency));
                        }
                    } else {
                        /*
                         * If latency of the path covered is greater than desired latency,
                         * we are on wrong path, back track one node and traverse again
                         */
                        if (array_sum($latency) >= $this->latency){
                            array_pop($latency);
                            array_pop($result);

                            $adjacentNode = $result[count($result) - 1];
                        }
                        $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($adjacentNode);
                        return $this->traverseTheGraph($networkInfo, $latency, $result);
                    }
                }
            }

            /*
             * If reached the depth, back track and traverse again
             */
            array_pop($latency);
            array_pop($result);

            if (count($latency) > 0 && count($result) > 0){
                $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($result[count($result) - 1]);
                return $this->traverseTheGraph($networkInfo, $latency, $result);
            }
        }
        return self::MESSAGE_PATH_NOT_FOUND;
    }

    /**
     * @param $deviceFrom
     * @param $deviceTo
     * @param $latency
     * @return void
     */
    private function addToCollection($deviceFrom, $deviceTo, $latency): void
    {
        $hashKey = $this->getHashKey([$deviceFrom, $deviceTo]);
        $adjacentNetworkInfo = $this->networkPathInfoCollection->getAdjacentNodeInfoFromHash($hashKey);

        if(empty($adjacentNetworkInfo)) {
            $adjacentNetworkInfo = $this->createAdjacentNetworkInfo(
                $hashKey,
                $deviceFrom,
                $deviceTo,
                $latency
            );
        }

        $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($deviceFrom);

        if (!empty($networkInfo)) {
            $networkInfo->setDeviceTo($adjacentNetworkInfo);
        } else {
            $networkInfo = new NetworkPathInfo();
            $networkInfo->setDeviceFrom($deviceFrom);
            $networkInfo->setDeviceTo($adjacentNetworkInfo);

            $this->networkPathInfoCollection->add($networkInfo);
        }
    }

    /**
     * @param array $keysArray
     * @return string
     */
    private function getHashKey(array $keysArray)
    {
        sort($keysArray);
        return  md5(implode( ",", $keysArray ));
    }

    /**
     * @param string $hashKey
     * @param string $deviceFrom
     * @param string $deviceTo
     * @param int $latency
     * @return AdjacentNetworkPathInfo
     */
    private function createAdjacentNetworkInfo(
        string $hashKey,
        string $deviceFrom,
        string $deviceTo,
        int $latency
    ) {
        $adjacentNetworkInfo = new AdjacentNetworkPathInfo();
        $adjacentNetworkInfo->setDeviceFrom($deviceFrom);
        $adjacentNetworkInfo->setDeviceTo($deviceTo);
        $adjacentNetworkInfo->setLatency($latency);
        $adjacentNetworkInfo->setVisited(0);
        $adjacentNetworkInfo->setHashKey($hashKey);
        $this->networkPathInfoCollection->addToAdjacentNodeHash($hashKey, $adjacentNetworkInfo);

        return $adjacentNetworkInfo;
    }

    /**
     * @param string|null $inputValue
     * @return bool
     */
    public function setUpInputParameters(?string $inputValue): bool
    {
        if(!empty($inputValue)){
            $inputNetworkInfo = explode(" ", strtoupper($inputValue));

            if(!empty($inputNetworkInfo) && count($inputNetworkInfo) === 3) {
                $this->deviceFrom = $inputNetworkInfo[0];
                $this->deviceTo = $inputNetworkInfo[1];
                $this->latency = $inputNetworkInfo[2];

                return true;
            }
        }

        return false;
    }
}