<?php
/*
 * Реализуйте Маршрут /companies/{id}, по которому отдается json представление
 * компании. Компания извлекается из списка $companies. Каждая компания 
 * представлена массивом у которого есть текстовый (то есть тип данных - 
 * строка) ключ id.

	Если компания с таким идентификатором не существует, то сайт должен 
	вернуть ошибку 404 (страница с HTTP кодом 404) и текстом Page not found.

Подсказки

-Для поиска нужной компании в списке компаний, воспользуйтесь методом firstWhere, 
библиотеки Collection
-Статус ответа выставляется методом $response->withStatus($status)
 */
use Slim\Factory\AppFactory;
use Illuminate\Support\Collection;

require '/composer/vendor/autoload.php';

$companies = App\Generator::generate(100);

/* $configuration = [ */
/*     'settings' => [ */
/*         'displayErrorDetails' => true, */
/*     ], */
/* ]; */

/* $app = new \Slim\App($configuration); */
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response, $args) {
    return $response->write('open something like (you can change id): /companies/5');
});

// BEGIN (write your solution here)
$app->get('/companies/{id}', function ($request, $response, $args) use ($companies) {
    $id = $args['id'];
    $company = collect($companies)->firstWhere('id', $id);
    if (!$company) {
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found');
    }
        return  $response->withJson($company);
});
// END

$app->run();

