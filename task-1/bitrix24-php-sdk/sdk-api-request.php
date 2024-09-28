<?php

declare(strict_types=1);

use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

require_once '../vendor/autoload.php';

try {
    $logger = new Logger('task-1-app');
    $logger->pushHandler(new StreamHandler('b24-api-client-debug.log', Level::Debug));

    $b24Service = ServiceBuilderFactory::createServiceBuilderFromWebhook(
        webhookUrl: 'https://b24-ppqfwe.bitrix24.ru/rest/1/uy41llabw8evqdnx/',
        logger: $logger
    );

    var_dump($b24Service->getMainScope()->main()->getCurrentUserProfile()->getUserProfile());
    var_dump($b24Service->core->call('user.current')->getResponseData()->getResult());
} catch (InvalidArgumentException $exception) {
    print(sprintf('ERROR IN CONFIGURATION OR CALL ARGS: %s', $exception->getMessage()).PHP_EOL);
    print($exception::class.PHP_EOL);
    print($exception->getTraceAsString());
} catch (Throwable $throwable) {
    print(sprintf('FATAL ERROR: %s', $throwable->getMessage()).PHP_EOL);
    print($throwable::class.PHP_EOL);
    print($throwable->getTraceAsString());
}
