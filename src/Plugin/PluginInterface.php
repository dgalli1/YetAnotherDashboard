<?php
namespace App\Plugin;

interface PluginInterface {

    public function init(array $config);

    public function registerHooks() :array;

}