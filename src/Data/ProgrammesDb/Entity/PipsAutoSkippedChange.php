<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This table lists all changes relating to our desired partner_pids that have been skipped due to having a parent in
 * another partner_pid we do not ingest.
 * It's here so we have a permanent record of when that happens and can re-ingest if necessary.
 *
 * We do this because there's really no sane way to deal with that case other than "ingest everything in all
 * partner_pids", which would require a massive rewrite of our queries and reingest of all data.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PipsAutoSkippedChangeRepository")
 */
class PipsAutoSkippedChange extends PipsChangeBase
{
    /**
     * The ID of the parent entity that caused this (otherwise valid) change event to be auto-skipped.
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $triggeringEntityId;

    /**
     * @return string
     */
    public function getTriggeringEntityId(): string
    {
        return $this->triggeringEntityId;
    }

    /**
     * @param string $triggeringEntityId
     */
    public function setTriggeringEntityId(string $triggeringEntityId)
    {
        $this->triggeringEntityId = $triggeringEntityId;
    }
}
