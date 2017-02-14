<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChange as PipsChangeEntity;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class PipsChangeFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $datesProcessed = [
            new DateTime('-2 days'),
            new DateTime('-10 months'),
            new DateTime('-4 months'),
            (new DateTime())->setDate(1970,1,1)->setTime(0, 0, 0), // '1970-01-01 00:00:00'
            null
        ];

        foreach ($datesProcessed as $index => $dateProcessed) {
            $pipChange = new PipsChangeEntity();
            $pipChange->setCid($index);
            $pipChange->setCreatedTime(new DateTime());
            $pipChange->setEntityId('');
            $pipChange->setEntityType('');
            $pipChange->setEntityUrl('');
            $pipChange->setStatus('');
            $pipChange->setType('');
            if (null !== $dateProcessed) {
                $pipChange->setProcessedTime($dateProcessed);
            }

            $manager->persist($pipChange);
        }

        $manager->flush();
    }
}

