<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SynopsesTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $shortSynopsis = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $mediumSynopsis = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $longSynopsis = '';

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function setShortSynopsis(string $shortSynopsis): void
    {
        $this->shortSynopsis = $shortSynopsis;
    }

    public function getMediumSynopsis(): string
    {
        return $this->mediumSynopsis;
    }

    public function setMediumSynopsis(string $mediumSynopsis): void
    {
        $this->mediumSynopsis = $mediumSynopsis;
    }

    public function getLongSynopsis(): string
    {
        return $this->longSynopsis;
    }

    public function setLongSynopsis(string $longSynopsis)
    {
        $this->longSynopsis = $longSynopsis;
    }
}
