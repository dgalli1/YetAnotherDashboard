<?php
namespace App;

class AssetManager {
    private static $heads = [];
    private static $bustCache = false;
    const assetLocationAbsolute=__DIR__."/../webroot/assets/";
    const assetLocationRelative="/assets/";
    
    public function __construct($bustCache)
    {
        self::$bustCache = $bustCache;
        HookManager::registerHook('preprocess_config',function($template,$config) {
            if(!array_key_exists('head',$config)) {
                $config['head'] = [];
            }
            $config['head']['includes'] = self::$heads;
            return $config;
        },$this);
    }

    private static function attributeBuilder($attributes) {
        $stringAttributes = "";
        foreach ($attributes as $key => $value) {
            if(is_numeric($key)) {
                $stringAttributes.=" ".$value;
            } else {
                $stringAttributes.=" ".$key."=".'"'.$value.'"';
            }
        }
        return $stringAttributes;
    }

    private static function copyAsset($sourcePath,$filename) {
        if(self::$bustCache) {
            copy($sourcePath,self::assetLocationAbsolute.$filename);
        }
        return self::assetLocationRelative.$filename;
    }
    /**
     * Adds a Script Tag from a local file
     *
     * @param [type] $url
     * @param array $attributes Ex ['async','defer','crossorigin' => 'anonymous']
     * @return void
     */
    public static function addLocalScript($scriptpath,$filename,$attributes = []) {
        $filepath = self::copyAsset($scriptpath,$filename);
        $stringAttributes = self::attributeBuilder($attributes);
        self::$heads[] = '<script src="'.$filepath.'?cache='.CacheManager::getTimestamp().'"'.$stringAttributes."></script>";
    }
    /**
     * Adds a Script Tag from a CDN
     *
     * @param [type] $url
     * @param array $attributes Ex ['async','defer','crossorigin' => 'anonymous']
     * @return void
     */
    public static function addRemoteScript($url,$attributes = []) {
        $stringAttributes = self::attributeBuilder($attributes);
        self::$heads[] = '<script src="'.$url.'?cache='.CacheManager::getTimestamp().'"'.$stringAttributes."></script>";
    }

}