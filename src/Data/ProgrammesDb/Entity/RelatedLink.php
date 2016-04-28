<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="related_link_pid_idx", columns={"pid"}),
 * })
 * @ORM\Entity()
 */
class RelatedLink
{
    use Traits\SynopsesTrait;
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
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $uri;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $relatedTo;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isExternal;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $position;


    public function __construct(
        string $pid,
        string $title,
        string $uri,
        string $type,
        CoreEntity $relatedTo,
        bool $isExternal
    ) {
        $this->pid = $pid;
        $this->title = $title;
        $this->uri = $uri;
        $this->type = $type;
        $this->relatedTo = $relatedTo;
        $this->isExternal = $isExternal;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getRelatedTo(): CoreEntity
    {
        return $this->relatedTo;
    }

    public function setRelatedTo(CoreEntity $relatedTo)
    {
        $this->relatedTo = $relatedTo;
    }

    public function getIsExternal(): bool
    {
        return $this->isExternal;
    }

    public function setIsExternal(bool $isExternal)
    {
        $this->isExternal = $isExternal;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
