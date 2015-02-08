<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use MacFJA\DoctrineTestSet\Ecommerce\FixtureLoader;

$loader = require_once(__DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

$paths = array(realpath(__DIR__.'/Ecommerce/Entity/'));

$cache = new ArrayCache();

$driver = new AnnotationDriver(new AnnotationReader(), $paths);

$config = Setup::createAnnotationMetadataConfiguration($paths, true);
$config->setMetadataCacheImpl( $cache );
$config->setQueryCacheImpl( $cache );
$config->setMetadataDriverImpl( $driver );


$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__.DIRECTORY_SEPARATOR.'ecommerce.sqlite',
);

$entityManager = EntityManager::create($conn, $config);

$fixtureLoader = new FixtureLoader();
$fixtureLoader->setEntityManager($entityManager);
$fixtureLoader->injectData();