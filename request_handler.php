<?php
/*
 * Добавьте два обработчика:

/phones - возвращает список телефонов содержащихся в переменной $phones закодированный в json
/domains - возвращает список доменов содержащихся в переменной $domains закодированный в json
Подсказки
Кодирование в json: json_encode($data)
Чтобы получить данные внутри обработчиков, воспользуйтесь замыканием (для телефонов: use
($phones)).
 */

namespace App;

require '/composer/vendor/autoload.php';

$faker = \Faker\Factory::create();
$faker->seed(1234);

$domains = [];
for ($i = 0; $i < 10; $i++) {
    $domains[] = $faker->domainName;
}

$phones = [];
for ($i = 0; $i < 10; $i++) {
    $phones[] = $faker->phoneNumber;
}

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$app->get('/', function ($request, $response) {
	    $response->write('go to the /phones or /domains');
});

// BEGIN (write your solution here)
$app->get('/phones', function ($request, $response) use ($phones) {
     	return $response->write(json_encode($phones));
});

$app->get('/domains', function ($request, $response) use ($domains) {
         return  $response->write(json_encode($domains));
});
         // END

$app->run();

