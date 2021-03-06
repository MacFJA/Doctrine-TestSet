<?php

namespace MacFJA\DoctrineTestSet\Ecommerce;

use Doctrine\Common\Persistence\ObjectManager;
use MacFJA\DoctrineTestSet\Ecommerce\Entity\Category;
use MacFJA\DoctrineTestSet\Ecommerce\Entity\Image;
use MacFJA\DoctrineTestSet\Ecommerce\Entity\Order;
use MacFJA\DoctrineTestSet\Ecommerce\Entity\OrderItem;
use MacFJA\DoctrineTestSet\Ecommerce\Entity\Product;
use MacFJA\DoctrineTestSet\Ecommerce\Model\Shipment;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class FixtureLoader {
    /** @var ObjectManager */
    protected $manager;
    protected $idMapper = array();

    public function load() {
        return Yaml::parse(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'fixtures.yml'));
    }

    /**
     * @param ObjectManager $entityManager
     */
    public function setEntityManager($entityManager) {
        $this->manager = $entityManager;
    }

    public function injectData() {
        $data = $this->load();
        //Start with categories
        $this->injectCategoriesData($data['MacFJA\DoctrineTestSet\Ecommerce\Entity\Category']);

        //Next product info with image
        $this->injectProductsData($data['MacFJA\DoctrineTestSet\Ecommerce\Entity\Product']);
        $this->clearTumbnails();

        $this->injectOrderData(
            $data['MacFJA\DoctrineTestSet\Ecommerce\Entity\Order'],
            $data['MacFJA\DoctrineTestSet\Ecommerce\Entity\OrderItem']
        );

        $this->manager->flush();
    }

    protected function injectCategoriesData($data) {
        // - Create all base data
        foreach($data as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $this->manager->persist($category);
            $this->manager->flush();//Need to flush directly to have the new id
            //Add id in idMapper
            $this->idMapper[$categoryData['id']] = $category->getId();
        }
        // - Create Parent relation
        foreach($data as $categoryData) {
            if (!array_key_exists($categoryData['parentId'], $this->idMapper)) {
                continue;
            }
            /** @var Category $category */
            $category = $this->getCategoryWithFixtureId($categoryData['id']);
            /** @var Category $parent */
            $parent = $this->getCategoryWithFixtureId($categoryData['parentId']);

            $category->setParent($parent);
            $this->manager->persist($category);
        }
    }

    protected function injectOrderData($ordersData, $orderItemData) {
        //Create orders
        foreach ($ordersData as $orderData) {
            $order = new Order();
            $order->setBillingAddress($orderData['billingAddress']);
            $order->setDeliverySelected(new \DateTime($orderData['delivery']['date']));
            $order->setPreferredDeliveryHour(new \DateTime($orderData['delivery']['hour']));
            $order->setIncrementId($orderData['incrementId']);
            $order->setPurchaseAt(new \DateTime($orderData['purchaseAt']));
            $order->setOrderedItems($this->getOrderItemForIncrementId($orderItemData, $orderData['incrementId']));
            $shipment = new Shipment();
            $shipment->setAddress($orderData['shipment']['address']);
            $shipment->setTransporter($orderData['shipment']['transporter']);
            $shipment->setCost($orderData['shipment']['cost']);
            $order->setShipping($shipment);
            $this->manager->persist($order);
        }
        $this->manager->flush();
    }

    protected function getOrderItemForIncrementId($data, $incrementId) {
        $items = array();
        foreach ($data as $itemData) {
            if($itemData['orderId'] == $incrementId) {
                $item = new OrderItem();
                $item->setQuantity($itemData['quantity']);
                $item->setTaxRate($itemData['taxRate']);
                $item->setProduct($this->getProductWithEan($itemData['productId']));
                $this->manager->persist($item);
                $items[] = $item;
            }
        }
        return $items;
    }

    protected function getProductWithEan($ean) {
        return $this->manager->getRepository('MacFJA\DoctrineTestSet\Ecommerce\Entity\Product')->findOneBy(array('ean' => $ean));
    }

    protected function injectProductsData($data) {
        foreach($data as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setDescription($productData['description']);
            $product->setEan($productData['ean']);
            $product->setEnabled(array_key_exists('categories', $productData));
            $product->setPrice($productData['price']);
            $tags = array('test', 'doctrine');

            if(array_key_exists('categories', $productData)) {
                foreach ($productData['categories'] as $categoryId) {
                    /** @var Category $category */
                    $category = $this->getCategoryWithFixtureId($categoryId);
                    $product->addCategory($category);
                    $tags[] = $category->getName();
                }
            }
            if(array_key_exists('image', $productData)) {
                $imagePath = __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'images'.$productData['image'];
                $thumbnailPath = __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'thumbnails'.$productData['image'];
                if(!is_dir(dirname($thumbnailPath))) {
                    mkdir(dirname($thumbnailPath), 0750, true);
                }
                $image = new Image();
                $image->setData(fopen($imagePath,'r'));
                if (!file_exists($thumbnailPath)) {
                    imagejpeg($this->getThumbnail($imagePath),$thumbnailPath);
                }
                $image->setThumbnail(fopen($thumbnailPath,'r'));
                $product->setImage($image);

            }
            $product->setTags($tags);
            $product->setFeatures(array_key_exists('features', $productData)?$productData['features']:array());
            $this->manager->persist($product);
        }
    }

    /**
     * Get the category with the mapped id
     * @param int $id
     * @return Category|null
     */
    protected function getCategoryWithFixtureId($id) {
        return $this->manager->find('MacFJA\DoctrineTestSet\Ecommerce\Entity\Category', $this->idMapper[$id]);
    }

    protected function getThumbnail($filePath) {
        list($width, $height) = getimagesize($filePath);
        $newWidth = 90;
        $newHeight = $height * ($newWidth / $width);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        $source = imagecreatefromjpeg($filePath);

        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $thumb;
    }

    protected function clearTumbnails() {
        $finder = new Finder();
        $finder->in(__DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'thumbnails');
        foreach($finder->ignoreDotFiles(false)->files() as $file) {
            /** @var $file SplFileInfo */
                unlink($file->getRealPath());
        }
        foreach($finder->sort(function($a,$b){return strlen($b->getRealPath())-strlen($a->getRealPath());})->directories() as $file) {
            /** @var $file SplFileInfo */
                rmdir($file->getRealPath());

        }
        rmdir(__DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'thumbnails');
    }
}