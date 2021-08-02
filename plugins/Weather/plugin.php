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
    private $lat = "";
    private $lon = "";
    private $altitude = "";
    private ?Client $guzzle;

    public function init(array $config)
    {
        $this->lat = $config['lat'];
        $this->lon = $config['lon'];
        $this->altitude = $config['altitude'];
        $this->guzzle = new Client([
            'base_uri' => 'https://api.met.no/weatherapi/',
            'timeout' => 10
        ]);
    }
    private function getURLOrCache() {
   
        if(!file_exists(self::cacheFile) || time() - filemtime(self::cacheFile) >= self::cacheTTL) {
            // https://api.met.no/doc/ForecastJSON
            $response = $this->guzzle->get('locationforecast/2.0/complete?lat='.$this->lat.'&lon='.$this->lon.'&altitude='.$this->altitude,[
                'headers' => [
                    'User-Agent' => 'https://github.com/dgalli1/YetAnotherDashboard',
                ]
            ]);
            $body = $response->getBody()->__toString();
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
                    $variables['items'][] = [
                        'template' => 'weather.twig',
                        'units' => $response['properties']['meta']['units'],
                        'temperatur'

                    ];
                }
                return $variables;
            }
        ];
    }
}
