<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\MemberOfGroupInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\PromotableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\RelatedLinkContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ImageRepository")
 */
class Ancestry implements MemberOfGroupInterface, RelatedLinkContextInterface, PromotableInterface
{

    /**
     * @var CoreEntity|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=true, unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(name: "core_entity_id", referencedColumnName: "id", nullable=true)
     */
    private $coreEntityId;

    /**
     * @var CoreEntity|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer",  nullable=true, unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(name: "ancestor_id", referencedColumnName: "id", nullable=true)
     */
    private $ancestorId;

    public function __construct(int $ancestorId, int $coreEntityId)
    {
        $this->ancestorId = $ancestorId;
        $this->coreEntityId = $coreEntityId;
    }

    public function getAncestorId(): ?int
    {
        return $this->ancestorId;
    }

    public function getCoreEntityId(): ?int
    {
        return $this->coreEntityId;
    }
}
