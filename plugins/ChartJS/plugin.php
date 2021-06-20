<?php

use App\AssetManager;
use App\Plugin\PluginInterface;
use App\WhitelistManager;

class ChartJS implements PluginInterface
{
    public function init(array $config)
    {
        if(array_key_exists('cdn',$config)) {
            AssetManager::addRemoteScript('https://cdn.jsdelivr.net/npm/chart.js');
            AssetManager::addRemoteScript('https://cdn.jsdelivr.net/npm/moment');
            AssetManager::addRemoteScript('https://cdn.jsdelivr.net/npm/chartjs-adapter-moment');

        } else {
            AssetManager::addLocalScript(__DIR__."/assets/chart.js",'chart.js');
        }
    }

    public function registerHooks(): array
    {
        return [];
    }
}
