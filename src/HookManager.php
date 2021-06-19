<?php
namespace App;

use Exception;

class HookManager {
    private $registered_hooks = [];
    private static $instance;
    
    public function __construct()
    {
        self::$instance = $this;
    }
    
    public static function getInstance() {
        return self::$instance;
    }

    public function registerHookInternal($hookName,$function,$class) {
        if(!array_key_exists($hookName,$this->registered_hooks)) {
            $this->registered_hooks[$hookName] = [];
        }
        if(in_array($function,$this->registered_hooks[$hookName])) {
            throw new Exception('Hook '.$hookName." is registered multiple times");
        }

        $this->registered_hooks[$hookName][] = [
            'closure' => $function,
            'class' => $class
        ];
    }
    public static function registerHook($hookName,$function,$class) {
        $instance = self::getInstance();
        return $instance->registerHookInternal($hookName,$function,$class);   
    }

    public function triggerInteral($hookName,$arguments,$returnkey) {
        if(!array_key_exists($hookName,$this->registered_hooks)) {
            return $arguments[$returnkey];
        }
        $registerAdditonalClasses = false;
    
        foreach ($this->registered_hooks[$hookName] as $key => $hook) {
            /** @var \Closure  */
            $closure = $hook['closure'];
            $arguments[$returnkey] = $closure->call($hook['class'],...$arguments);
        }
        return $arguments[$returnkey];
    }
    public static function trigger($hookName,$arguments,$returnkey) {
        $instance = self::getInstance();
        return $instance->triggerInteral($hookName,$arguments,$returnkey);   
    }
}