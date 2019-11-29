<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 */
class Thing
{
    use TimestampableEntity;

    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string", length=36, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $preferredLabel;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $disambiguationHint;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getPreferredLabel(): string
    {
        return $this->preferredLabel;
    }

    public function setPreferredLabel(string $preferredLabel)
    {
        $this->preferredLabel = $preferredLabel;
    }

    public function getDisambiguationHint(): string
    {
        return $this->disambiguationHint;
    }

    public function setDisambiguationHint(string $disambiguationHint)
    {
        $this->disambiguationHint = $disambiguationHint;
    }
}
