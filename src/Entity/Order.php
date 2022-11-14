<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=OrderArticle::class, mappedBy="order", cascade={"remove"})
     */
    private $orderArticles;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Assert\DateTime
     * @ORM\Column(type="date")
     */
    private $date_ordered;

    /**
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @Assert\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_retrieve;

    public function __construct()
    {
        $this->orderArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|OrderArticle[]
     */
    public function getOrderArticles(): Collection
    {
        return $this->orderArticles;
    }

    public function addOrderArticle(OrderArticle $orderArticle): self
    {
        if (!$this->orderArticles->contains($orderArticle)) {
            $this->orderArticles[] = $orderArticle;
        }

        return $this;
    }

    public function removeOrderArticle(OrderArticle $orderArticle): self
    {
        $this->orderArticles->removeElement($orderArticle);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateOrdered(): ?\DateTime
    {
        return $this->date_ordered;
    }

    public function setDateOrdered(\DateTime $date_ordered): self
    {
        $this->date_ordered = $date_ordered;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getDateRetrieve(): ?\DateTime
    {
        return $this->date_retrieve;
    }

    public function setDateRetrieve(?\DateTime $date_retrieve): self
    {
        $this->date_retrieve = $date_retrieve;

        return $this;
    }
}
