<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;

class MasterBrand
{
    /** @var Mid */
    private $mid;

    /** @var string */
    private $name;

    /** @var Image */
    private $image;

    /** @var Network */
    private $network;

    /** @var Version|null */
    private $competitionWarning;

    public function __construct(
        Mid $mid,
        string $name,
        Image $image,
        Network $network,
        Options $options,
        ?Version $competitionWarning = null
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

    /**
     * @throws DataNotFetchedException
     */
    public function getNetwork(): Network
    {
        if ($this->network instanceof UnfetchedNetwork) {
            throw new DataNotFetchedException(
                'Could not get Network of MasterBrand "'
                    . $this->mid . '" as it was not fetched'
            );
        }
        return $this->network;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getOption(string $key)
    {
        return $this->options->getOption($key);
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getCompetitionWarning(): ?Version
    {
        if ($this->competitionWarning instanceof UnfetchedVersion) {
            throw new DataNotFetchedException(
                'Could not get Competition Warning of MasterBrand "'
                    . $this->mid . '" as it was not fetched'
            );
        }
        return $this->competitionWarning;
    }
}
