<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class FilterArticleAdmin
{
    private $shop;

    /**
     * @return mixed
     */
    public function getShop(): mixed
    {
        return $this->shop;
    }

    /**
     * @param mixed $shop
     */
    public function setShop(mixed $shop): void
    {
        $this->shop = $shop;
    }
}
