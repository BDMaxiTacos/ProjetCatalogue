<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class FilterShopAdmin
{
    private $search;

    /**
     * @return mixed
     */
    public function getSearch(): mixed
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch(mixed $search): void
    {
        $this->search = $search;
    }
}
