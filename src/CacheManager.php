<?php
namespace App;
use App\HookManager;

class CacheManager {
    const cachePath = __DIR__."/../cache";
    const filepath = __DIR__."/../cache/build-timestamp.txt";
    private static $timestamp;
    public function __construct()
    {
        if(file_exists(static::filepath)) {
            static::$timestamp = file_get_contents(static::filepath);
        } else {
            static::$timestamp = $_SERVER['REQUEST_TIME'];
        }
        HookManager::registerHook('preprocess_config',function($template,$config) {
            $config['cache'] = static::$timestamp;
            $config = $this->walkArray($config);
            return $config;
        },$this);

    }

    public static function getTimestamp() {
        return static::$timestamp;
    }

    private function walkArray($config) {
        foreach ($config as $key => &$value) {
            if($key === 'logo_file') {
                $value = $value."?cache=".static::$timestamp;
            }
            if(is_array($value)) {
                $value = $this->walkArray($value);
            }
        }
        return $config;
    }
}