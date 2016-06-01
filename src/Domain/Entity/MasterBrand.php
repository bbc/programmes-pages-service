<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;

class MasterBrand
{
    /**
     * @var Mid
     */
    private $mid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Image
     */
    private $image;

    /**
     * @var Network
     */
    private $network;

    /**
     * @var Version|null
     */
    private $competitionWarning;


    public function __construct(
        Mid $mid,
        string $name,
        Image $image,
        Network $network,
        Version $competitionWarning = null
    ) {
        $this->mid = $mid;
        $this->name = $name;
        $this->image = $image;
        $this->network = $network;
        $this->competitionWarning = $competitionWarning;
    }

    public function getMid(): Mid
    {
        return $this->mid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getNetwork(): Network
    {
        return $this->network;
    }

    /**
     * @return Version|null
     */
    public function getCompetitionWarning()
    {
        return $this->competitionWarning;
    }
}
