<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="ref_relationship_object_id_idx", columns={"object_id"}),
 * })
 * @ORM\Entity()
 */
class RefRelationship
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private $subjectId;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $subjectType;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $objectId;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $objectType;

    /**
     * @var RefRelationshipType
     *
     * @ORM\ManyToOne(targetEntity="RefRelationshipType")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $relationshipType;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $pipsLastModified;

    public function __construct(
        string $pid,
        string $subjectId,
        string $subjectType,
        string $objectId,
        string $objectType,
        RefRelationshipType $relationshipType,
        DateTime $pipsLastModified
    ) {
        $this->pid = $pid;
        $this->subjectId = $subjectId;
        $this->subjectType = $subjectType;
        $this->objectId = $objectId;
        $this->objectType = $objectType;
        $this->relationshipType = $relationshipType;
        $this->pipsLastModified = $pipsLastModified;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid)
    {
        $this->pid = $pid;
    }

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }

    public function setSubjectId(string $subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function getSubjectType(): string
    {
        return $this->subjectType;
    }

    public function setSubjectType(string $subjectType)
    {
        $this->subjectType = $subjectType;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function setObjectId(string $objectId)
    {
        $this->objectId = $objectId;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function setObjectType(string $objectType)
    {
        $this->objectType = $objectType;
    }

    public function getRelationshipType(): RefRelationshipType
    {
        return $this->relationshipType;
    }

    public function setRelationshipType(RefRelationshipType $relationshipType)
    {
        $this->relationshipType = $relationshipType;
    }

    public function getPipsLastModified(): DateTime
    {
        return $this->pipsLastModified;
    }

    public function setPipsLastModified(DateTime $pipsLastModified)
    {
        $this->pipsLastModified = $pipsLastModified;
    }
}
