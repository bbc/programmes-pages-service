<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class RefAppwProgramme
{
    use TimestampableEntity;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ProgrammeItem
     *
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $programmeItem;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $sequencer;

    public function __construct(
        ProgrammeItem $programmeItem,
        ?string $sequencer
    ) {
        $this->programmeItem = $programmeItem;
        $this->sequencer = $sequencer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgrammeItem(): ProgrammeItem
    {
        return $this->programmeItem;
    }

    public function getSequencer(): ?string
    {
        return $this->sequencer;
    }

    public function setSequencer(?string $sequencer): void
    {
        $this->sequencer = $sequencer;
    }
}
