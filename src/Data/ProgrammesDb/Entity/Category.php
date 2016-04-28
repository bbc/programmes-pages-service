<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *      @ORM\Index(name="category_ancestry_idx", columns={"ancestry"}),
 *      @ORM\Index(name="category_type_idx", columns={"type"}),
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\MaterializedPathRepository")
 * @Gedmo\Tree(type="materializedPath", cascadeDeletes=false)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\MappedSuperclass()
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *   "genre"="Genre",
 *   "format"="Format",
 * })
 */
abstract class Category
{
    use TimestampableEntity;

    /**
     * @var int
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
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\TreePath()
     */
    private $ancestry;

    /**
     * @var int|null
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @var int
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(length=255, nullable=false)
     */
    private $urlKey;

    /**
     * @var string
     *
     * @ORM\Column(length=32, nullable=false)
     */
    private $pipId;

    /**
     * Category constructor.
     *
     * @param string $pipId
     * @param string $title
     * @param string $urlKey
     */
    public function __construct(string $pipId, string $title, string $urlKey)
    {
        $this->pipId = $pipId;
        $this->title = $title;
        $this->urlKey = $urlKey;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getAncestry()
    {
        return $this->ancestry;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPipId(): string
    {
        return $this->pipId;
    }

    public function setPipId(string $pipId)
    {
        $this->pipId = $pipId;
    }

    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return Category|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
    }

    public function setUrlKey(string $urlKey)
    {
        $this->urlKey = $urlKey;
    }

    /**
     * @return DoctrineCollection;
     */
    public function getChildren()
    {
        return $this->children;
    }
}
