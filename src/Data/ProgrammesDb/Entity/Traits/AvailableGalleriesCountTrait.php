<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AvailableGalleriesCountTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $availableGalleriesCount = 0;

    public function getAvailableGalleriesCount(): int
    {
        return $this->availableGalleriesCount;
    }

    public function setAvailableGalleriesCount(int $availableGalleriesCount)
    {
        $this->availableGalleriesCount = $availableGalleriesCount;
    }
}
