<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\VersionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CompetitionWarningFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [ImagesFixture::class];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        // Radio four competition warning
        $originalType = $this->buildVersionType('Original', 'Original');
        $radioFourCompetitionWarningEpisode = $this->buildEpisode('p0000010', 'Radio Four Competition Warning', false, true);
        $radioFourCompetitionWarningVersion = $this->buildVersion('v0000016', $radioFourCompetitionWarningEpisode, true, false, [$originalType]);
        $radioFourMasterBrand = $this->buildMasterBrand('radio_four', 'b1000000', $radioFourCompetitionWarningVersion);
        $this->updateEpisode($radioFourCompetitionWarningEpisode, $radioFourCompetitionWarningVersion, $radioFourMasterBrand);

        $brandParent = $this->buildBrand('b0000111', 'Brand with masterbrand', false, $radioFourMasterBrand);
        $bbcOneMasterBrand = $this->buildMasterBrand('bbc_one', 'b2000000', null);

        // Standard episode without competition warning
        $streamableEpisode = $this->buildEpisode('p0000011', 'Standard episode without competition warning', false, true);
        $streamableVersion = $this->buildVersion('v0000017', $streamableEpisode, true, false, [$originalType], false);
        $otherVersion = $this->buildVersion('v0000020', $streamableEpisode, true, false, [$originalType], false);
        $this->updateEpisode($streamableEpisode, $streamableVersion, $bbcOneMasterBrand);

        // Standard episode with competition warning
        $streamableEpisode2 = $this->buildEpisode('p0000012', 'Standard episode with competition warning', false, true, $brandParent);
        $streamableVersion2 = $this->buildVersion('v0000018', $streamableEpisode2, true, false, [$originalType], true);
        $otherVersion2 = $this->buildVersion('v0000021', $streamableEpisode2, true, false, [$originalType]);
        $this->updateEpisode($streamableEpisode2, $streamableVersion2, $radioFourMasterBrand);

        // No masterbrand episode with parents that have masterbrand with competition warning
        $trickyEpisode = $this->buildEpisode('p0000015', 'No masterbrand episode with competition warning', false, true, $brandParent);
        $trickyVersion = $this->buildVersion('v0000019', $trickyEpisode, true, false, [$originalType], true);
        $trickyOtherVersion = $this->buildVersion('v0000022', $trickyEpisode, true, false, [$originalType]);
        $this->updateEpisode($trickyEpisode, $trickyVersion, null);

        // Non streamable episode
        $nonStreamableEpisode = $this->buildEpisode('p0000016', 'Non streamable episode', false, false, $brandParent);

        $manager->flush();
    }

    private function buildVersion($pid, $parent, bool $isStreamable, bool $isDownloadable, array $types = [], bool $hasCompetitionWarning = false)
    {
        $entity = new Version($pid, $parent);
        $entity->setStreamable($isStreamable);
        $entity->setDownloadable($isDownloadable);
        $entity->setCompetitionWarning($hasCompetitionWarning);

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

    private function buildEpisode($pid, $title, $embargoed = false, $streamable = false, Brand $parent = null)
    {
        $entity = new Episode($pid, $title);
        $entity->setIsEmbargoed($embargoed);
        $entity->setStreamable($streamable);
        $entity->setParent($parent);

        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }

    private function updateEpisode(Episode $episode, Version $streamableVersion, ?MasterBrand $masterBrand)
    {
        $episode->setStreamableVersion($streamableVersion);
        $episode->setMasterBrand($masterBrand);
        $this->setReference($episode->getPid(), $episode);
        $this->manager->persist($episode);
    }

    private function buildBrand(string $pid, string $title, bool $embargoed = false, ?MasterBrand $masterBrand = null): Brand
    {
        $entity = new Brand($pid, $title);
        $entity->setIsEmbargoed($embargoed);
        $entity->setMasterBrand($masterBrand);
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildMasterBrand(string $mid, string $pid, ?Version $competitionWarning)
    {
        $entity = new MasterBrand($mid, $pid, 'Radio Four');
        $entity->setCompetitionWarning($competitionWarning);
        $this->addReference($mid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }
}
