<?php

namespace BBC\ProgrammesPagesService\Service;

class EntitySingleServiceResult implements ServiceResultInterface
{
    protected $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function hasResult(): bool
    {
        return !!$this->result;
    }
}
