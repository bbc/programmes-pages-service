<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use DateTime;

class EastendersFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $brand = new Brand();
        $brand->setPid('b006m86d');
        $brand->setTitle('Eastenders');
        $brand->setReleaseDate(new PartialDate('2015'));
        $brand->setAvailableClipsCount(2);

        $manager->persist($brand);
        $manager->flush();
    }
}
