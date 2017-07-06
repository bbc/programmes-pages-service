<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\OptionsMapper;

class OptionsMapperTest extends BaseMapperTestCase
{
    public function testBasicOptionsNoHierarchy()
    {
        // just one set of options. no complicated inheritance
        $options = [
            'language' =>  'cy',
            'second_option' =>  null,
        ];

        $expectedOptions = [
            'language' => 'cy',
            'branding_id' => 'br-00002',
            'second_option' =>  null,
            'pulse_survey' => null,
            'brand_2016_layout' => false,
            'brand_2016_layout_use_minimap' => false,
            'show_clip_cards' => true,
            'show_gallery_cards' => true,
            'double_width_first_promo' => false,
            'pid_override' => null,
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
            'ivote' => null,
            'coming_soon' => null,
            'navigation_links' => [],
        ];

        $this->assertEquals(
            new Options($expectedOptions),
            $this->getMapper()->getDomainModel($options)
        );
    }

    public function testInheritanceOfOptions()
    {
        // Create three levels of options
        // and check the ones that are allowed to cascade, do so.
        // Once in the domain model, we don't care about the cascades property
        $childOptions = [
            'language' => 'languageInChild',
        ];

        $parentOptions = [
            'language' => 'languageInParent',
            'live_stream_id' => 'streamIdParent',
        ];

        $grandparentOptions = [
            'language' => 'languageInGrandParent',
            'live_stream_id' => 'streamIdGrandparent',
            'comments_clips_id' => 'clips-id-grandparent',
        ];

        $expectedOptions = [
            'language' => 'languageInChild', // option set in child
            'branding_id' => 'br-00002', // default option
            'pulse_survey' => null,
            'brand_2016_layout' => false,
            'brand_2016_layout_use_minimap' => false,
            'show_clip_cards' => true,
            'show_gallery_cards' => true,
            'double_width_first_promo' => false,
            'pid_override' => null,
            'show_tracklist_inadvance' => false,
            'show_tracklist_timings' => false,
            'show_enhanced_navigation' => false,
            'comments_clips_id' => 'clips-id-grandparent', // cascading option
            'comments_clips_enabled' => false,
            'comments_episodes_id' => null,
            'comments_episodes_enabled' => false,
            'playlister_popularity_enabled' => false,
            'recipes_enabled' => false,
            'brand_layout' => 'availability',
            'promoted_programmes' => [],
            'live_stream_id' => null,
            'live_stream_heading' => null,
            'ivote' => null,
            'coming_soon' => null,
            'navigation_links' => [],
        ];

        $this->assertEquals(
            new Options($expectedOptions),
            $this->getMapper()->getDomainModel($childOptions, $parentOptions, $grandparentOptions)
        );
    }

    private function getMapper(): OptionsMapper
    {
        return new OptionsMapper($this->getMapperFactory());
    }
}
