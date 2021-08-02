<?php

use App\AssetManager;
use App\CacheManager;
use App\ConfigManager;
use App\HookManager;
use App\Plugin\PluginManager;
use App\PreprocessTwig;
use App\RemoteHeaders;
use App\RoutingManager;
use App\WhitelistManager;
use Twig\TwigFilter;

require '../vendor/autoload.php';
$configManger = new ConfigManager();
new WhitelistManager($configManger->getIsDebug());
$hookManager = new HookManager();
new AssetManager($configManger->getIsDebug());
$cache = new CacheManager();
$plugin = new PluginManager();
$loader = new \Twig\Loader\FilesystemLoader([
    __DIR__."/../templates",
    __DIR__."/../config/custom-templates",
    ...$plugin->getTemplateFolders()
    ]
);

$twig = new \Twig\Environment($loader, [
    'cache' => $configManger->getIsDebug() ? false : __DIR__."/../cache",
    'debug' => $configManger->getIsDebug()
]);
$twig->addFilter(new TwigFilter('preprocess', function ($array, String $template = "") {
    return PreprocessTwig::preprocessTemplateVariables($array,$template);
}));
$filter = new \Twig\TwigFilter('preprocess_classes', function ($classes,$template,$overwritten) {
    return PreprocessTwig::preprocessCssClasses($classes, $template,$overwritten);
});
if($configManger->getIsDebug()) {
    $twig->addExtension(new \Twig\Extension\DebugExtension());
}
$twig->addFilter($filter);
// $remote = new RemoteHeaders();
$config = $configManger->getConfig();
$template = $twig->load('page.twig');

$config = HookManager::trigger('preprocess_config',[
    'template' => $template,
    'config' => $config
],'config');
if(count($_GET) > 0) {
    RoutingManager::handleRoute();
}

$rendered = $template->render(
    $config
);
HookManager::trigger('postprocess_html',[
    'template' => $template,
    'rendered_html' => $rendered
],'rendered_html');
echo $rendered;
echo '<style id="overwritteStyles"></style>';