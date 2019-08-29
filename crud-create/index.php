<?php

use Slim\Factory\AppFactory;
use DI\Container;

require '/composer/vendor/autoload.php';

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$repo = new App\Repository();
$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
});

$app->get('/posts', function ($request, $response) use ($repo) {
    $flash = $this->get('flash')->getMessages();

    $params = [
        'flash' => $flash,
        'posts' => $repo->all()
    ];
    return $this->get('renderer')->render($response, 'posts/index.phtml', $params);
})->setName('posts');

// BEGIN (write your solution here)
$app->get('/posts/new', function ($request, $response) {
    $params = [
        'postData' => [],
        'errors' => []
    ];
    return $this->get('renderer')->render($response, 'posts/new.phtml', $params);
})->setName('newPost');

$app->post('/posts', function ($request, $response) use ($router, $repo) {
    //получаем данные из формы
    $postData = $request->getParsedBodyParam('post');
    //проверяем коректность полученных данных
    $validator = new App\Validator();

    $errors = $validator->validate($postData);

    if (count($errors) === 0) {
        //если данные корректны то сохраняем, добавляем флеш и выполняем редирект
        $repo->save($postData);
        $this->get('flash')->addMessage('success', 'Post has been created');
        //редирект
        $url = $router->urlFor('posts');
        return $response->withRedirect($url);
    }

    $params = [
        'postData' => $postData,
        'errors' => $errors
    ];

    //если возникли ошибки то устанавливаем код ответа 422 и рендерим форму с указанием ошибок

    $response = $response->withStatus(422);
    return $this->get('renderer')->render($response, '/posts/new.phtml', $params);
});
// END

$app->run();
