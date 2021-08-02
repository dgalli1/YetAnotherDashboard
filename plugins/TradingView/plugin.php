<?php

use App\AssetManager;
use App\Plugin\PluginInterface;
use App\Plugin\PluginRouteInterface;
use App\WhitelistManager;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TradingView implements PluginInterface
{
    private $apikey = "";
    private ?Client $guzzle;
    private $plugin_instances = 0;

    public function init(array $config)
    {
        AssetManager::addRemoteScript('https://s3.tradingview.com/tv.js',['defer']);
    }

    public function registerHooks(): array
    {
        return [
            'alter_template_data_groups_group' => function ($variables) {
                if(array_key_exists('plugin',$variables) && $variables['plugin'] === 'TradingView') {
                    if(!array_key_exists('symbol',$variables)) {
                        throw new Exception('You have to declare a Symbol to use Tradingview');
                    }
                    $plugin_data = [
                            'autosize' => true,
                            'symbol' =>  $variables['symbol'],
                            'theme' => isset($variables['theme']) ? $variables['theme'] : 'dark',
                            'interval' => isset($variables['interval']) ? $variables['inverval'] : 'D', // D / W or hour as number
                            'style' => isset($variables['style']) ? $variables['style'] : "1", 
                            'allow_symbol_change' => isset($variables['allow_symbol_change']) ? $variables['allow_symbol_change'] : false,
                            'local' => isset($variables['locale']) ? $variables['locale'] : 'en',
                            'toolbar_bg' => isset($variables['toolbar_bg']) ? $variables['toolbar_bg'] : '#f1f3f6',
                            'enable_publishing' => isset($variables['enable_publishing']) ? $variables['enable_publishing'] : false,
                            'withdateranges' => isset($variables['withdateranges']) ? $variables['withdateranges'] : false,
                            'hide_side_toolbars' => isset($variables['hide_side_toolbar']) ? $variables['hide_side_toolbar'] : true,
                            'calendar' => isset($variables['calendar']) ? $variables['calandar'] : false,
                            'hotlist' => isset($variables['hotlist']) ? $variables['hotlist'] : false,
                            'details' => isset($variables['details']) ? $variables['details'] : false,
                            'container_id' => 'tradingview-'.$this->plugin_instances,
                    ];
                    if(isset($variables['studies'])) {
                        $plugin_data['studies'] = $variables['studies'];
                    }
                    if(isset($variables['range'])) {
                        $plugin_data['range'] = $variables['range'];
                    }
                    $plugin_data = (object)$plugin_data;
                    $config = json_encode($plugin_data);
                    $variables['items'][] = [
                        'template' => 'tradingview.twig',
                        'container_id' => 'tradingview-'.$this->plugin_instances,
                        'tradingview_settings' => $config,
                        'height' => isset($variables['height']) ? $variables['height'] : '350px'
                    ];
                    $this->plugin_instances++;
                }
                return $variables;
            }
        ];
    }
}
