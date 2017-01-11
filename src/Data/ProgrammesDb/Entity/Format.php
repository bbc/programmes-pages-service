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

    /**
     * @return null
     */
    public function getParent()
    {
        return null;
    }
}
