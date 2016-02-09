<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *   indexes={ @ORM\Index(name="pid_idx", columns={"pid", "id", "ancestry"})}
 * )
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\MappedSuperclass()
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *   "brand"="Brand",
 *   "series"="Series",
 *   "episode"="Episode",
 *   "clip"="Clip",
 *   "collection"="Collection",
 *   "season"="Season",
 *   "gallery"="Gallery",
 *   "franchise"="Franchise"
 * })
 * @Gedmo\Tree(type="materializedPath")
 *
 * TODO Properties: masterbrand(link)
 */
abstract class CoreEntity
{
    use TimestampableEntity;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Gedmo\TreePathSource()
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isEmbargoed = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $searchTitle = '';


    /**
     * @var CoreEntity|null
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @Gedmo\TreeParent()
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     * @Gedmo\TreePath(endsWithSeparator=false)
     */
    private $ancestry = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $shortSynopsis = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $longestSynopsis = '';

    /**
     * @var Image|null
     *
     * @ORM\ManyToOne(targetEntity="Image")
     */
    private $image;

    //// Denormalisations

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $relatedLinksCount = 0;


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
        // TOOD Validate PID

        $this->pid = $pid;
    }

    public function getIsEmbargoed(): bool
    {
        return $this->isEmbargoed;
    }

    public function setIsEmbargoed(bool $isEmbargoed)
    {
        $this->isEmbargoed = $isEmbargoed;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getSearchTitle(): string
    {
        return $this->searchTitle;
    }

    public function setSearchTitle(string $searchTitle)
    {
        $this->searchTitle = $searchTitle;
    }

    /**
     * @return CoreEntity|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param CoreEntity|null $parent
     */
    public function setParent(CoreEntity $parent = null)
    {
        $this->parent = $parent;
    }

    public function getAncestry(): string
    {
        return $this->ancestry;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function setShortSynopsis(string $shortSynopsis)
    {
        $this->shortSynopsis = $shortSynopsis;
    }

    public function getLongestSynopsis(): string
    {
        return $this->longestSynopsis;
    }

    public function setLongestSynopsis(string $longestSynopsis)
    {
        $this->longestSynopsis = $longestSynopsis;
    }

    /**
     * @return Image|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Image|null $image
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;
    }

    public function getRelatedLinksCount(): int
    {
        return $this->relatedLinksCount;
    }

    public function setRelatedLinksCount(int $relatedLinksCount)
    {
        $this->relatedLinksCount = $relatedLinksCount;
    }
}
