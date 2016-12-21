<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\RelatedLinkRepository")
 */
class RelatedLink
{
    use TimestampableEntity;
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
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $uri;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $type;

    /**
     * One of relatedToCoreEntity, relatedToPromotion or relatedToImage must be
     * set. So even though this is nullable, we do want deleting a CoreEntity to
     * cascade to delete the relatedLinks attached to the CoreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $relatedToCoreEntity;


    /**
     * One of relatedToCoreEntity, relatedToPromotion or relatedToImage must be
     * set. So even though this is nullable, we do want deleting a Promotion to
     * cascade to delete the relatedLinks attached to the Promotion
     * @ORM\ManyToOne(targetEntity="Promotion")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $relatedToPromotion;

    /**
     * One of relatedToCoreEntity, relatedToPromotion or relatedToImage must be
     * set. So even though this is nullable, we do want deleting an Image to
     * cascade to delete the relatedLinks attached to the Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $relatedToImage;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isExternal;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @param string $pid
     * @param string $title
     * @param string $uri
     * @param string $type
     * @param CoreEntity|Promotion|Image $relatedTo
     * @param bool $isExternal
     */
    public function __construct(
        string $pid,
        string $title,
        string $uri,
        string $type,
        $relatedTo,
        bool $isExternal
    ) {
        $this->pid = $pid;
        $this->title = $title;
        $this->uri = $uri;
        $this->type = $type;
        $this->setRelatedTo($relatedTo);
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

    /**
     * @return CoreEntity|Promotion|Image
     */
    public function getRelatedTo()
    {
        return $this->relatedToCoreEntity ?? $this->relatedToPromotion ?? $this->relatedToImage;
    }

    /**
     * @return CoreEntity|null
     */
    public function getRelatedToCoreEntity()
    {
        return $this->relatedToCoreEntity;
    }

    /**
     * @return Promotion|null
     */
    public function getRelatedToPromotion()
    {
        return $this->relatedToPromotion;
    }

    /**
     * @return Image|null
     */
    public function getRelatedToImage()
    {
        return $this->relatedToImage;
    }

    /**
     * @param CoreEntity|Promotion|Image $relatedTo
     */
    public function setRelatedTo($relatedTo)
    {
        if ($relatedTo instanceof CoreEntity) {
            $this->setRelatedToBatch($relatedTo, null, null);
        } elseif ($relatedTo instanceof Promotion) {
            $this->setRelatedToBatch(null, $relatedTo, null);
        } elseif ($relatedTo instanceof Image) {
            $this->setRelatedToBatch(null, null, $relatedTo);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected setRelatedTo() to be called with an an instance of "%s", "%s" or "%s". Found instance of "%s"',
                CoreEntity::CLASS,
                Promotion::CLASS,
                Image::CLASS,
                (is_object($relatedTo) ? get_class($relatedTo) : gettype($relatedTo))
            ));
        }
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

    public function setPosition(int $position = null)
    {
        $this->position = $position;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    private function setRelatedToBatch(
        CoreEntity $relatedToCoreEntity = null,
        Promotion $relatedToPromotion = null,
        Image $relatedToImage = null
    ) {
        $this->relatedToCoreEntity = $relatedToCoreEntity;
        $this->relatedToPromotion = $relatedToPromotion;
        $this->relatedToImage = $relatedToImage;
    }
}
