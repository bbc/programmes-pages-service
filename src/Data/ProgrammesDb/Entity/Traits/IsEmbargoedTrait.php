<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IsEmbargoedTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isEmbargoed = false;

    public function getIsEmbargoed(): bool
    {
        return $this->isEmbargoed;
    }

    public function setIsEmbargoed(bool $isEmbargoed)
    {
        $this->isEmbargoed = $isEmbargoed;
    }
}
