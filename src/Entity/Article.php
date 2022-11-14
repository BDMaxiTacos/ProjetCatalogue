<?php

namespace App\Entity;

use App\Entity\Shop;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="integer", scale="2")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_available;

    /**
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity=OrderArticle::class, mappedBy="article")
     */
    private $orderArticles;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->orderArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStockAvailable(): ?int
    {
        return $this->stock_available;
    }

    public function setStockAvailable(int $stock_available): self
    {
        $this->stock_available = $stock_available;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop_id): self
    {
        $this->shop = $shop_id;

        return $this;
    }

    /**
     * @return Collection|OrderArticle[]
     */
    public function getOrderArticles(): Collection
    {
        return $this->orderArticles;
    }

    public function addOrderArticles(OrderArticle $orderArticle): self
    {
        if (!$this->orderArticles->contains($orderArticle)) {
            $this->orderArticles[] = $orderArticle;
        }

        return $this;
    }

    public function removeOrder(OrderArticle $orderArticle): self
    {
        if ($this->orderArticles->removeElement($orderArticle)) {
            $orderArticle->removeArticle($this);
        }

        return $this;
    }
}
