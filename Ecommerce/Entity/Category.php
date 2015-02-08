<?php

namespace MacFJA\DoctrineTestSet\Ecommerce\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Category
 *
 * @author MacFJA
 *
 * @ORM\Table(name="category")
 * @ORM\Entity
 */
class Category {
    /**
     * The identifier of the category
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;
    /**
     * The category name
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * Product in the category
     * @var Product[]
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="categories")
     **/
    protected $products;
    /**
     * All children categories
     * @var Category[]
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     **/
    private $children;

    /**
     * The category parent
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;

    /**
     * Constructor of the Category class.
     * (Initialize array field)
     */
    function __construct()
    {
        //Initialize products as a Doctrine Collection
        $this->products = new ArrayCollection();
        //Initialize children as a Doctrine Collection
        $this->children = new ArrayCollection();
    }

    /**
     * Add a product in the category
     * @param $product Product The product to associate
     */
    public function addProduct($product) {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }
    }

    /**
     * @param Product $product
     */
    public function removeProduct($product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Set the name of the category
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name of the category
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the parent category
     * @param Category $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the parent category
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get all children categories
     * @return Category[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get the id of the category.
     * Return null if the category is new and not saved
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return all product associated to the category
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set all products in the category
     * @param Product[] $products
     */
    public function setProducts($products) {
        $this->products->clear();
        $this->products = new ArrayCollection($products);
    }

    /** {@inheritdoc} */
    function __toString()
    {
        return $this->getName();
    }


}