<?php

namespace NetworkPath\Service;

use NetworkPath\Collection\NetworkPathInfoCollection;
use NetworkPath\DTO\AdjacentNetworkPathInfo;
use NetworkPath\DTO\NetworkPathInfo;
use \Exception;

class NetworkPathService
{
    private const MAX_ROW_LENGTH = 250;
    private const FILE_DELIMITOR = ",";
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

                while (($row = fgetcsv($fp, self::MAX_ROW_LENGTH, self::FILE_DELIMITOR)) !== FALSE) {
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

        foreach ($this->networkPathInfoCollection->getCollection() as $adj) {
            foreach ($adj->getDeviceTo() as $nwInfo) {
                $nwInfo->setVisited(false);
            }
        }

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
    public function traverseTheGraph($networkInfo, $latency, $result): string
    {
        echo sprintf('%s => %s', implode(' => ', $result), array_sum($latency)).PHP_EOL;
        if (!empty($networkInfo)){
            print_r($networkInfo);
            foreach($networkInfo->getDeviceTo() as $adjacentNetworkInfo){//C,b,e
                echo "Checking ". $adjacentNetworkInfo->getDeviceTo().PHP_EOL;
                $adjacentNode = $this->getAdjacentNode($networkInfo->getDeviceFrom(), $adjacentNetworkInfo);

                if ($adjacentNetworkInfo->isVisited()){
                    continue;
                }
                $adjacentNetworkInfo->setVisited(true);
                if(!in_array($adjacentNode, $result)) {
                    echo "Adding the node ".$adjacentNode.PHP_EOL;
                    $latency[] = $adjacentNetworkInfo->getLatency();
                    $result[] = $adjacentNode;
                    if ($adjacentNode == $this->deviceTo) {
                        if ((array_sum($latency) + $adjacentNetworkInfo->getLatency()) <= $this->latency) {

                            return sprintf('%s => %s', implode(' => ', $result), array_sum($latency));
                        }
                    } else {
                        echo " Getting network info for =>=> ".$adjacentNode.PHP_EOL;
                        $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($adjacentNode);
                        return $this->traverseTheGraph($networkInfo, $latency, $result);
                    }
                }
            }

            echo sprintf('Before popping %s => %s', implode(' => ', $result), array_sum($latency)).PHP_EOL;
            array_pop($latency);
            array_pop($result);
            echo sprintf('After popping %s => %s', implode(' => ', $result), array_sum($latency)).PHP_EOL;

            if (count($latency) > 0 && count($result) > 0){
                echo " Getting network info for ".$result[count($result) - 1].PHP_EOL;
                $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($result[count($result) - 1]);
               // print_r($networkInfo);

                print_r($networkInfo);
                return $this->traverseTheGraph($networkInfo, $latency, $result);
            }
        }
        echo sprintf('%s => %s', implode(' => ', $result), array_sum($latency)).PHP_EOL;
        return self::MESSAGE_PATH_NOT_FOUND;
    }

    public function traverseTheGraphNew($networkInfo, $latency, $result): string
    {
        echo sprintf('%s => %s', implode(' => ', $result), array_sum($latency)).PHP_EOL;
        if (!empty($networkInfo)){
            for($node = 0; $node < count($networkInfo->getDeviceTo()) ; $node++){
                //print_r($adjacentNetworkInfo);
                $adjacentNode = $networkInfo->getDeviceTo()[$node];

                if($adjacentNode->getDeviceTo() == $this->deviceTo){
                    if((array_sum($latency) + $adjacentNode->getLatency()) <= $this->latency){
                        $latency[] = $adjacentNode->getLatency();
                        $result[] = $adjacentNode->getDeviceTo();
                        return sprintf('%s => %s', implode(' => ', $result), array_sum($latency));
                    }
                } else {
                    if(!in_array($adjacentNode->getDeviceTo(), $result)){
                        $result[] = $adjacentNode->getDeviceTo();
                        $latency[] = $adjacentNode->getLatency();
                        $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($adjacentNode->getDeviceTo());
                        return $this->traverseTheGraph($networkInfo, $latency, $result);
                    }
                }
            }

            array_pop($latency);
            array_pop($result);

            if (count($latency) > 0 && count($result) > 0){
                echo " Getting network info for ".$result[count($result) - 1].PHP_EOL;
                $networkInfo = $this->networkPathInfoCollection->findDeviceFrom($result[count($result) - 1]);
                // print_r($networkInfo);
                foreach ($networkInfo->getDeviceTo() as $nwInfo) {
                    echo " setting back tracking node to not visited => [".$nwInfo->getDeviceFrom().",".$nwInfo->getDeviceTo()."]".PHP_EOL;
                    $nwInfo->setVisited(0);
                }

                print_r($networkInfo);
                return $this->traverseTheGraph($networkInfo, $latency, $result);
            }
        }
        echo sprintf('%s => %s', implode(' => ', $result), array_sum($latency)).PHP_EOL;
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
        //$adjacentNetworkInfoArray = $this->networkPathInfoCollection->getAdjacentNodeHash();

        $hashKey = $this->getHashKey([$deviceFrom, $deviceTo]);
//        $adjacentNetworkInfo = (isset($adjacentNetworkInfoArray) &&
//            array_key_exists($hashKey, $adjacentNetworkInfoArray)) ?
//            $adjacentNetworkInfoArray[$hashKey] : null;

        //if(empty($adjacentNetworkInfo)) {
            $adjacentNetworkInfo = $this->createAdjacentNetworkInfo(
                $hashKey,
                $deviceFrom,
                $deviceTo,
                $latency
            );
        //}

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
     * @param string $networkNode
     * @param AdjacentNetworkPathInfo $adjacentNetworkPathInfo
     * @return string
     */
    private function getAdjacentNode(
        string $networkNode,
        AdjacentNetworkPathInfo $adjacentNetworkPathInfo
    ): string {
        return $adjacentNetworkPathInfo->getDeviceTo();
//        $networkNode === $adjacentNetworkPathInfo->getDeviceTo() ?
//            $adjacentNetworkPathInfo->getDeviceFrom() : $adjacentNetworkPathInfo->getDeviceTo();
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
        //$this->networkPathInfoCollection->addToAdjacentNodeHash($hashKey, $adjacentNetworkInfo);

        return $adjacentNetworkInfo;
    }

    /**
     * @param string $inputValue
     * @return bool
     */
    public function setUpInputParameters(string $inputValue): bool
    {
        $inputNetworkInfo = explode(" ", strtoupper($inputValue));

        if(!empty($inputNetworkInfo) && count($inputNetworkInfo) === 3)
        {
            $this->deviceFrom = $inputNetworkInfo[0];
            $this->deviceTo = $inputNetworkInfo[1];
            $this->latency = $inputNetworkInfo[2];

            return true;
        }

        return false;
    }
}