<?php

use App\AssetManager;
use App\Plugin\PluginInterface;
use App\WhitelistManager;

class ChartJS implements PluginInterface
{
    public function init(array $config)
    {
        AssetManager::addRemoteScript('https://cdn.jsdelivr.net/npm/chart.js',['defer']);
        AssetManager::addRemoteScript('https://cdn.jsdelivr.net/npm/moment',['defer']);
        AssetManager::addRemoteScript('https://cdn.jsdelivr.net/npm/chartjs-adapter-moment',['defer']);
    }

    public function registerHooks(): array
    {
        return [];
    }
}
