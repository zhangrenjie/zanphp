<?php
namespace Zan\Framework\Foundation\Core;

use Zan\Framework\Foundation\Exception\System\InvalidArgumentException;
use Zan\Framework\Utilities\DesignPattern\Singleton;
use Zan\Framework\Utilities\Types\Dir;
use Zan\Framework\Utilities\Types\Arr;

class ConfigLoader
{
    use Singleton;

    public function load($path ,$ignoreStructure = false)
    {
        if(!is_dir($path)){
            throw new InvalidArgumentException('Invalid path for ConfigLoader');
        }

        $path = Dir::formatPath($path);
        $configFiles = Dir::glob($path, '*.php', Dir::SCAN_BFS);

        $configMap = [];
        foreach($configFiles as $configFile){
            $loadedConfig = require $configFile;
            if(!is_array($loadedConfig)){
                throw new InvalidArgumentException("syntax error find in config file: " . $configFile);
            }
            if(!$ignoreStructure){
                $keyString = substr($configFile, strlen($path), -4);
                $loadedConfig = Arr::createTreeByList(explode('/',$keyString),$loadedConfig);
            }

            $configMap = Arr::merge($configMap,$loadedConfig);
        }

        return $configMap;
    }

}