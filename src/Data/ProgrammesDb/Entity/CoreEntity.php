<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\ContributableToInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\MemberOfGroupInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\PromotableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\RelatedLinkContextInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Util\SearchUtilitiesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *   @ORM\Index(name="core_entity_ancestry_idx", columns={"ancestry"}),
 *   @ORM\Index(name="core_entity_tleo_id_idx", columns={"tleo_id"}),
 *   @ORM\Index(name="core_entity_type_idx", columns={"type"}),
 *   @ORM\Index(name="core_entity_streamable_idx", columns={"streamable"}),
 *   @ORM\Index(name="core_entity_streamable_alternate_idx", columns={"streamable_alternate"}),
 *   @ORM\Index(name="core_entity_children_json_cover_idx", columns={"parent_id", "position", "first_broadcast_date", "title"}),
 *   @ORM\Index(name="core_entity_type_embargoed_ancestry_cover_idx", columns={"type", "is_embargoed", "ancestry"}),
 *   @ORM\Index(name="core_entity_type_streamable_ancestry_cover_idx", columns={"type", "streamable", "ancestry"}),
 *   @ORM\Index(name="core_entity_sibling_by_first_broadcast_idx", columns={"parent_id", "first_broadcast_date"}),
 *   @ORM\Index(name="core_entity_available_category_cover_idx", columns={"parent_id", "type", "streamable", "is_embargoed"}),
 *   @ORM\Index(name="core_entity_ft_all", columns={"search_title","short_synopsis"}, flags={"fulltext"}),
 *   @ORM\Index(name="core_entity_ft_search_title", columns={"search_title"}, flags={"fulltext"}),
 *   @ORM\Index(name="core_entity_ft_short_synopsis", columns={"short_synopsis"}, flags={"fulltext"}),
 *   @ORM\Index(name="core_entity_tleo_id_type_streamable_from_idx", columns={"tleo_id", "type", "streamable_from"}),
 *   @ORM\Index(name="core_entity_tleo_id_type_streamable_streamable_from_idx", columns={"tleo_id", "type", "streamable", "streamable_from"}),
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
abstract class CoreEntity implements ContributableToInterface, MemberOfGroupInterface, RelatedLinkContextInterface, PromotableInterface
{
    use TimestampableEntity;
    use Traits\IsEmbargoedTrait;
    use Traits\OptionsTrait;
    use Traits\PartnerPidTrait;
    use Traits\SynopsesTrait;
    use SearchUtilitiesTrait;

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
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $searchTitle = '';

    /**
     * @var CoreEntity|null
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Gedmo\TreeParent()
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\TreePath()
     */
    private $ancestry = '';

    /**
     * @var int|null
     *
     * TLEO is the top level editorial object. It is the item at the top of the
     * ancestry tree, which may be the current item if it has no parents.
     * On initial insert into the DB this shall be null (as the entity won't
     * know its ID yet), but shall be instantly updated in the TreePath config
     * logic.
     * This won't be a FK, to avoid potential locking issues, but shall be an
     * index.
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeRoot()
     */
    private $tleoId;

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
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="programmes")
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

    /**
     * ANNOYING EXPLANATORY COMMENT
     *
     * Fields marked TRAIT_PULLDOWN are defined on CoreEntity rather than
     * in the trait that defines the field's getters and setters. This is because
     * doctrine generates incorrect SQL (specifying a single field multiple
     * times in a select) for fields that are defined in more than one
     * place in a class heirarchy. Getters and setters are defined in the
     * trait and the trait is only added to entities where they are relevant.
     *
     * Fields marked COUNT_PULLDOWN are defined on CoreEntity rather than
     * on the entities that actually use them because doctrine does not
     * allow us to set a non-nullable column on a subclass of a discriminator
     * mapped table. See https://github.com/doctrine/doctrine2/issues/5555
     */

    /**
     * @var int
     * TRAIT_PULLDOWN for AggregatedBroadcastsCountMethodsTrait
     * COUNT_PULLDOWN for classes importing AggregatedBroadcastsCountMethodsTrait
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    protected $aggregatedBroadcastsCount = 0;

    /**
     * @var int
     * TRAIT_PULLDOWN for AggregatedEpisodesCountMethodsTrait
     * COUNT_PULLDOWN for classes importing AggregatedEpisodesCountMethodsTrait
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    protected $aggregatedEpisodesCount = 0;

    /**
     * @var int
     * TRAIT_PULLDOWN for AvailableClipsCountMethodsTrait
     * COUNT_PULLDOWN for classes importing AvailableClipsCountMethodsTrait
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    protected $availableClipsCount = 0;

    /**
     * @var int
     * TRAIT_PULLDOWN for AvailableEpisodesCountMethodsTrait
     * COUNT_PULLDOWN for classes implementing AvailableEpisodesCountMethodsTrait
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    protected $availableEpisodesCount = 0;

    /**
     * @var int
     * TRAIT_PULLDOWN for AggregatedGalleriesCountMethodsTrait
     * COUNT_PULLDOWN for classes implementing AggregatedGalleriesCountMethodsTrait
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    protected $aggregatedGalleriesCount = 0;

    /**
     * @var bool
     * TRAIT_PULLDOWN for IsPodcastableMethodsTrait
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    protected $isPodcastable = false;

    /**
     * @var int
     * COUNT_PULLDOWN for Programme
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    protected $promotionsCount = 0;

    /**
     * @var bool
     * COUNT_PULLDOWN for Programme
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    protected $streamable = false;

    /**
     * @var bool
     * COUNT_PULLDOWN for Programme
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    protected $streamableAlternate = false;

    /**
     * @var int
     * COUNT_PULLDOWN for ProgrammeItem
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    protected $segmentEventCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    private $contributionsCount = 0;

    public function __construct(string $pid, string $title)
    {
        $this->pid = $pid;
        $this->setTitle($title);
        $this->directCategories = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
        // TODO Validate PID

        $this->pid = $pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->setSearchTitle($this->stripPunctuation($title));
    }

    public function getSearchTitle(): string
    {
        return $this->searchTitle;
    }

    public function setSearchTitle(string $searchTitle): void
    {
        $this->searchTitle = $searchTitle;
    }

    public function getParent(): ?CoreEntity
    {
        return $this->parent;
    }

    public function setParent(?CoreEntity $parent): void
    {
        $this->parent = $parent;
    }

    public function getAncestry(): string
    {
        return $this->ancestry;
    }

    public function getTleoId(): ?int
    {
        return $this->tleoId;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): void
    {
        $this->image = $image;
    }

    public function getMasterBrand(): ?MasterBrand
    {
        return $this->masterBrand;
    }

    public function setMasterBrand(?MasterBrand $masterBrand): void
    {
        $this->masterBrand = $masterBrand;
    }

    public function getRelatedLinksCount(): int
    {
        return $this->relatedLinksCount;
    }

    public function setRelatedLinksCount(int $relatedLinksCount): void
    {
        $this->relatedLinksCount = $relatedLinksCount;
    }

    public function getContributionsCount(): int
    {
        return $this->contributionsCount;
    }

    public function setContributionsCount(int $contributionsCount): void
    {
        $this->contributionsCount = $contributionsCount;
    }
}
