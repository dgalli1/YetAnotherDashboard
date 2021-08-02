<?php
namespace App;
use Symfony\Component\Yaml\Yaml;

class ConfigManager {
    const configPath = __DIR__ ."/../config.yml";
    private $config = NULL;
    private static $instance = NULL;
    private $hasOverwrittenClasses = false;
    private $hasAdditionalClasses = false;
    private $hasCustomTemplates = false;

    public $mappedClasses = [];

    const classesOverride = 'classes-override';
    const classesAdditonal = 'classes-additional';
    const templatesOverride = 'templates';

    public function __construct()
    {
        $this->config = Yaml::parseFile(__DIR__ ."/../config/config.yml");
        if( array_key_exists('theme',$this->config)) {
            $this->hasOverwrittenClasses = 
                array_key_exists(self::classesOverride,$this->config['theme']) &&
                $this->config['theme'][self::classesOverride] !== NULL &&
                count($this->config['theme'][self::classesOverride]) > 0;
            $this->hasAdditionalClasses = 
                array_key_exists(self::classesAdditonal,$this->config['theme']) &&
                $this->config['theme'][self::classesAdditonal] !== NULL &&
                count($this->config['theme'][self::classesAdditonal]) > 0;
            $this->hasCustomTemplates =
                array_key_exists(self::templatesOverride,$this->config['theme'])
                && $this->config['theme'][self::templatesOverride] !== NULL
                &&  count($this->config['theme'][self::templatesOverride]) > 0;
        }
        self::$instance = $this;
    }

    public static function getInstance() {
        return self::$instance;
    }
    private function getTemplateMapping($templateName) {
        return str_replace('/','_',explode('.',$templateName)[0]);
    }
    public function getIsDebug() {
        return $this->config['debug'];
    }
    public function getOverwrittenGlobalTemplates($templateName) {
        if(!$this->hasCustomTemplates ) {
            return $templateName;
        }
        $templateNameMapped = $this->getTemplateMapping($templateName);
        if(
            $templateNameMapped !== false &&
            array_key_exists($templateNameMapped,$this->config['theme'][self::templatesOverride]) &&
            is_string($this->config['theme'][self::templatesOverride][$templateNameMapped])
        ) {
            $templateNameMapped = HookManager::trigger("alter_template_name_found",[
                'template' => $templateName,
                'templateConfigName' => $templateNameMapped
            ],'templateConfigName');
            return $this->config['theme'][self::templatesOverride][$templateNameMapped];
        }
        HookManager::trigger("alter_template_name",[
            'template' => $templateName,
            'templateConfigName' => $templateNameMapped
        ],'template');
        if(!in_array($templateNameMapped,$this->mappedClasses))
        $this->mappedClasses[] = $templateNameMapped;

        return $templateName;
    }

    public function getOverwrittenGlobalClasses($templateName,$classes) {
        
        $templateNameMapped = $this->getTemplateMapping($templateName);
        HookManager::trigger("alter_class_config_key",[
            'template' => $templateName,
            'templateConfigName' => $templateNameMapped
        ],'templateConfigName');
        if(
            $this->hasOverwrittenClasses
            && array_key_exists($templateNameMapped,$this->config['theme'][self::classesOverride]) && 
            is_string($this->config['theme'][self::classesOverride][$templateNameMapped])
        ) {
            $classes = $this->config['theme'][self::classesOverride][$templateNameMapped];
        }
        if(
            $this->hasAdditionalClasses &&
            array_key_exists($templateNameMapped,$this->config['theme'][self::classesAdditonal]) &&
            is_string($this->config['theme'][self::classesAdditonal][$templateNameMapped])
        ) {
            $classes .= " ".$this->config['theme'][self::classesAdditonal][$templateNameMapped];
        }
        $classes = HookManager::trigger(
            "alter_classes_$templateNameMapped",
            ['classes' => $classes],
            'classes'
    );
        return $classes;
    }


    public function getConfig() {
        return $this->config;
    }
}