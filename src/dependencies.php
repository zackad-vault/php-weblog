<?php
// DIC configuration

$container = $app->getContainer();

// Authentication
$container['auth'] = function ($c) {
    $auth = new Models\Auth;
    return $auth;
};

// Register Twig View helper
$container['view'] = function ($c) {
    $settings = $c->get('settings')['twig'];
    $view = new \Slim\Views\Twig($settings['template_path'], [
        // 'cache' => 'path/to/cache'
        'debug' => $settings['debug'],
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
    $view->addExtension(new Twig_Extension_Debug);
    $view->addExtension(new Jralph\Twig\Markdown\Extension(
        new Jralph\Twig\Markdown\Parsedown\ParsedownExtraMarkdown
    ));
    $view['debug'] = $settings['debug'];
    $view['session'] = $_SESSION;

    return $view;
};

// monolog
// $container['logger'] = function ($c) {
//     $settings = $c->get('settings')['logger'];
//     $logger = new Monolog\Logger($settings['name']);
//     $logger->pushProcessor(new Monolog\Processor\UidProcessor());
//     $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
//     return $logger;
// };
