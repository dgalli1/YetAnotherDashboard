<?php
namespace App;

use Exception;

class RemoteHeaders {

    private $user;
    private $groups;
    private $email;
    private static $instance;

    public function __construct()
    {
        $this->user = array_key_exists('HTTP_REMOTE_USER',$_SERVER) ? $_SERVER['HTTP_REMOTE_USER'] : '' ;
        $this->groups = array_key_exists('HTTP_REMOTE_GROUPS',$_SERVER) ? explode(',',$_SERVER['HTTP_REMOTE_GROUPS']) : ['cloud','chat','media','admins','developer'] ;
        $this->email = array_key_exists('HTTP_REMOTE_EMAIL',$_SERVER) ? $_SERVER['HTTP_REMOTE_EMAIL'] : '' ;
        self::$instance = $this;
    }
    public static function getInstance() {
        return self::$instance;
    }

    public function getUser() {
        return $this->user;
    }

    public function getGroups() {
        return $this->groups;
    }

    public function getEmail() {
        return $this->email;
    }
    private function inGroup($groups) {
        if(count($groups) === 0) {
            return  true;
        }
        foreach ($groups as $key2 => $group) {
            if(in_array($group,$this->getGroups())) {
                return true;
            }
        }
        return false;
    }
    private function filterByGroup(Array &$config) {
        //whole function should be recursive
        foreach ($config as $key => $value) {
            $groups = array_key_exists('groups',$value) ? $value['groups'] : [];
            $unsetCount = 0;
            if(!is_array($groups)) {
                throw new Exception("Check your config, Groups have to be an array in group ".$value['name']);
            }
            if($this->inGroup($groups)) {
                //check for secondary level
                if(array_key_exists('items',$value)) {
                    foreach ($value['items'] as $keyInner => $innerItems) {
                        $groups = array_key_exists('groups',$innerItems) ? $innerItems['groups'] : [];
                        if(!is_array($groups)) {
                            throw new Exception("Check your config, Groups have to be an array at item ".$innerItems['text']);
                        }
                        if(!$this->inGroup($groups)) {
                            $unsetCount++;
                            unset($config[$key]['items'][$keyInner]);
                        }
                    }
                }
            } else {
                unset($config[$key]);
            }
            if($unsetCount > 0) {
                //is group completly empty?
                if(count($value['items']) === $unsetCount) {
                    unset($config[$key]);
                }
            }
        }

    }
    public function filter(ConfigManager $configManager) {
        $config = $configManager->getConfig();
        $this->filterByGroup($config['header']['navigation']);
        $this->filterByGroup($config['groups']);
        return $config;
    }
}