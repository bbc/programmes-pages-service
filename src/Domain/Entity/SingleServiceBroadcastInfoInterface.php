<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

interface SingleServiceBroadcastInfoInterface extends BroadcastInfoInterface
{
    public function getService(): Service;
}
