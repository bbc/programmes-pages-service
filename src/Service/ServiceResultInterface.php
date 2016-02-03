<?php

namespace BBC\ProgrammesPagesService\Service;

interface ServiceResultInterface
{
    public function getResult();
    public function hasResult(): bool;
}
