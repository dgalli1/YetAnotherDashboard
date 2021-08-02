<?php
namespace App\Plugin;
use App\ConfigManager;
use App\HookManager;
use App\RoutingManager;

class PluginManager {

    const rootfolders = [
        __DIR__."/../../config/plugins/",
        __DIR__."/../../plugins/"
    ];
    private $plugins = [];
    public function __construct()
    {
        $this->plugins = $this->getPlugins();
        foreach ($this->plugins as $key => &$plugin) {
            $plugin = $this->findPlugin($plugin);
            if($plugin == false) {
                continue;
            }
            $this->registerPlugin($plugin);
        }
    }
    private function findPlugin($plugin)  {
        //@todo Might need caching
        foreach (self::rootfolders as $key => $folder) {
            $pluginFolder = $folder.$plugin['name'];
            if(!is_dir($pluginFolder)) {
                continue;
            }
            $plugin['folder'] = $pluginFolder;
            $plugin['pluginFile'] = $pluginFolder."/plugin.php";
            if(!is_file($plugin['pluginFile'])) {
                continue;
            }
            require_once $plugin['pluginFile'];
            return $plugin;
            $folder = "";
        }
        return false;
    }
    private function getPlugins() {
        $configManager =ConfigManager::getInstance();
        $config = $configManager->getConfig();
        if(!array_key_exists('plugins',$config)) {
            return [];
        }
        $config['plugins'] = array_filter($config['plugins'], function($plugin){
            return $plugin['enabled'];
        });
        return $config['plugins'];        
    }

    private function registerPlugin($plugin) {
        /** @var PluginInterface */
        $pluginInstance = new $plugin['name'];
        $pluginInstance->init($plugin);
        $hooks = $pluginInstance->registerHooks();
        foreach ($hooks as $key => $hook) {
            HookManager::registerHook($key,$hook,$pluginInstance);
        }
        if($pluginInstance instanceof PluginRouteInterface) {
            $routes = $pluginInstance->registerRoute();
            foreach ($routes as $key => $route) {
                RoutingManager::registerRoute($plugin,$key,$route,$pluginInstance);
            }
        }
    }

    public function getTemplateFolders() {
        $folders = [];
        foreach ($this->plugins as $key => $value) {
            if(is_dir($value['folder']."/templates")) {
                $folders[] = $value['folder']."/templates";
            }
        }
        return $folders;
    }
}