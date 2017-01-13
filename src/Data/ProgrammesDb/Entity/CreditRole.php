<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 */
class CreditRole
{
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
     * @ORM\Column(type="string", length=40, nullable=false, unique=true)
     */
    private $creditRoleId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $description = '';

    public function __construct(string $creditRoleId)
    {
        $this->creditRoleId = $creditRoleId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreditRoleId(): string
    {
        return $this->creditRoleId;
    }

    public function setCreditRoleId(string $creditRoleId): void
    {
        $this->creditRoleId = $creditRoleId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }
}
