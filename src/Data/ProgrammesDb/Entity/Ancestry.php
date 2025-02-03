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
     * @ORM\Column(type="integer", unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(name:"core_entity_id", referencedColumnName:"id")
     */
    private $coreEntityId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(name:"ancestor_id", referencedColumnName:"id")
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
}
