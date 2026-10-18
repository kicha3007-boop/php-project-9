<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use GuzzleHttp\Client;
use Hexlet\Code\Database;
use Hexlet\Code\PageChecker;
use Hexlet\Code\UrlCheckRepository;
use Hexlet\Code\UrlNormalizer;
use Hexlet\Code\UrlRepository;
use Hexlet\Code\UrlValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;

session_start();

$container = new Container();
$container->set(PDO::class, fn() => Database::connect((string) getenv('DATABASE_URL')));
$container->set(Messages::class, fn() => new Messages());
$container->set(Client::class, fn() => new Client(['timeout' => PageChecker::REQUEST_TIMEOUT]));
$container->set(PhpRenderer::class, function (Container $container) {
    $renderer = new PhpRenderer(__DIR__ . '/../templates');
    $renderer->setLayout('layout.phtml');
    $renderer->addAttribute('flash', $container->get(Messages::class));

    return $renderer;
});

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addRoutingMiddleware();

// Ссылки в шаблонах строятся по именам маршрутов
$container->get(PhpRenderer::class)->addAttribute('router', $app->getRouteCollector()->getRouteParser());

$displayErrorDetails = filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);
$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (Request $request) use ($app, $container) {
        $response = $app->getResponseFactory()->createResponse(404);

        return $container->get(PhpRenderer::class)->render($response, 'errors/404.phtml');
    }
);
if (!$displayErrorDetails) {
    $errorMiddleware->setDefaultErrorHandler(function (Request $request, Throwable $exception) use ($app, $container) {
        error_log((string) $exception);
        $response = $app->getResponseFactory()->createResponse(500);

        return $container->get(PhpRenderer::class)->render($response, 'errors/500.phtml');
    });
}

$app->get('/', function (Request $request, Response $response) {
    return $this->get(PhpRenderer::class)->render($response, 'index.phtml', ['url' => '']);
})->setName('home');

$app->post('/urls', function (Request $request, Response $response) {
    $rawUrl = trim((string) ($request->getParsedBody()['url'] ?? ''));
    $error = UrlValidator::validate($rawUrl);
    if ($error !== null) {
        $this->get(Messages::class)->addMessageNow('danger', $error);

        return $this->get(PhpRenderer::class)
            ->render($response->withStatus(422), 'index.phtml', ['url' => $rawUrl]);
    }

    $urls = $this->get(UrlRepository::class);
    $flash = $this->get(Messages::class);
    $name = UrlNormalizer::normalize($rawUrl);
    $existing = $urls->findByName($name);
    if ($existing !== null) {
        $flash->addMessage('info', 'Страница уже существует');
        $id = $existing['id'];
    } else {
        $id = $urls->create($name);
        $flash->addMessage('success', 'Страница успешно добавлена');
    }

    $routeParser = RouteContext::fromRequest($request)->getRouteParser();

    return $response->withHeader('Location', $routeParser->urlFor('urls.show', ['id' => (string) $id]))
        ->withStatus(302);
})->setName('urls.store');

$app->get('/urls', function (Request $request, Response $response) {
    $urls = $this->get(UrlRepository::class)->allWithLastCheck();

    return $this->get(PhpRenderer::class)->render($response, 'urls/index.phtml', ['urls' => $urls]);
})->setName('urls.index');

$app->get('/urls/{id:[0-9]+}', function (Request $request, Response $response, array $args) {
    $url = $this->get(UrlRepository::class)->find((int) $args['id']);
    if ($url === null) {
        throw new HttpNotFoundException($request);
    }
    $checks = $this->get(UrlCheckRepository::class)->findByUrlId($url['id']);

    return $this->get(PhpRenderer::class)
        ->render($response, 'urls/show.phtml', ['url' => $url, 'checks' => $checks]);
})->setName('urls.show');

$app->post('/urls/{id:[0-9]+}/checks', function (Request $request, Response $response, array $args) {
    $url = $this->get(UrlRepository::class)->find((int) $args['id']);
    if ($url === null) {
        throw new HttpNotFoundException($request);
    }

    $check = $this->get(PageChecker::class)->check($url['name']);
    $flash = $this->get(Messages::class);
    if ($check === null) {
        $flash->addMessage('danger', 'Произошла ошибка при проверке, не удалось подключиться');
    } else {
        $this->get(UrlCheckRepository::class)->create($url['id'], $check);
        $flash->addMessage('success', 'Страница успешно проверена');
    }

    $routeParser = RouteContext::fromRequest($request)->getRouteParser();

    return $response->withHeader('Location', $routeParser->urlFor('urls.show', ['id' => (string) $url['id']]))
        ->withStatus(302);
})->setName('urls.checks.store');

$app->run();
