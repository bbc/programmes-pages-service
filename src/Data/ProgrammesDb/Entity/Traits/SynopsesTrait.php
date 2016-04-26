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

    /**
     * @return string
     */
    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    /**
     * @param string $shortSynopsis
     */
    public function setShortSynopsis($shortSynopsis)
    {
        $this->shortSynopsis = $shortSynopsis;
    }

    /**
     * @return string
     */
    public function getMediumSynopsis(): string
    {
        return $this->mediumSynopsis;
    }

    /**
     * @param string $mediumSynopsis
     */
    public function setMediumSynopsis($mediumSynopsis)
    {
        $this->mediumSynopsis = $mediumSynopsis;
    }

    /**
     * @return string
     */
    public function getLongSynopsis(): string
    {
        return $this->longSynopsis;
    }

    /**
     * @param string $longSynopsis
     */
    public function setLongSynopsis($longSynopsis)
    {
        $this->longSynopsis = $longSynopsis;
    }
}
