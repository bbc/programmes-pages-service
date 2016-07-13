<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This table is needed so that we can tell MySQL to use it as a stopword list for InnoDB
 * We don't really do much with it, but it does need to be part of the schema.
 *
 * @ORM\Entity()
 */
class Stopword
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $value;
}
