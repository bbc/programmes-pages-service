<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait OptionsTrait
{
    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $options;

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(string $shortSynopsis): void
    {
        $this->shortSynopsis = $shortSynopsis;
    }
}
