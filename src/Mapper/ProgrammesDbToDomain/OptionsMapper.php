<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;

class OptionsMapper extends AbstractMapper
{
    private const OPTIONS_DEFAULT_SCHEMA = [
        // admin options
        'branding_id' => [ 'default' => 'br-00002', 'cascades' => true ],
        'language' => [ 'default' => 'en', 'cascades' => true ],
        'pulse_survey' => [ 'default' => null, 'cascades' => true ],
        'brand_layout' => [ 'default' => 'availability', 'cascades' => false ],
        'brand_2016_layout' => [ 'default' => false, 'cascades' => true ],
        'brand_2016_layout_use_minimap' => [ 'default' => false, 'cascades' => true ],
        'show_clip_cards' => [ 'default' => true, 'cascades' => true ],
        'show_gallery_cards' => [ 'default' => true, 'cascades' => true ],
        'double_width_first_promo' => [ 'default' => false, 'cascades' => true ],
        'pid_override_url' => [ 'default' => null, 'cascades' => true ],
        'pid_override_code' => [ 'default' => null, 'cascades' => true ],
        'show_tracklist_inadvance' => [ 'default' => false, 'cascades' => true ],
        'show_tracklist_timings' => [ 'default' => false, 'cascades' => true ],
        'promoted_programmes' => [ 'default' => [], 'cascades' => false ],
        'show_enhanced_navigation' => [ 'default' => false, 'cascades' => true ],

        // local options
        'comments_clips_id' => [ 'default' => null, 'cascades' => true ],
        'comments_clips_enabled' => [ 'default' => false, 'cascades' => true ],
        'comments_episodes_id' => [ 'default' => null, 'cascades' => true ],
        'comments_episodes_enabled' => [ 'default' => false, 'cascades' => true ],
        'playlister_popularity_enabled' => [ 'default' => false, 'cascades' => true ],
        'recipes_enabled' => [ 'default' => false, 'cascades' => true ],
        'live_stream_id' => [ 'default' => null, 'cascades' => false ],
        'live_stream_heading' => [ 'default' => null, 'cascades' => false ],
        'ivote_block' => [ 'default' => null, 'cascades' => false ],
        'comingsoon_block' => [ 'default' => null, 'cascades' => false ],
        'comingsoon_textonly' => [ 'default' => null, 'cascades' => false ],
        'navigation_links' => [ 'default' => [], 'cascades' => false ],
        'bbc_site' => [ 'default' => null, 'cascades' => false ],
        'livepromo_block' => [ 'default' => null, 'cascades' => false ],
        'prioritytext_block' => [ 'default' => null, 'cascades' => false ],
    ];

    private static $defaults = [];

    public function getDomainModel(array $options, array ...$parentEntities)
    {
        // Transform options
        $options = $this->transformOptions($options);

        // $parentEntities must start from the bottom of the hierarchy

        // now loop through parents and apply the data
        // ONLY if the key is allowed to cascade AND
        // the key doesn't already exist lower down.
        foreach ($parentEntities as $parentOptions) {
            $parentOptions = $this->transformOptions($parentOptions);

            foreach ($parentOptions as $key => $value) {
                if (!isset($options[$key]) && (self::OPTIONS_DEFAULT_SCHEMA[$key]['cascades'] ?? false)) {
                    $options[$key] = $value;
                }
            }
        }

        // set default values
        $defaults = $this->getDefaultValues();
        foreach ($defaults as $key => $value) {
            if (!isset($options[$key])) {
                $options[$key] = $value;
            }
        }

        return new Options($options);
    }

    private function getDefaultValues()
    {
        if (!self::$defaults) {
            foreach (self::OPTIONS_DEFAULT_SCHEMA as $key => $value) {
                self::$defaults[$key] = $value['default'];
            }
        }

        return self::$defaults;
    }

    private function transformOptions(array $options): array
    {
        // If brand_2016_layout is null (inherit), these options that can only be
        // set when brand_2016_layout is true should also be inherited
        if (array_key_exists('brand_2016_layout', $options) && is_null($options['brand_2016_layout'])) {
            $options['brand_2016_layout_use_minimap'] = null;
            $options['double_width_first_promo'] = null;
            $options['show_gallery_cards'] = null;
            $options['show_clip_cards'] = null;
        }

        return $options;
    }
}
