<?php
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;

// replace with mechanism to retrieve EntityManager in your app
$paths = array(realpath(__DIR__.'/Ecommerce/Entity/'));

$cache = new ArrayCache();

$driver = new AnnotationDriver(new AnnotationReader(), $paths);

$config = Setup::createAnnotationMetadataConfiguration($paths, true);
$config->setMetadataCacheImpl( $cache );
$config->setQueryCacheImpl( $cache );
$config->setMetadataDriverImpl( $driver );


$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => ':memory:',
);

$entityManager = EntityManager::create($conn, $config);


return ConsoleRunner::createHelperSet($entityManager);