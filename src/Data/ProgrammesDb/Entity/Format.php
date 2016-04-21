<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Format extends Category
{
    public function setParent(Category $parent = null)
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

    /**
     * @return array
     */
    public function getChildren()
    {
        return [];
    }

    /**
     * @param array $children
     */
    public function setChildren($children)
    {
        // FORMATS DO NOT HAVE CHILDREN. GOODNIGHT.
    }
}
