<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
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
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $type;

    /**
     * @var RefRelationship
     *
     * @ORM\OneToOne(targetEntity="RefRelationship")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", unique=true)
     */
    private $relationship;

    public function __construct(
        CoreEntity $coreEntity,
        Image $image,
        string $type,
        RefRelationship $relationship
    ) {
        $this->coreEntity = $coreEntity;
        $this->image = $image;
        $this->type = $type;
        $this->relationship = $relationship;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getRelationship(): RefRelationship
    {
        return $this->relationship;
    }

    public function setRelationship(RefRelationship $relationship)
    {
        $this->relationship = $relationship;
    }
}
