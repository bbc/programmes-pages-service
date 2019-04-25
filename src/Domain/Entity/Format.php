<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Format extends Category
{
    public function getAncestry(): array
    {
        return [];
    }

    public function getChildren(): array
    {
        return [];
    }

    public function getHierarchicalTitle(): string
    {
        return $this->getTitle();
    }

    public function getUrlKeyHierarchy(): string
    {
        return $this->getUrlKey();
    }
}
