<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity;
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
        /*
        PROMOTION CONTEXTS:
        ===================

        Brand (b010t19z)
            promotion 0 (p000000h, SUPER PROMOTION)

            series 1 (b00swyx1)
                promotion 1 (p000001h)
                promotion 2 (EXPIRED)

                episode 1
                    promotion 4 (p000004h)

            series 2 (b010t150)
                promotion 3 (EXPIRED)

        */

        $this->manager = $manager;

        $this->buildPromotion(
            'p000000h',
            'promotion 1',
            $this->getReference('b0175lqm'), // promoting: series2/episode1
            5,
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('3000-01-01 00:00:00'),
            true,
            $this->getReference('b010t19z') // context: brand1
        );

        $manager->flush();

        $this->buildPromotion(
            'p000001h',
            'promotion 1',
            $this->getReference('b0176rgj'), // promoting: series2/episode2
            1, // weight
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('3000-01-01 00:00:00'),
            false,
            $this->getReference('b00swyx1') // context: brand1/series 1
        );

        // EXPIRED
        $this->buildPromotion(
            'p000002h',
            'promotion 2',
            $this->getReference('b0176rgj'), // promoting: series2/episode2
            4,
            false,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('2000-01-01 00:00:00'),
            false,
            $this->getReference('b00swyx1') // context: brand1/series 1
        );

        // EXPIRED
        $this->buildPromotion(
            'p000003h',
            'promotion 3',
            $this->getReference('b0177ffr'), // promoting: series2/episode3
            3,
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('2000-01-01 00:00:00'),
            false,
            $this->getReference('b00swyx1') // context: brand1/series 1
        );

        $this->buildPromotion(
            'p000004h',
            'promotion 4',
            $this->getReference('b0177ffr'), // promoting: series2/episode3
            3,
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('2200-01-01 00:00:00'),
            false,
            $this->getReference('b00syxx6') // context: brand1/series1/episode2
        );

        $this->buildPromotion(
            'p000005h',
            'promotion 5',
            $this->getReference('mg000003'), // promoting: image
            2,
            true,
            new DateTime('1900-01-01 00:00:00'),
            new DateTime('3000-01-01 00:00:00'),
            false,
            $this->getReference('b010t150') // context: brand1/series2
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
        bool $isSuperpromotion,
        CoreEntity $context
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
        $promo->setContext($context);

        $this->manager->persist($promo);

        return $promo;
    }
}
