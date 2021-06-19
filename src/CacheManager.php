<?php
namespace App;
use App\HookManager;

class CacheManager {
    const filepath = __DIR__."/../cache/build-timestamp.txt";
    private $timestamp;
    public function __construct()
    {
        if(file_exists(static::filepath)) {
            $this->timestamp = file_get_contents(static::filepath);
        } else {
            $this->timestamp = $_SERVER['REQUEST_TIME'];
        }

        HookManager::registerHook('preprocess_config',function($template,$config) {
            $config['cache'] = $this->timestamp;
            $config = $this->walkArray($config);
            return $config;
        },$this);
    }

    private function walkArray($config) {
        foreach ($config as $key => &$value) {
            if($key === 'logo_file') {
                $value = $value."?cache=".$this->timestamp;
            }
            if(is_array($value)) {
                $value = $this->walkArray($value);
            }
        }
        return $config;
    }
}