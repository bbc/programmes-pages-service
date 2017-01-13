<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Format extends Category
{
    public function setParent(?Category $parent)
    {
        // FORMATS DO NOT HAVE PARENTS. GOODNIGHT.
    }

    public function getParent(): ?Category
    {
        return null;
    }
}
