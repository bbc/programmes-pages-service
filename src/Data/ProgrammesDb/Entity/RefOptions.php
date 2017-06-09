<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Exception\InvalidArgumentException;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="ref_options_idx", columns={"guid", "entity"})})
 */
class RefOptions
{
    const TYPE_LOCAL = 'local';
    const TYPE_ADMIN = 'admin';

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
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $guid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $projectId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $options;

    public function __construct(
        string $entity,
        string $guid,
        string $projectId,
        string $type,
        DateTime $createdAt,
        DateTime $updatedAt,
        array $options = []
    ) {
        $this->entity = $entity;
        $this->guid = $guid;
        $this->projectId = $projectId;
        $this->setType($type);
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->options = $options;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function setGuid(string $guid)
    {
        $this->guid = $guid;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     */
    public function setProjectId(string $projectId)
    {
        $this->projectId = $projectId;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        if (!in_array($type, [self::TYPE_ADMIN, self::TYPE_LOCAL])) {
            throw new InvalidArgumentException('Type for options not allowed');
        }

        $this->type = $type;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
