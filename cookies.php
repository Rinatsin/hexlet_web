Задание
public/index.php
Реализуйте два обработчика

POST /cart-items для добавления товаров в корзину
DELETE /cart-items для очистки корзины
Корзина должна храниться на клиенте в куках. Кроме самого товара, необходимо
хранить количество единиц. Повторное добавление того же товара приводит к
увеличению счетчика и редиректу на главную. Подробнее смотрите в шаблоне.
Для сериализации данных используйте json_encode.
<?php
<?php

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;

require '/composer/vendor/autoload.php';

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->add(MethodOverrideMiddleware::class);
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) {
    $cart = json_decode($request->getCookieParam('cart', json_encode([])), true);
    $params = [
        'cart' => $cart
    ];
    return $this->get('renderer')->render($response, 'index.phtml', $params);
});

// BEGIN (write your solution here)
$app->post('/cart-items', function ($request, $response) {
    //get form data
    $item = $request->getParsedBodyParam('item');
    //get cart values
    $cart = json_decode($request->getCookieParam('cart', json_encode([])), true);
    //add items in cart
    $countItems = collect($cart)->filter(function ($value) use ($item) {
        return $value['id'] === $item['id'];
    })->count();
    $item['count'] = $countItems + 1;
    $cart[] = $item;
    //encode cart
    $encodedCart = json_encode($cart);
    //install new cart in the cookies
    return $response->withHeader('Set-Cookie', "cart={$encodedCart}")->withRedirect('/');
});

$app->delete('/cart-items', function ($request, $response) {
    $cart = [];
    $encodedCart = json_encode($cart);
    return $response->withHeader('Set-Cookie', "cart={$encodedCart}")->withRedirect('/');
});
// END

$app->run();

/*Решенеие учителя
 * $app->post('/cart-items', function ($request, $response) {
    $item = $request->getParsedBodyParam('item');
    $cart = json_decode($request->getCookieParam('cart', json_encode([])), true);

    $id = $item['id'];
    if (!isset($cart[$id])) {
        $cart[$id] = ['name' => $item['name'], 'count' => 1];
    } else {
        $cart[$id]['count'] += 1;
    }

    $encodedCart = json_encode($cart);
    return $response->withHeader('Set-Cookie', "cart={$encodedCart}")
        ->withRedirect('/');
});

$app->delete('/cart-items', function ($request, $response) {
    $encodedCart = json_encode([]);
    return $response->withHeader('Set-Cookie', "cart={$encodedCart}")
        ->withRedirect('/');
});
// END
 */

/*содержимое файла index phtml
 *
 * <form action="/cart-items" method="post">
    <input type="hidden" name="item[id]" value="1">
    <input type="hidden" name="item[name]" value="One">
    One
    <input type="submit" value="Add">
</form>

<form action="/cart-items" method="post">
    <input type="hidden" name="item[id]" value="2">
    <input type="hidden" name="item[name]" value="Two">
    Two
    <input type="submit" value="Add">
</form>

<form action="/cart-items" method="post">
    <input type="hidden" name="_METHOD" value="DELETE">
    <input type="submit" value="Clean">
</form>

<?php if (count($cart) == 0) : ?>
    <div>Cart is empty</div>
<?php else : ?>
    <?php foreach ($cart as $item) : ?>
        <div>
            <?= htmlspecialchars($item['name']) ?>: <?= htmlspecialchars($item['count']) ?>         
        </div>
    <?php endforeach ?>
<?php endif ?>
 */
