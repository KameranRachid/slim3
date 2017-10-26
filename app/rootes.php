<?php
// front
$app->get('/', \App\Controllers\Front\FrontController::class.':index')->setName('index');

$app->get('/cere-oferta', \App\Controllers\Front\FrontController::class.':offer')->setName('offer');
$app->post('/cere-oferta', \App\Controllers\Front\FrontController::class.':postOffer');

$app->post('/newsletter', \App\Controllers\Front\FrontController::class.':newsletter')->setName('newsletter');

$app->get('/contact', \App\Controllers\Front\FrontController::class.':contact')->setName('contact');
$app->post('/contact', \App\Controllers\Front\FrontController::class.':postContact');

$app->get('/noutati-legislative', \App\Controllers\Front\FrontController::class.':legislative')->setName('legislative');

$app->get('/servicii[/{slug}]', \App\Controllers\Front\FrontController::class.':service')->setName('service');


// admin
$app->group('/admin', function() {
    $this->get('/post[/{id}]', \App\Controllers\Dashboard\PostFormController::class.':getPost')->setName('post');
    $this->post('/post', \App\Controllers\Dashboard\PostFormController::class.':savePost');

    $this->get('/home', \App\Controllers\Dashboard\PostListController::class.':home')->setName('home');
    $this->post('/changeStatus', \App\Controllers\Dashboard\PostListController::class.':changeStatus');
    $this->post('/deletePost', \App\Controllers\Dashboard\PostListController::class.':deletePost');

    $this->get('/signout', \App\Controllers\Dashboard\AuthController::class.':getSignOut')->setName('signout');

    $this->get('/subscriptions', \App\Controllers\Dashboard\ClientListController::class.':show')->setName('clients');

})->add(new \App\Middlewares\AuthMiddleware($container));


$app->group('/admin', function() use ($container) {
    $this->get('', \App\Controllers\Dashboard\AuthController::class.':getSignIn')->setName('signin')
        ->add(new \App\Middlewares\TwigCsrfMiddleware($container->view->getEnvironment(), $container->csrf));
    $this->get('/signin', \App\Controllers\Dashboard\AuthController::class.':getSignIn')->setName('signin')
        ->add(new \App\Middlewares\TwigCsrfMiddleware($container->view->getEnvironment(), $container->csrf));

    $this->post('/signin', \App\Controllers\Dashboard\AuthController::class.':postSignIn');
    $this->post('', \App\Controllers\Dashboard\AuthController::class.':postSignIn');
})->add($container->csrf);
