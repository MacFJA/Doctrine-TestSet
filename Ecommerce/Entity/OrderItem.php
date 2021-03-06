<?php


namespace MacFJA\DoctrineTestSet\Ecommerce\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OrderItem
 *
 * @author MacFJA
 *
 * @ORM\Table(name="order_item")
 * @ORM\Entity
 */
class OrderItem {
    /**
     * The identifier of the image
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;
    /**
     * The ordered quantity
     * @var integer
     * @ORM\Column(type="smallint")
     */
    protected $quantity = 1;
    /**
     * The tax rate to apply on the product
     * @var string
     * @ORM\Column(type="decimal", name="tax_rate")
     */
    protected $taxRate = 0.21;
    /**
     * The ordered product
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    protected $product;

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param string $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     * @return string
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    function __toString()
    {
        return $this->getProduct()->getName().' [x'.$this->getQuantity().']: '.$this->getTotalPrice();
    }

    /**
     * Return the total price (tax included)
     * @return float
     */
    public function getTotalPrice() {
        return $this->product->getPrice() * $this->quantity * (1 + $this->taxRate);
    }
}