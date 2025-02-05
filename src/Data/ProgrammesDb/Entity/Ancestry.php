<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AncestryRepository")
 */
class Ancestry
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id", onDelete="NO ACTION")
     */
    private $coreEntityId;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id", onDelete="NO ACTION")
     */
    private $ancestorId;

    public function __construct(int $ancestorId, int $coreEntityId)
    {
        $this->ancestorId = $ancestorId;
        $this->coreEntityId = $coreEntityId;
    }

    public function getAncestorId(): int
    {
        return $this->ancestorId;
    }

    public function getCoreEntityId(): int
    {
        return $this->coreEntityId;
    }

    public function setAncestorId(int $ancestorId): int
    {
        return $this->ancestorId = $ancestorId;
    }

    public function setCoreEntityId(int $coreEntityId): int
    {
        return $this->coreEntityId = $coreEntityId;
    }
}
