<?php

use App\AssetManager;
use App\CacheManager;
use App\Plugin\PluginInterface;
use App\Plugin\PluginRouteInterface;
use App\WhitelistManager;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Weather implements PluginInterface
{
    const cacheFile = CacheManager::cachePath."/weatherresponse.json";
    const cacheTTL = 3600;
    private $location = "";
    private $apikey = "";
    private $units = "metric";
    private ?Client $guzzle;

    public function init(array $config)
    
    {
        $this->location = $config['location'];
        $this->apikey = $config['apikey'];
        $this->units = $config['units'];

        $this->guzzle = new Client([
            'base_uri' => 'https://api.openweathermap.org',
            'timeout' => 10
        ]);
    }
    private function getURLOrCache() {
        if(!file_exists(self::cacheFile) || time() - filemtime(self::cacheFile) >= self::cacheTTL || true) {
            // https://openweathermap.org/current
            $response = $this->guzzle->get('/data/2.5/weather',[
                'query' => [
                    'q' => $this->location,
                    'apikey' => $this->apikey,
                    'units' => $this->units
                ],
                'headers' => [
                    'User-Agent' => 'https://github.com/dgalli1/YetAnotherDashboard',
                ]
            ]);
            
            $body = $response->getBody()->__toString();
            //get weathericon
            // http://openweathermap.org/img/wn/10d@2x.png
            file_put_contents(CacheManager::cachePath."/weatherresponse.json",$body);    
        } else {
            $body = file_get_contents(self::cacheFile);
        }
        return $body;
    }



    public function registerHooks(): array
    {
        return [
            'alter_template_data_groups_group' => function ($variables) {
                if(array_key_exists('plugin',$variables) && $variables['plugin'] === 'Weather') {
                    $response = json_decode($this->getURLOrCache(),true);
                    $weatherimagepath = CacheManager::cachePath."/weatherimages";
                    if (!file_exists($weatherimagepath)) {
                        mkdir($weatherimagepath, 0777, true);
                    }
                    $weatherImageFile = $response['weather'][0]['icon'].".png";
                    $weatherImageFileDownload = $response['weather'][0]['icon']."@4x.png";
                    if(!file_exists($weatherimagepath."/".$weatherImageFile)) {
                        file_put_contents($weatherimagepath."/".$weatherImageFile,file_get_contents('http://openweathermap.org/img/wn/'.$weatherImageFileDownload));
                    }
                    $imagePath = AssetManager::addImage($weatherimagepath."/".$weatherImageFile,$weatherImageFile);
                    $variables['items'][] = [
                        'template' => 'weather.twig',
                        'data' => $response,
                        'imagePath' => $imagePath
                    ];
                }
                return $variables;
            }
        ];
    }
}
