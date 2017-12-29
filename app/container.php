<?php

$container = $app->getContainer();

$container['debug'] = function() {
    return true;
};

$container['validator'] = function () {
    return new \App\Validation\Validator;
};

$container['csrf'] = function () {
    return new \Slim\Csrf\Guard;
};

$container['auth'] = function () use ($container) {
    return new \App\Middlewares\AuthMiddleware($container);
};

$container['upload_directory'] = dirname(__DIR__) . '/public/uploads';

$container['mailer'] = function ($container) {
    $transport = Swift_SmtpTransport::newInstance('absgroup.ro');
    $mailer = Swift_Mailer::newInstance($transport);
    return $mailer;
};

// Service factory for the ORM
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use($capsule) {
    return $capsule;
};

// Register component on container
$container['view'] = function ($container) {
    $dir = dirname(__DIR__);
    $view = new \Slim\Views\Twig($dir.'/resources/views', [
        'cache' =>  $container->debug ? false : $dir.'/tmp/views/cache',
        'debug' => $container->debug
    ]);

    if ($container->debug) {
        $view->addExtension(new Twig_Extension_Debug());
    }

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new Cocur\Slugify\Bridge\Twig\SlugifyExtension(Cocur\Slugify\Slugify::create()));
    $view['baseUrl'] = $container['request']->getUri()->getBaseUrl();

    return $view;
};