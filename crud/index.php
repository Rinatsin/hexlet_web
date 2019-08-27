<?php

use Slim\Factory\AppFactory;
use DI\Container;

require '/composer/vendor/autoload.php';

$posts = App\Generator::generate(100);

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
});

// BEGIN (write your solution here)
$app->get('/posts', function ($request, $response) use ($posts) {
    $page = $request->getQueryParam('page', 1);
    $per = $request->getQueryParam('per', 5);
    $offset = ($page - 1) * $per;
    $posts = collect($posts)->slice($offset, $per)->all();
    $params = [
        'posts' => $posts,
        'page' => $page
    ];
    return $this->get('renderer')->render($response, "posts/index.phtml", $params);
})->setName('posts');

$app->get('/posts/{slug}', function ($request, $response, $args) use ($posts) {
    $slug = $args['slug'];
    $post = collect($posts)->firstWhere('slug', $slug);
    if (!isset($post)) {
        $response->getBody()->write('Page not found');
        return $response->withStatus(404);
    }
    $params = [
        'post' => $post
    ];
    return $this->get('renderer')->render($response, "posts/show.phtml", $params);
})->setName('posts');
// END

$app->run();

