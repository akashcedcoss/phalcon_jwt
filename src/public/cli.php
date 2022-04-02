<?php

declare(strict_types=1);
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
// use Exception;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Mysql;

// use Throwable;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH .'/app');

$loader = new Loader();
$loader->registerNamespaces(
    [
       'App\Console' => BASE_PATH . '/app/console',
    ]
);

$loader->register();

$container  = new CliDI();
$dispatcher = new Dispatcher();

$dispatcher->setDefaultNamespace('App\Console');
$container->setShared('dispatcher', $dispatcher);

// $container->setShared('config', function () {
//     return include 'app/config/config.php';
// });
$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
        APP_PATH . "/listners/",
    ],
    
);
$config = new Config([]);

$console = new Console($container);

$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

$container->set(
    'config',
    $config,
    true

);

$container->set(
    'db',
    function () {
        $config = $this->get('config');

        return new Mysql(
            [
                'host'=> 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'events',
                ]
            );
        }
);
try {
    $console->handle($arguments);
} catch (PhalconException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}