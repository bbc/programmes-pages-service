<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="ref_isite_options_entity_id_idx", columns={"entity_id"})})
 */
class RefIsiteOptions
{
    const TYPE_LOCAL = 'local';
    const TYPE_ADMIN = 'admin';
    const TYPE_CONTACT = 'contact';

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
     * @ORM\Column(type="string", length=36, unique=true, nullable=false)
     */
    private $guid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=35, nullable=false)
     */
    private $projectId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $parentProjectId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=55, nullable=false)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $fileId;

    /**
     * local|admin|contact
     *
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=false)
     */
    private $options;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $isiteLastModified;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $isiteCreatedAt;

    public function __construct(
        string $guid,
        string $projectId,
        ?string $parentProjectId,
        string $entityId,
        string $fileId,
        string $type,
        DateTime $createdAt,
        DateTime $modifiedAt,
        array $options = []
    ) {
        $this->guid = $guid;
        $this->projectId = $projectId;
        $this->parentProjectId = $parentProjectId;
        $this->entityId = $entityId;
        $this->fileId = $fileId;
        $this->setType($type);
        $this->isiteLastModified = $modifiedAt;
        $this->isiteCreatedAt = $createdAt;
        $this->options = $options;
    }

    public function getId(): ?int
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

    public function setProjectId(string $projectId)
    {
        $this->projectId = $projectId;
    }

    public function getParentProjectId(): ?string
    {
        return $this->parentProjectId;
    }

    public function setParentProjectId(?string $parentProjectId)
    {
        $this->parentProjectId = $parentProjectId;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function setFileId(string $fileId)
    {
        $this->fileId = $fileId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (!in_array($type, [self::TYPE_ADMIN, self::TYPE_LOCAL, self::TYPE_CONTACT])) {
            throw new InvalidArgumentException('Type document for options not allowed');
        }

        $this->type = $type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }

    public function getIsiteLastModified(): DateTime
    {
        return $this->isiteLastModified;
    }

    public function setIsiteLastModified(DateTime $isiteLastModified)
    {
        $this->isiteLastModified = $isiteLastModified;
    }

    public function getIsiteCreatedAt(): DateTime
    {
        return $this->isiteCreatedAt;
    }

    public function setIsiteCreatedAt(DateTime $isiteCreatedAt)
    {
        $this->isiteCreatedAt = $isiteCreatedAt;
    }
}
