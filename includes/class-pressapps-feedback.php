<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://pressapps.co/
 * @since      1.0.0
 *
 * @package    Pressapps_Feedback
 * @subpackage Pressapps_Feedback/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pressapps_Feedback
 * @subpackage Pressapps_Feedback/includes
 * @author     PressApps <support@pressapps.co>
 */
class Pressapps_Feedback {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pressapps_Feedback_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'pressapps-feedback';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pressapps_Feedback_Loader. Orchestrates the hooks of the plugin.
	 * - Pressapps_Feedback_i18n. Defines internationalization functionality.
	 * - Pressapps_Feedback_Admin. Defines all hooks for the admin area.
	 * - Pressapps_Feedback_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pressapps-feedback-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pressapps-feedback-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pressapps-feedback-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pressapps-feedback-public.php';

		$this->loader = new Pressapps_Feedback_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pressapps_Feedback_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pressapps_Feedback_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Pressapps_Feedback_Admin( $this->get_plugin_name(), $this->get_version() );
		// Enqueue admin scripts/styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Customize Plugin action links
		$this->loader->add_action( 'plugin_row_meta',		$plugin_admin, 	'row_links', 10, 2 );
		$this->loader->add_action( 'plugin_action_links_' . $this->get_plugin_name()."/".$this->get_plugin_name().".php", $plugin_admin, 'settings_link' );

		// Register custom post type 'surveys'
		$this->loader->add_action( 'init', 					$plugin_admin, 'register_cpt_surveys');
		
		// Register 'feedback' & 'report' pages submenus
		$this->loader->add_action( 'admin_menu', 			$plugin_admin, 'add_feedback_submenus');
				
		// CPT survey custom column
		$this->loader->add_filter( 'manage_pafb-survey_posts_columns', $plugin_admin, 'set_custom_edit_survey_columns');
		$this->loader->add_action( 'manage_pafb-survey_posts_custom_column',$plugin_admin, 'custom_survey_column', 10, 2 );
		
		// Ajax
		$this->loader->add_action( 'wp_ajax_pafb_get_feedbacks' , $plugin_admin, 'pafb_get_feedbacks', 10, 2 );
		$this->loader->add_action( 'wp_ajax_pafb_delete_feedbacks' , $plugin_admin, 'pafb_delete_feedbacks', 10, 2 );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Pressapps_Feedback_Public( $this->get_plugin_name(), $this->get_version() );

		// Enqueue public-facing scripts/styles
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Register Shortcode
		$this->loader->add_action( 'init'			   , $plugin_public, 'register_shortcodes', 10, 2 );
		
		// Filter post content 
		$this->loader->add_filter( 'the_content'       , $plugin_public, 'filter_content', 10, 2 );

		// Ajax
		$this->loader->add_action( 'wp_ajax_pafb_save_feedback' , $plugin_public, 'ajax_save_feedback', 10, 2 );
		$this->loader->add_action( 'wp_ajax_nopriv_pafb_save_feedback' , $plugin_public, 'ajax_save_feedback', 10, 2 );
		
		// Add inline styles/script in the header
		$this->loader->add_action( 'wp_enqueue_scripts' , $plugin_public, 'add_inline_codes', 10, 2 );
		
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Pressapps_Feedback_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve Survey settings to prepend in selected posts/pages
	 *
	 * @param  $post_type page/post type
	 * @since  1.0.0
	 * @return  array
	 */
	public static function query_survey_settings(){
		$meta_query_args = array(
			'relation' => 'AND',
			array(
				'key'     => 'pafb_survey_settings',
				'value'   => 'posts',
				'compare' => 'like'
			)
		);
		$meta_query = new WP_Meta_Query( $meta_query_args );

		return $meta_query;
	}
}
