<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\MemberOfGroupInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\PromotableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\RelatedLinkContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ImageRepository")
 */
class Image implements MemberOfGroupInterface, RelatedLinkContextInterface, PromotableInterface
{
    use TimestampableEntity;
    use Traits\IsEmbargoedTrait;
    use Traits\PartnerPidTrait;
    use Traits\SynopsesTrait;

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
     * @ORM\Column(type="string", length=15, nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */

    private $type = 'standard';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=false)
     */
    private $extension = 'jpg';

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

     /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $width;


    public function __construct(string $pid, string $title)
    {
        $this->pid = $pid;
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid): void
    {
        $this->pid = $pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }
}
