<?php
/*
 * Реализуйте Slim приложение, в котором по адресу / отдается строчка Welcome to Hexlet!
*/
namespace App;

require '/composer/vendor/autoload.php';

use Slim\Factory\AppFactory;

// BEGIN (write your solution here)
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->get('/', function ($request, $response) {
    return $response->write('Welcome to Hexlet!');
});
$app->run();

