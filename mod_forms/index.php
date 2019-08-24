<?php

use Slim\Factory\AppFactory;
use DI\Container;

require '/composer/vendor/autoload.php';

$repo = new App\Repository();

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

$app->get('/courses', function ($request, $response) use ($repo) {
    $params = [
        'courses' => $repo->all()
    ];
return $this->get('renderer')->render($response, 'courses/index.phtml', $params);
});

// BEGIN (write your solution here)
$app->post('/courses', function ($request, $response) use ($repo) {
    $validator = new App\Validator();
    $course = $request->getParsedBodyParam('course');
    $errors = $validator->validate($course);
    if (empty($errors)) {
        $repo->save($course);
        return $response->withHeader('Location', '/courses')
        ->withStatus(302);
    }
    $params = [
        'course' => $course,
        'errors' => $errors
    ];
    return $this->get('renderer')->render($response, "courses/new.phtml", $params)
    ->withStatus(422);
});

$app->get('/courses/new', function ($request, $response) {
    $params = [
        'course' => [],
        'errors' => []
    ];
    return $this->get('renderer')->render($response, "courses/new.phtml", $params);
});
// END

$app->run();
 
