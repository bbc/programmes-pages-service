<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class CachingTest extends BaseProgrammeMapperTestCase
{
    public function testCacheAccountsForUnfetchedEntities()
    {
        $mapperFactory = new MapperFactory();
        $programmeMapper = $mapperFactory->getProgrammeMapper();

        $dbEntityWithAllUnfetched = $this->getSampleProgrammeDbEntity(
            'b0000001',
            null,
            null,
            [],
            null,
            1
        );

        $defaultImage = $mapperFactory->getImageMapper()->getDefaultImage();
        $expectedDomainEntityWithAllUnfetched = $this->getSampleProgrammeDomainEntity(
            'b0000001',
            $defaultImage,
            null,
            [],
            [],
            null,
            1
        );

        $dbEntityWithFetchedItem = $this->getSampleProgrammeDbEntity(
            'b0000001',
            [
                'id' => '1',
                'pid' => 'p01m5mss',
                'title' => 'Title',
                'shortSynopsis' => 'ShortSynopsis',
                'mediumSynopsis' => 'MediumSynopsis',
                'longSynopsis' => 'LongestSynopsis',
                'type' => 'standard',
                'extension' => 'jpg',
            ],
            null,
            [],
            null,
            1
        );


        // Build an entity with unfetched relationships
        $entityWithAllUnfetched = $programmeMapper->getDomainModel($dbEntityWithAllUnfetched);

        // Then build an entity with those fetched relationships
        $entityWithFetchedItem = $programmeMapper->getDomainModel($dbEntityWithFetchedItem);

        // Assert correct entity with unfetched relationships
        $this->assertEquals($expectedDomainEntityWithAllUnfetched, $entityWithAllUnfetched);

        // Make sure the Fetched and unfetched entities are different as they
        // should have different cache keys
        $this->assertNotEquals($entityWithAllUnfetched, $entityWithFetchedItem);
    }

    /**
     * @dataProvider cacheKeysForProgrammeDataProvider
     */
    public function testGeneratesUniqueCacheKeysForProgramme($expectedKey, $dbEntity)
    {
        $mapperFactory = new MapperFactory();
        $mapper = $mapperFactory->getProgrammeMapper();

        $this->assertEquals($expectedKey, $mapper->getCacheKey($dbEntity));
    }

    public function cacheKeysForProgrammeDataProvider()
    {
        // Cache keys are comma (",") delimited lists of the id fields of itself
        // cares about. The values of each item is either:
        // "&" if the relationship is not cached
        // "!" if the relationship has not been requested
        // "@" if the relationship has been requested and is null
        // The identifier for the entity relationship if the relationship has
        // been requested and is not null

        return [
            // Nothing fetched
            ['{1,!,!,!,!}', ['id' => 1]],
            // Image fetched and set
            ['{1,{2},!,!,!}', ['id' => 1, 'image' => ['id' => 2]]],
            // Image fetched and not set
            ['{1,@,!,!,!}', ['id' => 1, 'image' => null]],
            // Parent fetched
            ['{1,!,{3,!,!,!,!},!,!}', ['id' => 1, 'parent' => ['id' => 3]]],
            // Parent fetched and not set
            ['{1,!,@,!,!}', ['id' => 1, 'parent' => null]],
            // Recursive Parents fetched
            [
                '{1,!,{3,!,{4,!,@,!,!},!,!},!,!}',
                [
                    'id' => 1,
                    'parent' => [
                        'id' => 3,
                        'parent' => [
                            'id' => 4,
                            'parent' => null,
                        ],
                    ],
                ],
            ],

            // MasterBrand fetched and set
            ['{1,!,!,{5,!,!,!},!}', ['id' => 1, 'masterBrand' => ['id' => 5]]],
            // MasterBrand fetched and not set
            ['{1,!,!,@,!}', ['id' => 1, 'masterBrand' => null]],

            // Categories fetched and set
            ['{1,!,!,!,[{6,@},{7,@}]}', ['id' => 1, 'categories' => [['id' => 6, 'parent' => null], ['id' => 7, 'parent' => null]]]],
            // Recursive Categories fetched
            [
                '{1,!,!,!,[{8,@},{10,{11,@}}]}',
                [
                    'id' => 1,
                    'categories' => [
                        [
                            'id' => 8,
                            'parent' => null,
                        ],
                        [
                            'id' => 10,
                            'parent' => [
                                'id' => 11,
                                'parent' => null,
                            ],
                        ],
                    ],
                ],
            ],
            // Categories fetched and not set
            ['{1,!,!,!,[]}', ['id' => 1, 'categories' => []]],
        ];
    }
}
