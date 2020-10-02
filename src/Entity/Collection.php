<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Collection
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $api_id;

    /**
     * @ORM\Column(name="dateCreatedAt", type="datetime", nullable=true)
     */
    private $dateCreatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="collections")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getApiId()
    {
        return $this->api_id;
    }

    /**
     * @param mixed $api_id
     */
    public function setApiId($api_id): void
    {
        $this->api_id = $api_id;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreatedAt()
    {
        return $this->dateCreatedAt;
    }

    /**
     * @param mixed $dateCreatedAt
     */
    public function setDateCreatedAt($dateCreatedAt): void
    {
        $this->dateCreatedAt = $dateCreatedAt;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }
}
