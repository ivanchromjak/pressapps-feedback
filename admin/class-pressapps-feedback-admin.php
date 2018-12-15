<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://pressapps.co/
 * @since      1.0.0
 *
 * @package    Pressapps_Feedback
 * @subpackage Pressapps_Feedback/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pressapps_Feedback
 * @subpackage Pressapps_Feedback/admin
 * @author     PressApps <support@pressapps.co>
 */
class Pressapps_Feedback_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pressapps-feedback-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pressapps-feedback-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Creates a new custom post type
     */
	public function register_cpt_surveys(){

		$labels = array(
			'name'               => __( 'Surveys', 'post type general name', 'pressapps-feedback' ),
			'singular_name'      => __( 'Surveys', 'post type singular name', 'pressapps-feedback' ),
			'menu_name'          => __( 'Feedback', 'admin menu', 'pressapps-feedback' ),
			'name_admin_bar'     => __( 'Surveys', 'add new on admin bar', 'pressapps-feedback' ),
			'add_new'            => __( 'Add New', 'Surveys', 'pressapps-feedback' ),
			'add_new_item'       => __( 'Add New Survey', 'pressapps-feedback' ),
			'new_item'           => __( 'New Survey', 'pressapps-feedback' ),
			'edit_item'          => __( 'Edit Survey', 'pressapps-feedback' ),
			'view_item'          => __( 'View Survey', 'pressapps-feedback' ),
			'all_items'          => __( 'All Surveys', 'pressapps-feedback' ),
			'search_items'       => __( 'Search Survey', 'pressapps-feedback' ),
			'parent_item_colon'  => __( 'Parent Survey:', 'pressapps-feedback' ),
			'not_found'          => __( 'No surveys found.', 'pressapps-feedback' ),
			'not_found_in_trash' => __( 'No surveys found in Trash.', 'pressapps-feedback' )
		);

		$args = array(
			'labels'             => $labels,
	        'description'        => __( 'Description.', 'pressapps-feedback' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'survey' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
			'menu_icon'			 => 'dashicons-testimonial'
		);

		$args = apply_filters( 'pafb-survey-cpt-options', $args );

		register_post_type( strtolower(	'pafb-survey' ) , $args );
	}

	/**
	 * Feedback menu
	 */
	public function add_feedback_submenus(){
		add_submenu_page( 'edit.php?post_type=pafb-survey', 'Feedback', 'Feedback', 'manage_options', 'feedback', array($this,'submenu_feedback') );
		add_submenu_page( 'edit.php?post_type=pafb-survey', 'Reports',  'Reports', 'manage_options', 'reports' , array($this,'submenu_reports' ) );
	}

	public function submenu_feedback(){
		include_once plugin_dir_path( __FILE__ ).'partials/pressapps-feedback-admin-feedback.php';
	}

	public function submenu_reports(){
		include_once str_replace("admin/","",plugin_dir_path( __FILE__ ).'includes/class-pressapps-feedback-paginate.php');
		include_once plugin_dir_path( __FILE__ ).'partials/pressapps-feedback-admin-reports.php';
	} 

	/**
	 * Survey Questions metabox
	 */
	public static function metabox_survey_questions(){
		include_once plugin_dir_path( __FILE__ ) .'partials/pressapps-feedback-admin-metabox-survey.php';
	} 

	/**
	 * Add shortcode column to surveys page
	 */
    public function set_custom_edit_survey_columns($columns) {
	    $columns['pafb_survey_shortcode'] = __( 'Shortcode', 'pressapps-feedback' );
	    return $columns;
	}

	public function custom_survey_column( $column, $post_id ) {
	    switch ( $column ) {
			case 'pafb_survey_shortcode' :
	            echo '<code>[pafb_survey id="'. $post_id .'"]</code>'; 
	         break;

	    }
	}

	/**
	 * Adds a link to the plugin settings page
	 */
    public function settings_link( $links ) {

        $settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . $this->plugin_name ), __( 'Settings', 'pressapps-feedback' ) );

        array_unshift( $links, $settings_link );

        return $links;

    }

	/**
	 * Adds links to the plugin links row
	 */
	public function row_links( $links, $file ) {

		if ( strpos( $file, $this->plugin_name . '.php' ) !== false ) {

			$link = '<a href="http://pressapps.co/help/" target="_blank">' . __( 'Help', 'pressapps-feedback' ) . '</a>';

			array_push( $links, $link );

		}

		return $links;

	}
	/**
	 * Retrieves all posts, pages and CPT posts in public
	 * @return array
	 * 
	 * Returns options in associative array for callbacks 
	 * to populate select/checkbox/radio fields options
	 */
	public static function get_all_post_types(){

		$options = array();

		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);

		$output = 'objects'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'

		$post_types = get_post_types( $args, $output, $operator ); 
		// add builtin types
		$options[''] = "None";
		$options['pages'] = "Pages";
		$options['posts'] = "Posts";
		
		foreach ($post_types  as $i => $post_type) {
			if( $i != 'pafb-survey' ){
				$options[ $i ] = $post_type->labels->name;
			}
		}
		
		return $options;
	}

	/**
	 * Retrieves all surveys
	 * @return array
	 */
	public static function get_all_surveys(){

		$options = array();

		$postlist = get_posts( 'post_type=pafb-survey&sort_column=menu_order&sort_order=asc&numberposts=-1&post_status=publish' );
		
		foreach ( $postlist as $post ) {
			$options[$post->ID] = $post->post_title;
		}

		
		return $options;
	}

	/**
	 * Get all feedbacks
	 * @return  json
	 */
	public function pafb_get_feedbacks(){
		
		 			$sk = new Skelet("pafb"); 
				$return = array("data");
		$survey_post_ID = $_POST["survey_id"];
			 $feedbacks = get_comments("post_id=".$survey_post_ID);
		 $arr_questions = $sk->get_meta($survey_post_ID, "survey_questions");
	   $prepend_post_ID = isset( $_POST["prepend_post_id"] ) ? $_POST["prepend_post_id"] : null ;
	 $arr_ratings_label = array();
		 // Sync data columns with re-order
		if( isset( $arr_questions['pafb_questions_item'] ) ){
			
			foreach ($arr_questions['pafb_questions_item'] as $key => $value) {
				if( isset( $value["question"] ) ){
					$arr_columns[ "column_".md5( $value["question"] ) ] = $value["question"];
				}

				if( in_array($value["type"], array( "positive-negative" ) ) ){
					$arr_ratings_label[ "column_".md5( $value["question"] ) ] = array( $value["positive"] , $value["negative"] );
				}

			}
			
		}
		

		/*
		 TODO: Optimize Comments with Meta Query
		 */ 
		if(	isset( $feedbacks ) ){
			foreach ($feedbacks as $value) {
				if( is_numeric( $value->comment_ID ) ){
					
					$feedback_meta_data = get_comment_meta( $value->comment_ID, 'pafb-survey', true );
					 $survey_meta_prepend_id = get_comment_meta( $value->comment_ID, 'pafb-survey-'.$survey_post_ID.'-prepend-id', true );
					
					if(	! empty($feedback_meta_data) &&  ( $survey_meta_prepend_id == $prepend_post_ID ||  ! isset( $prepend_post_ID ) ) ){
						$arr_data = array();
						foreach ($arr_columns as $i => $column) {
								if( in_array( $i, array_keys( $feedback_meta_data ) ,true ) ){
									if( isset( $arr_ratings_label[  $i  ] ) ) { // For positive-negative label mapping
										$arr_data[ $i ] =  $arr_ratings_label[ $i ][ $feedback_meta_data[ $i ] ];
									}else{
										$arr_data[ $i ] =  $feedback_meta_data[ $i ];
									}
								}else{
									$arr_data[ $i ] =  '';
								}
						}
						$arr_data["action"] = "<input type='button' data-feedback-id='". $value->comment_ID ."' class='btn button pafb_delete_feedback' value='".__("Delete","pressapps")."' name='delete_feedback'/>";
						
						$return["data"][] = $arr_data;
						
					}
				}
			}
		}


		if( empty( $return["data"] ) ){
			$return["data"] = array();
		}
		wp_send_json($return);
	}
	/**
	 * Delete feedbacks
	 * @return  json
	 */
	public function pafb_delete_feedbacks(){
			global $wpdb;

			$inputs = $_POST;
			// Delete a feedback of a survey,
			if( isset( $inputs["fbid"] ) && is_numeric( $inputs["fbid"] ) ){
				 wp_delete_comment( $inputs["fbid"], true );
				 delete_comment_meta( $inputs["fbid"] ); ;
			}
			// Delete all feedbacks of a survey,
			if( isset( $inputs["survey_id"] ) && is_numeric( $inputs["survey_id"] ) ){
				 
				 $wpdb->query(
				 	$wpdb->prepare("DELETE FROM {$wpdb->prefix}commentmeta WHERE comment_id IN(SELECT comment_ID FROM {$wpdb->prefix}comments WHERE comment_post_ID = %d)",$inputs["survey_id"])
				 );

				 
				 $wpdb->query(
				 	$wpdb->prepare("DELETE FROM {$wpdb->prefix}comments WHERE comment_post_ID = %d",$inputs["survey_id"])
				 );


			}
			wp_send_json(array("success" => true, "data" => $inputs));
	}
}
