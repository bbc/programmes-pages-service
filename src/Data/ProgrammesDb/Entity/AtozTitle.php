<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 * name="atoz_title",
 * uniqueConstraints={@ORM\UniqueConstraint(name="atoz_unique", columns={"core_entity_id", "title"})},
 * indexes={
 *   @ORM\Index(name="atoz_title_title", columns={"title"}),
 *   @ORM\Index(name="atoz_title_first_letter", columns={"first_letter"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository")
 */
class AtozTitle
{
    public const NUMERIC_KEY = '@';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var CoreEntity
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $coreEntity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $firstLetter;

    public function __construct(string $title, CoreEntity $coreEntity)
    {
        $this->coreEntity = $coreEntity;
        $this->setTitle($title);
    }

    public function getEntity(): CoreEntity
    {
        return $this->programme;
    }

    public function setEntity(CoreEntity $coreEntity)
    {
        $this->coreEntity = $coreEntity;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        $firstLetter = self::NUMERIC_KEY;
        $possibleAlphas = preg_replace('/[^A-Za-z0-9]/', '', $title);
        if ($possibleAlphas) {
            $firstLetter = substr($possibleAlphas, 0, 1);
            if (preg_match('/^[0-9]/', $firstLetter)) {
                $firstLetter = self::NUMERIC_KEY;
            }
        }
        $this->firstLetter = strtolower($firstLetter);
    }

    public function getFirstLetter(): string
    {
        return $this->firstLetter;
    }
}
