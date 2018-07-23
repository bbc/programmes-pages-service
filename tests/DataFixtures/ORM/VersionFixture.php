<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\VersionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VersionFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            ImagesFixture::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $originalType = $this->buildVersionType('Original', 'Original');
        $otherType = $this->buildVersionType('Other', 'Other');

        $episode = $this->buildEpisode('p0000001', 'Ep1');
        $embargoedEpisode = $this->buildEpisode('p0000002', 'Ep2', true);
        $episode3 = $this->buildEpisode('p0000003', 'Ep3', false, true);
        $episode4 = $this->buildEpisode('p0000004', 'Ep4');
        $episode5 = $this->buildEpisode('p0000005', '1 === 0');
        $episode6 = $this->buildEpisode('p0000006', 'Zzzz');

        $brand = $this->buildBrand('b0000022', 'Brand 1');
        $episode3->setParent($brand);

        $this->buildVersion('v0000001', $episode, false, false, [$originalType, $otherType]);
        $this->buildVersion('v0000002', $embargoedEpisode, true, false, [$originalType, $otherType]);
        $this->buildVersion('v0000003', $episode, true, false, [$originalType, $otherType]);
        $this->buildVersion('v0000004', $episode, false, true);
        $this->buildVersion('v0000005', $episode3, false, false);
        $this->buildVersion('v0000006', $episode4, false, false);
        $this->buildVersion('v0000007', $episode5, false, false);
        $this->buildVersion('v0000008', $episode6, false, false);

        // Streamable episode tests
        $alternateType = $this->buildVersionType('DubbedAudioDescribed', 'DubbedAudioDescribed');
        $streamableEpisode = $this->buildEpisode('p0000007', 'StreamableTest', false, true);
        $this->buildVersion('v0000009', $streamableEpisode, true, false, [$originalType]);
        $streamableVersion = $this->buildVersion('v0000010', $streamableEpisode, true, false, [$originalType, $otherType]);
        $this->buildVersion('v0000011', $streamableEpisode, true, false, [$alternateType]);
        $streamableEpisode->setStreamableVersion($streamableVersion);
        $streamableEpisode->setStreamable(true);

        $manager->flush();
    }

    private function buildVersion($pid, $parent, bool $isStreamable, bool $isDownloadable, array $types = [])
    {
        $entity = new Version($pid, $parent);
        $entity->setStreamable($isStreamable);
        $entity->setDownloadable($isDownloadable);

        if (!empty($types)) {
            $entity->setVersionTypes(new ArrayCollection($types));
        }

        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }

    private function buildVersionType($type, $title)
    {
        $entity = new VersionType($type, $title);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildEpisode($pid, $title, $embargoed = false, $addImage = false)
    {
        $entity = new Episode($pid, $title);
        $entity->setIsEmbargoed($embargoed);
        if ($addImage) {
            $entity->setImage($this->getReference('mg000003'));
        }
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildBrand(string $pid, string $title, bool $embargoed = false): Brand
    {
        $entity = new Brand($pid, $title);
        $entity->setIsEmbargoed($embargoed);
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }
}
