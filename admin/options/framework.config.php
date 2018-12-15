<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 * Framework page settings
 */
$settings = array(
    'header_title' => 'Feedback',
    'menu_title'   => 'Feedback',
    'menu_type'    => 'add_submenu_page',
    'menu_slug'    => 'pressapps-feedback',
    'ajax_save'    => false,
);


/**
 * sections and fields option
 * @var array
 */
$options        = array();

/*
 * General options tab and fields settings
 */
$options[]      = array(
    'name'        => 'general',
    'title'       => 'General',
    'icon'        => 'fa fa-cogs',
    'fields'      => array(
        array(
            'id'      => 'access',
            'type'    => 'radio',
            'title'   => 'Who Can Leave Feedback',
            'options' => array(
                'public'    =>  'Public',
                'logged_in' =>  'Logged in users only',
            ),
            'default' => 'public',
            'help'    => 'Require users to login before leaving feedback or allow anybody (public) to leave feedback.',
        ),
        array(
            'id'      => 'progress_bar', 
            'type'    => 'switcher',
            'title'   => 'Display Progress Bar',
            'default' => true,
        ),
    ),
);

/*
 *  Styling options tab and fields settings
 */
$options[]      = array(
    'name'        => 'styling',
    'title'       => 'Styling',
    'icon'        => 'fa fa-paint-brush',
    'fields'      => array(
        array(
            'id'      => 'background', 
            'type'    => 'background',
            'title'   => 'Background',
            'default'      => array(
                'color'      => '#f7f7f7',
            ),
        ),
        array(
            'id'      => 'image_overlay_color', 
            'type'    => 'color_picker',
            'title'   => 'Background Overlay',
        ),
        array(
            'id'      => 'border_radius',
            'type'    => 'number',
            'title'   => 'Border Radius',
            'after'   => ' <i class="sk-text-muted">(px)</i>',
            'default' => '2',
        ),
        array(
            'id'      => 'text_color', 
            'type'    => 'color_picker',
            'title'   => 'Content Text',
            'rgba'    => false,
            'default' => '#222222',
        ),
        array(
            'id'      => 'radio_color', 
            'type'    => 'color_picker',
            'title'   => 'Button Text',
            'rgba'    => false,
            'default' => '#222222',
        ),
        array(
            'id'      => 'radio_background', 
            'type'    => 'color_picker',
            'title'   => 'Button Background',
            'default' => '#E0E0E0',
        ),
        array(
            'id'      => 'controls_color', 
            'type'    => 'color_picker',
            'title'   => 'Selected Button Background',
            'rgba'    => false,
            'default' => '#269fe5',
        ),
        array(
            'id'      => 'navigation_background', 
            'type'    => 'color_picker',
            'title'   => 'Navigation Background',
            'default' => '#222222',
        ),
    ),
);

SkeletFramework::instance( $settings, $options );
