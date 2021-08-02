<?php

use App\AssetManager;
use App\Plugin\PluginInterface;
use App\Plugin\PluginRouteInterface;
use App\WhitelistManager;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RawHTML implements PluginInterface
{
    private $apikey = "";
    private ?Client $guzzle;
    private $plugin_instances = 0;

    public function init(array $config)
    {
    }

    public function registerHooks(): array
    {
        return [
            'alter_template_data_groups_group' => function ($variables) {
                if(array_key_exists('plugin',$variables) && $variables['plugin'] === 'RawHTML') {
                    $variables['items'][] = [
                        'template' => 'rawhtml.twig',
                        'html' => $variables['html']
                    ];
                }
                return $variables;
            }
        ];
    }
}
