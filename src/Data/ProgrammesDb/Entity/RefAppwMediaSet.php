<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Collection as DoctrineCollection;

/**
 * @ORM\Entity()
 */
class RefAppwMediaSet
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
     * @ORM\Column(type="string", length=128, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $rightsPlatform;

    /**
     * @var RefAppwService[]
     *
     * @ORM\ManyToMany(targetEntity="RefAppwService", cascade="persist")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $services;

    public function __construct(string $name, string $rightsPlatform)
    {
        $this->name = $name;
        $this->rightsPlatform = $rightsPlatform;
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

//    public function setName(string $name): void
//    {
//        $this->name = $name;
//    }

    public function getServices(): DoctrineCollection
    {
        return $this->services;
    }

    public function setServices(DoctrineCollection $services)
    {
        $this->services = $services;
    }

    public function getRightsPlatform(): string
    {
        return $this->rightsPlatform;
    }

    public function setRightsPlatform(string $rightsPlatform): void
    {
        $this->rightsPlatform = $rightsPlatform;
    }

    public function addService(RefAppwService $service)
    {
        $this->services->add($service);
    }

    public function hasService(RefAppwService $service)
    {
        foreach ($this->services as $existingService) {
            if ($existingService->getSid() === $service->getSid()) {
                return true;
            }
        }
        return false;
    }
}
