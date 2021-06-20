<?php
namespace App;

class PreprocessTwig {

    public static function preprocessTemplateVariables($variables,$template) {
        if(!is_array($variables)) {
            $variables = [];
        }
        //get template if it was not overwritten on item level
        if(!array_key_exists('template',$variables)) {
            $variables['template'] = ConfigManager::getInstance()->getOverwrittenGlobalTemplates($template,$variables);
        }
        $variables = HookManager::trigger("alter_template_data_".str_replace(['/','.twig'],['_',''],$variables['template']),[
            'variables' => $variables
        ],'variables');
        $variables = HookManager::trigger("alter_template_data_all",[
            'variables' => $variables
        ],'variables');
        return $variables;
    }

    public static function preprocessCssClasses($defaultClasses,$template,$overwrittenClasses) {
        if($overwrittenClasses !== NULL) {
            return $overwrittenClasses;
        }
        //check for global overwrittes
        return ConfigManager::getInstance()->getOverwrittenGlobalClasses($template,$defaultClasses);
    }

}