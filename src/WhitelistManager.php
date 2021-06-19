<?php
namespace App;
use Symfony\Component\Yaml\Yaml;

class WhitelistManager {
    const filepath = __DIR__."/../cache/whitelist.yml";
    public $enabled = false;
    public $classes = [];
    private static $instance;
    
    public function __construct($regenerate)
    {
        if($regenerate == true) {
            $this->enabled = true;
        }
        self::$instance = $this;
    }
    private static function getInstance() {
        return self::$instance;
    }
    public static function addClass(String|Array $classes) {
        $that = self::getInstance();
        if($that->enabled === false) {
            return;
        }
        if(is_string($classes)) {
            $classes = explode(' ',trim($classes));
        }
        $that->classes = array_merge($that->classes,$classes);
    }

    public function __destruct() {
        if($this->enabled === false) {
            return;
        }
        //ugly hack, otherwhise webpack will crash, because the yaml parsers outputs an object instead of an array
        if(count($this->classes) == 0) {
            $this->classes[] = 'random-nonexisting-class';
        }
        $yaml = Yaml::dump($this->classes);
        file_put_contents(self::filepath,$yaml);
    }
}