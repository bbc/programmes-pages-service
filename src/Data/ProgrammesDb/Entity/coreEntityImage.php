<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @Table(name="core_entity_image", uniqueConstraints={@UniqueConstraint(name="core_entity_image_unique", columns={"core_entity_id", "image_id", "relationship_type"})})
 * @ORM\Entity()
 */
class CoreEntityImage
{
    use TimestampableEntity;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var CoreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $coreEntity;

    /**
     * @var CoreEntity
     *
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $relationshipType = '';

    public function __construct(
        CoreEntity $coreEntity,
        Image $image,
        string $relationshipType
    ) {
        $this->coreEntity = $coreEntity;
        $this->image = $image;
        $this->relationshipType = $relationshipType;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCoreEntity(): CoreEntity
    {
        return $this->coreEntity;
    }

    public function setCoreEntity(CoreEntity $coreEntity)
    {
        $this->coreEntity = $coreEntity;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    public function getRelationshipType()
    {
        return $this->relationshipType;
    }

    public function setRelationshipType(string $relationshipType)
    {
        $this->relationshipType = $relationshipType;
    }
}
