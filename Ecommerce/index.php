<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;

$loader = require_once(__DIR__.DIRECTORY_SEPARATOR.'../vendor'.DIRECTORY_SEPARATOR.'autoload.php');

$paths = array(realpath(__DIR__.'/Entity/'));

$cache = new ArrayCache();

$driver = new AnnotationDriver(new AnnotationReader(), $paths);

$config = Setup::createAnnotationMetadataConfiguration($paths, true);
$config->setMetadataCacheImpl( $cache );
$config->setQueryCacheImpl( $cache );
$config->setMetadataDriverImpl( $driver );


$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__.DIRECTORY_SEPARATOR.'../ecommerce.sqlite',
);

$entityManager = EntityManager::create($conn, $config);

/** @var \MacFJA\DoctrineTestSet\Ecommerce\Entity\Product[] $products */
$products = $entityManager->createQuery('select e from MacFJA\DoctrineTestSet\Ecommerce\Entity\Product e')->getResult();
echo '<html><head><meta charset="utf-8"></head><body>';
foreach($products as $product) {
    echo '<h1>'.$product->getName().' <small>('.sprintf('%013d', $product->getEan()).')</small></h1>';
    $cats = array();
    foreach($product->getCategories() as $category) {
        $cats[] = $category->getBreadcrumb();
    }
    echo '<h2>'.implode(', ', $cats).'</h2>';
    if($product->getImage()) {
        echo '<img style="float:left" src="data:image/jpeg;base64,'.base64_encode(stream_get_contents($product->getImage()->getThumbnail())).'"/>';
    }
    echo '<p>'.nl2br($product->getDescription()).'</p>';
    echo '<p style="font-weight: bold">$'.$product->getPrice().'</p>';
    echo '<hr style="clear: both">';
}
echo '</body></html>';