<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

class Creneau
{
    private $date_retrieve;

    public function getDateRetrieve(): string
    {
        return $this->date_retrieve;
    }

    public function setDateRetrieve($date_retrieve): self
    {
        $this->date_retrieve = $date_retrieve;
        return $this;
    }
}
