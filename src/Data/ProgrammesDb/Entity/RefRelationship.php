<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Relationship Reference Table. Never queried directly.
 * Instead used for denormalisations
 * @ORM\Entity()
 */
class RefRelationship
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private $subjectPid;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private $objectPid;

    /**
     * @var RefRelationshipType
     *
     * @ORM\ManyToOne(targetEntity="RefRelationshipType")
     */
    private $relationshipType;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    public function setPid(string $pid = null)
    {
        $this->pid = $pid;
    }

    /**
     * @return string|null
     */
    public function getSubjectPid()
    {
        return $this->subjectPid;
    }

    public function setSubjectPid(string $subjectPid = null)
    {
        $this->subjectPid = $subjectPid;
    }

    /**
     * @return string|null
     */
    public function getObjectPid()
    {
        return $this->objectPid;
    }

    public function setObjectPid(string $objectPid = null)
    {
        $this->objectPid = $objectPid;
    }

    /**
     * @return RefRelationshipType|null
     */
    public function getRelationshipType()
    {
        return $this->relationshipType;
    }

    public function setRelationshipType(RefRelationshipType $relationshipType = null)
    {
        $this->relationshipType = $relationshipType;
    }
}
