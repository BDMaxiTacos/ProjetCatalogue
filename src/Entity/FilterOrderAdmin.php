<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class FilterOrderAdmin
{
    private $state;

    /**
     * @return mixed
     */
    public function getState(): mixed
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState(mixed $state): void
    {
        $this->state = $state;
    }
}
