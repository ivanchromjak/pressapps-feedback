<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

// global skelet metaboxes variable
global $skelet_metaboxes;

/**
 * sections and fields option
 * @var array
 */
$options        = array();

/**
 * Survey Questions metaboxes options and fields settings
 */
$options[]    = sk_apply_prefix(array(
    'id'        => 'survey_questions',
    'title'     => 'Questions',
    'post_type' => 'pafb-survey',
    'context'   => 'normal',
    'priority'  => 'default',
    'sections'  => array(
        array(
            'name'      => 'questions',
            'fields'    => array(
                array(
                    'id'          => 'content',
                    'type'        => 'content',
                    'content'     => '',
                    'options'     => 'callback',
                    'query_args'  => array(
                        'function'    => array('Pressapps_Feedback_Admin','metabox_survey_questions'),
                        'args'        => array()
                    ),
                ),
            )
        )
    ) 
));

/**
 * Survey Settings metaboxes options and fields settings
 */
$options[]    = sk_apply_prefix(array(
    'id'        => 'survey_settings',
    'title'     => 'Settings',
    'post_type' => 'pafb-survey',
    'context'   => 'normal',
    'priority'  => 'default',
    'sections'  => array(
        array(
            'name'      => 'settings',
            'fields'    => array(
                array(
                    'id'          => 'prepend_to_all',
                    'type'        => 'select',
                    'title'       => 'Prepend This Survey To All',
                    'options'     => 'callback',
                    'query_args'  => array(
                        'function'    => array('Pressapps_Feedback_Admin','get_all_post_types'),
                        'args'        => array()
                    ),
                    'attributes'  => array(
                        'multiple'    => 'only-key',
                        'style'       => 'width: 350px; height: 125px;'
                    ),
                ),
                array(
                    'id'          => 'background', 
                    'type'        => 'background',
                    'title'       => 'Background',
                ),
                array(
                    'id'          => 'image_overlay_color', 
                    'type'        => 'color_picker',
                    'title'       => 'Background Overlay',
                ),
                array(
                    'id'          => 'text_color', 
                    'type'        => 'color_picker',
                    'title'       => 'Content Text',
                    'rgba'        => false,
                ),
            ),

        ),

    )
));

// Merge to skelet metaboxes
$skelet_metaboxes = array_merge($skelet_metaboxes,$options);
