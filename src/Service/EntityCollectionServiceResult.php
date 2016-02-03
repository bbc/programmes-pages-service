<?php

namespace BBC\ProgrammesPagesService\Service;

class EntityCollectionServiceResult implements ServiceResultInterface
{
    protected $result;

    protected $limit;

    protected $page;

    public function __construct(
        array $result,
        int $limit,
        int $page = 1
    ) {
        $this->result = $result;
        $this->limit = $limit;
        $this->page = $page;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function hasResult(): bool
    {
        return !empty($this->result);
    }
}
