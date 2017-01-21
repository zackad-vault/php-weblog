<?php
// DIC configuration

$container = $app->getContainer();

// view twig renderer
$container['view'] = function ($c) {
    $settings = $c->get('settings')['twig'];
    $loader = new Twig_Loader_Filesystem($settings['template_path']);
    $view = new Twig_Environment($loader, [
        'debug' => $settings['debug'],
    ]);
    $view->addExtension(new Twig_Extension_Debug);
    $view->addExtension(new Jralph\Twig\Markdown\Extension(
        new Jralph\Twig\Markdown\Parsedown\ParsedownExtraMarkdown
    ));
    $view->addGlobal("debug", $settings['debug']);
    $view->addGlobal("session", $_SESSION);

    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
