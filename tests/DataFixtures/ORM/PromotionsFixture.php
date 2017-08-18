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
            promotion b1 a (p0000000h)
            promotion b1 b (p0000001h)

            series 1 (b00swyx1)
                promotion b1.s1 a (p000002h - SUPER PROMOTION)
                promotion b1.s2 b (p000003h - SUPER PROMOTION)
                promotion b1.s3 c (p000004h)
                promotion b1.s4 d (p000005h EXPIRED)

                episode 1 (b00syxx6)
                    promotion b1.s1.e1 a (p000006h)
                    promotion b1.s1.e1 b (p000007h)

            series 2 (b010t150)
                promotion b1.s2 a (p000008h  EXPIRED)
                promotion b1.s2 b (p000009h) -> promoting an image

        */

        $this->manager = $manager;

        // promotions in brand
        $this->buildPromotion(
            'p000000h',
            'promotion b1 a',
            $this->getReference('b0175lqm'),
            5,
            true,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            false,
            $this->getReference('b010t19z') // context: brand1
        );
        $this->buildPromotion(
            'p000001h',
            'promotion b1 b',
            $this->getReference('b0175lqm'),
            3,
            true,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            false,
            $this->getReference('b010t19z') // context: brand1
        );

        // promotions in series 1
        $this->buildPromotion(
            'p000002h',
            'promotion b1.s1 a',
            $this->getReference('b0176rgj'),
            4,
            true,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            true,
            $this->getReference('b00swyx1') // context: brand1/series 1
        );

        $this->buildPromotion(
            'p000003h',
            'promotion b1.s1 b',
            $this->getReference('b0176rgj'),
            2,
            true,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            true,
            $this->getReference('b00swyx1') // context: brand1/series 1
        );

        $this->buildPromotion(
            'p000004h',
            'promotion b1.s1 c',
            $this->getReference('b0176rgj'),
            1,
            false,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            false,
            $this->getReference('b00swyx1') // context: brand1/series 1
        );

        $this->buildPromotion(
            'p000005h',
            'promotion b1.s1 d',
            $this->getReference('b0176rgj'),
            7,
            false,
            new DateTime('-10 year'),
            new DateTime('-9 year'),
            false,
            $this->getReference('b00swyx1') // context: brand1/series 1
        );


        // promotions in episode 1:
        $this->buildPromotion(
            'p000006h',
            'promotion b1.s1.e1 a',
            $this->getReference('b0177ffr'),
            9,
            true,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            false,
            $this->getReference('b00syxx6')
        );

        $this->buildPromotion(
            'p000007h',
            'promotion b1.s1.e1 b',
            $this->getReference('b0177ffr'),
            8,
            true,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            false,
            $this->getReference('b00syxx6')
        );

        // promotions in series 2:
        $this->buildPromotion(
            'p000008h',
            'promotion 5',
            $this->getReference('mg000003'),
            2,
            true,
            new DateTime('-10 year'),
            new DateTime('-9 year'),
            false,
            $this->getReference('b010t150')
        );

        $this->buildPromotion(
            'p000009h',
            'promotion 5',
            $this->getReference('mg000003'),
            2,
            true,
            new DateTime('-1 year'),
            new DateTime('+1 year'),
            false,
            $this->getReference('b010t150')
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
