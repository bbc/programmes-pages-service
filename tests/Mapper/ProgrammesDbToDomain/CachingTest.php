<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Map\ContactMediaMap;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;

class CachingTest extends BaseCoreEntityMapperTestCase
{
    public function testCacheAccountsForUnfetchedEntities()
    {
        $mapperFactory = new MapperFactory();
        $programmeMapper = $mapperFactory->getCoreEntityMapper();

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
            1,
            new Options(
                [
                    'projectId' => null,
                    'branding_id' => 'br-00002',
                    'language' => 'en',
                    'pulse_survey' => null,
                    'brand_2016_layout' => false,
                    'brand_2016_layout_use_minimap' => false,
                    'show_clip_cards' => true,
                    'show_gallery_cards' => true,
                    'double_width_first_promo' => false,
                    'show_tracklist_inadvance' => false,
                    'show_tracklist_timings' => false,
                    'show_enhanced_navigation' => false,
                    'comments_clips_id' => null,
                    'comments_clips_enabled' => false,
                    'comments_episodes_id' => null,
                    'comments_episodes_enabled' => false,
                    'playlister_popularity_enabled' => false,
                    'recipes_enabled' => false,
                    'brand_layout' => 'availability',
                    'promoted_programmes' => [],
                    'live_stream_id' => null,
                    'live_stream_heading' => null,
                    'pid_override_url' => null,
                    'pid_override_code' => null,
                    'ivote_block' => null,
                    'comingsoon_block' => null,
                    'comingsoon_textonly' => null,
                    'bbc_site' => null,
                    'livepromo_block' => null,
                    'prioritytext_block' => null,
                    'navigation_links' => [],
                    'contact_details' => new ContactMediaMap(),
                ]
            )
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
        $entityWithAllUnfetched = $programmeMapper->getDomainModelForProgramme($dbEntityWithAllUnfetched);

        // Then build an entity with those fetched relationships
        $entityWithFetchedItem = $programmeMapper->getDomainModelForProgramme($dbEntityWithFetchedItem);

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
        $mapper = $mapperFactory->getCoreEntityMapper();

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
            ['{1,!,{3,!,!,!,!},!,!}', ['id' => 1, 'parent' => ['id' => 3, 'type' => 'brand']]],
            // Parent fetched and not set
            ['{1,!,@,!,!}', ['id' => 1, 'parent' => null]],
            // Recursive Parents fetched
            [
                '{1,!,{3,!,{4,!,@,!,!},!,!},!,!}',
                [
                    'id' => 1,
                    'parent' => [
                        'id' => 3,
                        'type' => 'brand',
                        'parent' => [
                            'id' => 4,
                            'type' => 'brand',
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
