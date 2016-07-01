<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces;

interface StreamableClipCountableInterface
{

    public function getAvailableClipsCount(): int;

    public function setAvailableClipsCount(int $availableClipsCount);

    public function getAncestry();
}
