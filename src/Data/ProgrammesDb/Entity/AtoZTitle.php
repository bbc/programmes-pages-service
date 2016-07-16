<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 * name="atoz_title",
 * uniqueConstraints={@ORM\UniqueConstraint(name="atoz_unique", columns={"programme_id", "title"})},
 * indexes={
 *   @ORM\Index(name="atoz_title_title", columns={"title"}),
 *   @ORM\Index(name="atoz_title_first_letter", columns={"first_letter"}),
 * })
 * @ORM\Entity()
 */
class AtoZTitle
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Programme
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $programme;

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

    public function __construct(string $title, Programme $programme)
    {
        $this->programme = $programme;
        $this->setTitle($title);
    }

    public function getProgramme(): Programme
    {
        return $this->programme;
    }

    public function setProgramme(Programme $programme)
    {
        $this->programme = $programme;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        $firstLetter = substr($title, 0, 1);
        if (!preg_match('/^[A-Za-z]/', $firstLetter)) {
            $firstLetter = '@';
        }
        $this->firstLetter = strtolower($firstLetter);
    }

    public function getFirstLetter(): string
    {
        return $this->firstLetter;
    }
}
