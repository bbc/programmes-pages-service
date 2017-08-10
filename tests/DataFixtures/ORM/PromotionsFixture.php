<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Promotion;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PromotionsFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            MongrelsFixture::Class,
            ImagesFixture::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->buildPromotion(
            'p000000h',
            'active promotion of CoreEntity',
            $this->getReference('b00swyx1'), // promotion of Core entity - serie 1
            1, // weight
            true,
            new DateTime('1900-01-01'),
            new DateTime('3000-01-01')
        );

        $this->buildPromotion(
            'p000001h',
            'active promotion of Image ',
            $this->getReference('mg000003'), // promotion of image
            2,
            true,
            new DateTime('1900-01-01'),
            new DateTime('3000-01-01')
        );

        $this->buildPromotion(
            'p000002h',
            'expired promotion B',
            $this->getReference('b00swyx1'),
            3,
            true,
            new DateTime('1900-01-01'),
            new DateTime('2000-01-01')
        );

        $this->buildPromotion(
            'p000003h',
            'disabled promotion C',
            $this->getReference('b00swyx1'),
            4,
            false,
            new DateTime('1900-01-01'),
            new DateTime('2000-01-01')
        );

        $this->buildSuperPromotion(
            'p000004h',
            'active super promotion D',
            $this->getReference('b010t150'), // promotion of Core entity - serie 2
            5,
            true,
            new DateTime('1900-01-01'),
            new DateTime('3000-01-01')
        );

        $manager->flush();
    }

    private function buildPromotion(string $promoPid, string $title, $promotionOf, int $weighting, bool $isActive, DateTime $startDate, DateTime $endDate)
    {
        $promo = new Promotion(
            $promoPid,
            $promotionOf,
            $startDate,
            $endDate,
            $weighting
        );

        $promo->setTitle($title);
        $promo->setCascadesToDescendants(0);
        $promo->setIsActive($isActive);

        $promo->setContext($this->getReference('b010t19z')); // brand pid

        $this->manager->persist($promo);

        return $promo;
    }

    private function buildSuperPromotion(string $promoPid, string $title, $promotionOf, int $weighting, bool $isActive, DateTime $startDate, DateTime $endDate)
    {
        $promo = new Promotion(
            $promoPid,
            $promotionOf,
            $startDate,
            $endDate,
            $weighting
        );

        $promo->setTitle($title);
        $promo->setCascadesToDescendants(1);
        $promo->setIsActive($isActive);

        $promo->setContext($this->getReference('b010t19z')); // brand pid

        $this->manager->persist($promo);

        return $promo;
    }
}
