<?php

use App\alterIncludeTokenParser;
use App\CacheManager;
use App\ConfigManager;
use App\HookManager;
use App\Plugin\PluginManager;
use App\PreprocessTwig;
use App\RemoteHeaders;
use App\WhitelistManager;
use Twig\TwigFilter;
use Twig\TwigFunction;
function bratwurst() {

}
require '../vendor/autoload.php';

$configManger = new ConfigManager();
new WhitelistManager(true);
$hookManager = new HookManager();
$plugin = new PluginManager();
$loader = new \Twig\Loader\FilesystemLoader([
    __DIR__."/../templates",
    __DIR__."/../config/overwrites"
    ]
);
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);
$twig->addFilter(new TwigFilter('preprocess', function ($array, String $template = "") {
    return PreprocessTwig::preprocessTemplateVariables($array,$template);
}));
$filter = new \Twig\TwigFilter('preprocess_classes', function ($classes,$template,$overwritten) {
    return PreprocessTwig::preprocessCssClasses($classes, $template,$overwritten);
});
$twig->addExtension(new \Twig\Extension\DebugExtension());
new CacheManager();
$twig->addFilter($filter);
$remote = new RemoteHeaders();
$config = $remote->filter($configManger);
$template = $twig->load('page.twig');
if(array_key_exists('generate_missing_keys',$_GET)) {
    $configManger->generateMissingConfigKeys(true);
}
$config = HookManager::trigger('preprocess_config',[
    'template' => $template,
    'config' => $config
],'config');
$rendered = $template->render(
    $config
);

HookManager::trigger('postprocess_html',[
    'template' => $template,
    'rendered_html' => $rendered
],'rendered_html');
echo $rendered;