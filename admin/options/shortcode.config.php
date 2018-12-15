<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 * Global skelet shortcodes variable
 */
  global $skelet_shortcodes;

/**
 * Feedback Survey Shortcode options and settings
 */
$skelet_shortcodes[]     = sk_shortcode_apply_prefix(array(
    'title'      => 'Feedback',
    'shortcodes' => array(
        array(
            'name'      => 'survey',
            'title'     => 'Insert Survey',
            'fields'    => array(
                array(
                    'id'      => 'id',
                    'type'    => 'select',
                    'title'   => 'Surveys',
                    'options' => 'posts',
                    'help'    => 'Insert a survey to a post',
                    'query_args'    => array(
                        'post_type' => 'pafb-survey',
                    ),
                ),
            ),
        ),
    ),
));