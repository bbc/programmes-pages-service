<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Ancestry
{
    /** @var int */
    private $coreEntityId;

    /** @var int */
    private $ancestorId;

    public function __construct(
      int $coreEntityId,
      int $ancestorId
    ) {
      $this->coreEntityId = $coreEntityId;
      $this->ancestorId = $ancestorId;
    }

    public function getCoreEntityId(): int
    {
        return $this->coreEntityId;
    }

    public function getAncestorId(): int
    {
        return $this->ancestorId;
    } 
}
