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
    use TimestampableEntity;
    use Traits\IsEmbargoedTrait;
    use Traits\PartnerPidTrait;
    use Traits\SynopsesTrait;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=true, unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true)
     */
    private $coreEntityId;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer",  nullable=true, unique=true)
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true)
     */
    private $ancestorId;

    public function __construct(string $ancestorId, string $coreEntityId)
    {
        $this->ancestorId = $ancestorId;
        $this->coreEntityId = $coreEntityId;
    }

    public function getAncestorId(): ?int
    {
        return $this->ancestorId;
    }

    public function getCoreEntityId(): string
    {
        return $this->coreEntityId;
    }

    public function setAncestorId(string $pid): void
    {
        $this->ancestorId = $pid;
    }

    public function setCoreEntityId(string $pid): void
    {
        $this->coreEntityId = $pid;
    }

}
