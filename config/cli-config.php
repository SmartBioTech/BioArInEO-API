<?php

use App\Helpers\DateTimeJsonType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__ . '/../vendor/autoload.php';

Type::overrideType('datetime', DateTimeJsonType::class);
Type::overrideType('datetimetz', DateTimeJsonType::class);

$settings = include __DIR__ . '/../app/settings.php';
$settings = $settings['settings']['doctrine'];
$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
    $settings['meta']['entity_path'],
    $settings['meta']['auto_generate_proxies'],
    $settings['meta']['proxy_dir'],
    $settings['meta']['cache'],
    false
);

$config->addCustomStringFunction('TYPE', \App\Doctrine\ORM\Query\Functions\TypeFunction::class);

$em = \Doctrine\ORM\EntityManager::create($settings['connection'], $config);

return ConsoleRunner::createHelperSet($em);
