<?php

use App\ConfigManager;
use App\RemoteHeaders;

require '../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__."/../templates");
$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ ."/../cache/",
]);
$configManger = new ConfigManager();
$remote = new RemoteHeaders();
$config = $remote->filter($configManger);
$template = $twig->load('dashboard.twig');
echo $template->render(
    $config
);