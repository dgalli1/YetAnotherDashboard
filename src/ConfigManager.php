<?php
namespace App;
use Symfony\Component\Yaml\Yaml;

class ConfigManager {
    const configPath = __DIR__ ."/../config.yml";
    private $config = NULL;
    private static $instance = NULL;

    public function __construct()
    {
        $this->config = Yaml::parseFile(__DIR__ ."/../config.yml");
        self::$instance = $this;
    }

    public static function getInstance() {
        return self::$instance;
    }

    public function getConfig() {
        return $this->config;
    }
}