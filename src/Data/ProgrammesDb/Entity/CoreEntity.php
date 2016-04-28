<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *   @ORM\Index(name="core_entity_pid_idx", columns={"pid"}),
 *   @ORM\Index(name="core_entity_ancestry_idx", columns={"ancestry"}),
 *   @ORM\Index(name="core_entity_type_idx", columns={"type"}),
 *  })
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
 * @Gedmo\Tree(type="materializedPath", cascadeDeletes=false)
 */
abstract class CoreEntity
{
    use TimestampableEntity;
    use Traits\IsEmbargoedTrait;
    use Traits\SynopsesTrait;

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
    private $searchTitle = '';


    /**
     * @var CoreEntity|null
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Gedmo\TreeParent()
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     * @Gedmo\TreePath()
     */
    private $ancestry = '';

    /**
     * @var Image|null
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $image;

    /**
     * @var MasterBrand|null
     *
     * @ORM\ManyToOne(targetEntity="MasterBrand")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $masterBrand;

    /**
     * This field is the actual category information from PIPs. For the front end, we denormalise
     * category information from parent programmes onto the child, do not query this field from the
     * front end.
     *
     * @ORM\ManyToMany(targetEntity="Category")
     * @ORM\JoinTable(name="ref_programme_category",
     *   joinColumns={@ORM\JoinColumn(name="programme_id", referencedColumnName="id", onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $directCategories;

    //// Denormalisations

    /**
     * This field is denormalised and is what's actually used for genre/format display
     *
     * @ORM\ManyToMany(targetEntity="Category")
     * @ORM\JoinTable(name="programme_category",
     *   joinColumns={@ORM\JoinColumn(name="programme_id", referencedColumnName="id", onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $categories;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $relatedLinksCount = 0;

    public function __construct(string $pid, string $title)
    {
        $this->pid = $pid;
        $this->title = $title;
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
        // TODO Validate PID

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

    /**
     * @return Image|null
     */
    public function getImage()
    {
        return $this->image;
    }

    public function setImage(Image $image = null)
    {
        $this->image = $image;
    }

    /**
     * @return MasterBrand|null
     */
    public function getMasterBrand()
    {
        return $this->masterBrand;
    }

    public function setMasterBrand(MasterBrand $masterBrand = null)
    {
        $this->masterBrand = $masterBrand;
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
