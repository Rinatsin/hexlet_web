<?php

use function Stringy\create as s;
use Slim\Factory\AppFactory;
use DI\Container;

require '/composer/vendor/autoload.php';

$users = App\Generator::generate(100);

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
$app->get('/users', function ($request, $response) use ($users) {
    $term = $request->getQueryParam('term');
    if (!empty($term)) {
        $users = array_filter($users, function ($user) use ($term) {
            $lowerTerm = s($term)->toLowercase();
            return s($user['firstName'])->toLowerCase()->contains($lowerTerm);
        });
    }
    $params = ['users' => $users,
               'term' => $term
              ];
    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
});
// END

$app->run();

