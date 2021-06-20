<?php

use App\AssetManager;
use App\Plugin\PluginInterface;
use App\Plugin\PluginRouteInterface;
use App\WhitelistManager;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SabnzbdTraffic implements PluginInterface,PluginRouteInterface
{
    private $apikey = "";
    private ?Client $guzzle;

    public function registerRoute(): array {
        return [
            'data' => function(Request $request):Response {
                $response = $this->guzzle->get('api?mode=queue&search=&start=0&limit=1&output=json&apikey='.$this->apikey);
                $responseArray = json_decode($response->getBody(),true)['queue'];
                $fullspeed = $responseArray['speedlimit_abs'] / $responseArray['speedlimit'] * 100;
                $currentSpeed = $responseArray['kbpersec'] * 1024;
                return new JsonResponse([
                    'currentSpeed' => round($currentSpeed /1024,2),
                    'fullSpeed' => round($fullspeed / 1024,2)
                ]);
            }
        ];
    }
    public function init(array $config)
    {
        $this->apikey = $config['apikey'];
        $this->guzzle = new Client([
            'base_uri' => $config['baseurl'],
            'timeout' => 5
        ]);
        AssetManager::addLocalScript(__DIR__."/assets/sabnzbd.js",'sabnzbd.js');
    }

    public function registerHooks(): array
    {
        return [
            'alter_template_data_groups_group' => function ($variables) {
                if(array_key_exists('plugin',$variables) && $variables['plugin'] === 'SabnzbdTraffic') {
                    $variables['items'][] = [
                        'template' => 'sabtraffic.twig'
                    ];
                }
                return $variables;
            }
        ];
    }
}
