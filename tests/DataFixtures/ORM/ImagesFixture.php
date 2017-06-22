<?php
declare(strict_types = 1);

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class ImagesFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->buildImage('mg000001', 'Default Image');
        $this->buildImage('mg000002', 'Network Image');
        $this->buildImage('mg000003', 'Programme Image');

        $manager->flush();
    }
    private function buildImage(string $pid, string $title)
    {
        $image = new Image($pid, $title);
        $image->setExtension('jpg');
        $image->setPartnerPid('s0000001');
        $this->manager->persist($image);
        $this->addReference($pid, $image);
        return $image;
    }
}
