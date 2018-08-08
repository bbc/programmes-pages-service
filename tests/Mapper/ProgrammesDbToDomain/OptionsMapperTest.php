<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\ValueObject\ContactDetails;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;

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
            'project_space' => null,
            'language' => 'cy',
            'branding_id' => 'br-00002',
            'second_option' =>  null,
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
            'telescope_block' => null,
            'comingsoon_block' => null,
            'comingsoon_textonly' => null,
            'bbc_site' => null,
            'livepromo_block' => null,
            'prioritytext_block' => null,
            'navigation_links' => [],
            'contact_details' => [],
        ];

        $this->assertEquals(
            new Options($expectedOptions),
            (new MapperFactory())->getOptionsMapper()->getDomainModel($options)
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
            'project_space' => 'progs-eastenders',
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
            'show_tracklist_inadvance' => false,
            'show_tracklist_timings' => false,
            'show_enhanced_navigation' => false,
            'comments_clips_id' => 'clips-id-grandparent', // cascading option
            'project_space' => 'progs-eastenders',
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
            'telescope_block' => null,
            'comingsoon_block' => null,
            'comingsoon_textonly' => null,
            'bbc_site' => null,
            'livepromo_block' => null,
            'prioritytext_block' => null,
            'navigation_links' => [],
            'contact_details' => [],
        ];

        $this->assertEquals(
            new Options($expectedOptions),
            (new MapperFactory())->getOptionsMapper()->getDomainModel($childOptions, $parentOptions, $grandparentOptions)
        );
    }

    public function testCanAddMappedContact()
    {
        $options = [
            'language' =>  'cy',
            'second_option' =>  null,
            'contact_details' => [
                [
                    'type' => 'email',
                    'value' => 'emailAAAA@myemail.com',
                    'freetext' => 'Free text',
                ],
                [
                    'type' => 'email',
                    'value' => 'emailBBBB@myemail.com',
                    'freetext' => 'Free text',
                ],
            ],
        ];

        $expectedOptions = [
            'project_space' => null,
            'language' => 'cy',
            'branding_id' => 'br-00002',
            'second_option' =>  null,
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
            'telescope_block' => null,
            'comingsoon_block' => null,
            'comingsoon_textonly' => null,
            'bbc_site' => null,
            'livepromo_block' => null,
            'prioritytext_block' => null,
            'navigation_links' => [],
            'contact_details' => [
                new ContactDetails('email', 'emailAAAA@myemail.com', 'Free text'),
                new ContactDetails('email', 'emailBBBB@myemail.com', 'Free text'),
            ],
        ];

        $this->assertEquals(
            new Options($expectedOptions),
            (new MapperFactory())->getOptionsMapper()->getDomainModel($options)
        );
    }
}
