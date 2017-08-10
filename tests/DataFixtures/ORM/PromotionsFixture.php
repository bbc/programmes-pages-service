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
            ImagesFixture::class,
            MongrelsFixture::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->buildPromotion(
            'p000000h',
            'active promotion of CoreEntity',
            $this->getReference('b00swyx1'), // promotion of Core entity - series 1
            1, // weight
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('3000-01-01 00:00:00')
        );

        $this->buildPromotion(
            'p000001h',
            'active promotion of Image ',
            $this->getReference('mg000003'), // promotion of image
            2,
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('3000-01-01 00:00:00')
        );

        $this->buildPromotion(
            'p000002h',
            'expired promotion B',
            $this->getReference('b010t150'), // promotion of series 2 (expired)
            3,
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('2000-01-01 00:00:00')
        );

        $this->buildPromotion(   // promotion of series 1 (expired)
            'p000003h',
            'disabled promotion C',
            $this->getReference('b00swyx1'),
            4,
            false,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('2000-01-01 00:00:00')
        );

        $this->buildPromotion(
            'p000004h',
            'active super promotion D',
            $this->getReference('b00swgkn'), // promotion of episode 1
            5,
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('3000-01-01 00:00:00'),
            true
        );

        $manager->flush();
    }

    private function buildPromotion(
        string $promoPid,
        string $title,
        $promotionOf,
        int $weighting,
        bool $isActive,
        DateTime $startDate,
        DateTime $endDate,
        bool $isSuperpromotion = false
    ): Promotion {
        $promo = new Promotion(
            $promoPid,
            $promotionOf,
            $startDate,
            $endDate,
            $weighting
        );

        $promo->setTitle($title);
        $promo->setCascadesToDescendants($isSuperpromotion);
        $promo->setIsActive($isActive);
        $promo->setContext($this->getReference('b010t19z')); // brand pid

        $this->manager->persist($promo);

        return $promo;
    }
}
