<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="categories", indexes={
 *      @ORM\Index(name="categories_id_idx", columns={"id"}),
 *      @ORM\Index(name="categories_ancestry_idx", columns={"ancestry"}),
 *      @ORM\Index(name="categories_parent_id_idx", columns={"parent_id"}),
 *      @ORM\Index(name="categories_type_idx", columns={"type"}),
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\MaterializedPathRepository")
 * @Gedmo\Tree(type="materializedPath")
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
     * @Gedmo\TreePath(endsWithSeparator=false)
     */
    private $ancestry = '';

    /**
     * @var int|null
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
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
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param array $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getRoot()
    {
        return $this->root;
    }
}
