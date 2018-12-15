<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://pressapps.co/
 * @since      1.0.0
 *
 * @package    Pressapps_Feedback
 * @subpackage Pressapps_Feedback/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pressapps_Feedback
 * @subpackage Pressapps_Feedback/public
 * @author     PressApps <support@pressapps.co>
 */
class Pressapps_Feedback_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		global $skelet_pafb;
		$skelet_pafb = new Skelet("pafb");
			
	   
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pressapps-feedback-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pressapps-feedback-public.js', array( 'jquery' ), $this->version, false );
        
    }
	/**
	 *  Register all shortcodes
	 *
	 * @since  1.0.0
	 */
	public function register_shortcodes(){
		
		add_shortcode("pafb_survey",array($this,'add_survey_shortcode'));

	}
	/**
	 * Register the Survey shortcode
	 *
	 * @since  1.0.0
	 */
	public function add_survey_shortcode( $atts = array() ){

		$atts = shortcode_atts( array(
			'id' => null,
		), $atts, 'pafb_survey' );

		$feedback_id = $atts["id"];
		
		return $this->the_feedback_form( $feedback_id, get_post_status( $feedback_id ), false, true );
		
	}

	/**
	 * Survey feedback form
	 *
	 * @since 1.0.0
	 * @param  Integer $feedback_id   Pass the Survey ID
	 */
	public function the_feedback_form( $feedback_id, $post_status, $echo = false, $shortcode = false ){

			global $skelet_pafb;

		    $form_access = $skelet_pafb->get("access");
		    $form_progress_bar = $skelet_pafb->get("progress_bar");
			$feedback_id = (int) $feedback_id;
		
		if( isset( $feedback_id ) && $feedback_id > 0 && $post_status == 'publish'  ){
			$html_form = "";
			$arr_post = get_post( $feedback_id );
			$arr_questions = get_post_meta( $feedback_id , 'pafb_survey_questions' , true );
			 $arr_settings = get_post_meta( $feedback_id , 'pafb_survey_settings'  , true );
			
			
			// Don't show survey forms that are not published
			if( get_post_status( $feedback_id ) != 'publish' || $post_status != 'publish' ){
				return ;
			}

			if(	isset(	$arr_questions['pafb_questions_item'] )	&& ! empty( $arr_questions['pafb_questions_item'] ) ){
				$i = count($arr_questions['pafb_questions_item']);
				
				foreach ( $arr_questions['pafb_questions_item'] as $key => $value ) {
					if( isset(  $value['question']  ) ){
						$arr_questions_reverse[ 'column_'.md5( $value['question'] ) ] = $value;
					}else{
						$arr_questions_reverse[ 'column_'.md5( $value['confirmation'] ) ] = $value;
					}
				}
			}
			$arr_questions_reverse = array_reverse($arr_questions_reverse);

			$html_form .=  "<script type=\"text/javascript\">\n";
			$html_form .= "var pafb_post 		=  ".( ! empty( $arr_post ) ? json_encode( $arr_post ): '[]' ).";\n";
			$html_form .= "var pafb_questions 	= ".( ! empty( $arr_questions_reverse )  ? json_encode( $arr_questions_reverse ) : '[]' ).";\n";
			$html_form .= "var pafb_ajax_url 	= '".admin_url("admin-ajax.php")."';\n";
			$html_form .= "var pafb_post_id 	= '".get_the_ID()."';\n";
			$html_form .= "var pafb_survey_id 	= ".$feedback_id.";\n";
			$html_form .= "pafb_survey_instances.push({
								pafb_survey_id:pafb_survey_id,
								pafb_post: pafb_post,
								pafb_questions:pafb_questions,
								pafb_ajax_url:pafb_ajax_url,
								pafb_post_id:pafb_post_id,
						});";
			$html_form .= "</script>\n";

			$html_form .= "<style type=\"text/css\">\n";
			if( ! empty( $arr_settings ) ){
				$html_form .= "#pafb-feedback-form[data-pafb-id=\"".$feedback_id."\"]{";
					
					if( isset( $arr_settings['pafb_background'] ) ){

						if( isset( $arr_settings['pafb_background']['image'] ) && ! empty( $arr_settings['pafb_background']['image'] ) ){
							$html_form .= "background-image: url(".$arr_settings['pafb_background']['image'].");";
						}
						if( isset( $arr_settings['pafb_background']['repeat'] ) && ! empty( $arr_settings['pafb_background']['repeat'] ) ){
							$html_form .= "background-repeat:".$arr_settings['pafb_background']['repeat'].";";
						}
						if( isset( $arr_settings['pafb_background']['position'] ) && ! empty( $arr_settings['pafb_background']['position'] ) ){
							$html_form .= "background-position:".$arr_settings['pafb_background']['position'].";";
						}
						if( isset( $arr_settings['pafb_background']['attachment'] ) && ! empty( $arr_settings['pafb_background']['attachment'] ) ){
							$html_form .= "background-attachment:".$arr_settings['pafb_background']['attachment'].";";
						}
						if( isset( $arr_settings['pafb_background']['color'] ) && ! empty( $arr_settings['pafb_background']['color'] ) ){
							$html_form .= "background-color:".$arr_settings['pafb_background']['color'].";";
						}

					}
					
					if( isset( $arr_settings['pafb_text_color'] ) && ! empty( $arr_settings['pafb_text_color'] ) ){
							$html_form .= "color: ".$arr_settings['pafb_text_color'].";";
					}
				$html_form .= "}\n";

				if( isset( $arr_settings['pafb_image_overlay_color'] ) && ! empty( $arr_settings['pafb_image_overlay_color'] ) ){
					$html_form .= "div[data-pafb-id=\"".$feedback_id."\"] .pafb-feedback-overlay{";
								$html_form .= "background: ".$arr_settings['pafb_image_overlay_color'].";";
					$html_form .= "}\n";
				}
			}
			$html_form .= "</style>";

			$has_submitted = $this->has_user_submitted( $feedback_id, get_the_ID() );
			if( ! empty( $arr_questions_reverse ) && is_singular() ){

				$html_form .= "<h3 class='pafb-feedback-title'>".$arr_post->post_title."</h3>";
				$html_form .= "<div id=\"pafb-feedback-form\" data-post-id=\"".get_the_ID()."\" data-pafb-id=\"".$feedback_id."\" data-pafb-shortcode=\"".$shortcode."\">\n";
				
					$html_form .= "<div class=\"pafb-feedback-overlay\">"; 
				
						if( $has_submitted <= 0 ){
				
							$html_form .= "<div id=\"pafb-survey-questions\"><ul class=\"items\"></ul></div>";
				
						}else{
				
							foreach( $arr_questions_reverse as $question ){
								if( $question['type'] == 'confirmation' ){
									if( ! empty( $question['confirmation'] ) ){
										$html_form .= "<div class='pafb-confirmation'>".__($question['confirmation'],"pressapps")."</div>";
									}else{
										$html_form .= "<div class='pafb-confirmation'>".__("Thank you for your feedback!","pressapps")."</div>";
									}
								}
							}
				
						}
				
					$html_form .="</div>";
					
					if( $has_submitted <= 0 && ( ( $form_access == 'logged_in' && is_user_logged_in() ) || $form_access == 'public' ) ){
					$html_form .= "			<div id=\"pafb-progress\">";
					$html_form .= "			    <span id=\"pafb-percent\"></span>";
					
					if( $form_progress_bar ){
						$html_form .= "			    <div id=\"pafb-bar\"></div>";
					}
					
					$html_form .= "			</div>";
					$html_form .= "<div class=\"pafb-feedback-controls\">";
					
						$html_form .= "<ul class='navigation'>";
						$html_form .= "<li class='prev'><a href='javascript:;'><i class='pafb-icon-left'></i></a></li>";
						$html_form .= "<li class='next'><a href='javascript:;'><i class='pafb-icon-right'></i></a></li>";
						$html_form .= "<li class='done'><a href='javascript:;'><i class='pafb-icon-right'></i></a></li>";
						$html_form .= "</ul>\n";
					$html_form .= "</div>";
					}

				$html_form .= "</div>\n";
			}

			if( $echo ){
				echo $html_form;
			}else{
				return $html_form;
			}
		}

	}
	/**
	 * Filter 'the_content' and append survey forms
	 *
	 * @since 1.0.0
	 */
	public function filter_content( $content ){

		$prepend = "";

		if ( get_post_type() != 'pafb-survey'  && is_singular() ) {

		 	$survey_IDs = $this->get_survey_by_post_type( get_post_type(), get_post_status());
			
			if( ! empty( $survey_IDs ) ){
			 	foreach( $survey_IDs as $survey_id ){
		    		 	$prepend .= $this->the_feedback_form( $survey_id, get_post_status() );
		    	}
		    }
		
		}
		
		$content = $content.$prepend;
		
		return $content;	
	}
	/**
	 * Checks user has submitted a feedback
	 * @param  Integer  $survey_id 
	 * @return boolean            
	 */
	public function has_user_submitted( $survey_id, $page_id ){
		
		global $skelet_pafb, $wpdb, $user_ID;

		$form_access = $skelet_pafb->get("access");

		if( $form_access == 'public' ) {

		 $tc = $wpdb->get_var($wpdb->prepare(
	 		"SELECT count(*) as tc
	 			FROM {$wpdb->prefix}commentmeta AS cm
			JOIN {$wpdb->prefix}comments AS c 
				ON (c.comment_ID = cm.comment_id AND c.comment_type = 'pafb_survey')
			WHERE 
			cm.meta_key = %s 
			AND 
			c.comment_post_ID = %d 
			AND 
			c.comment_author_IP = %s
			AND 
			cm.meta_value = %s
			"
			,'pafb-survey-'.$survey_id.'-prepend-id'
		    ,$survey_id
		    ,$this->get_the_user_ip()
		    ,$page_id
		    ));

		} else if( $form_access == 'logged_in' ) {

			$tc = $wpdb->get_var($wpdb->prepare(
	 		"SELECT count(*) as tc
	 			FROM {$wpdb->prefix}commentmeta AS cm
			JOIN {$wpdb->prefix}comments AS c 
				ON (c.comment_ID = cm.comment_id AND c.comment_type = 'pafb_survey')
			WHERE 
			cm.meta_key = %s 
			AND 
			c.comment_post_ID = %d 
			AND 
			c.user_id = %s
			AND 
			cm.meta_value = %s
			"
			,'pafb-survey-'.$survey_id.'-prepend-id'
		    ,$survey_id
		    ,$user_ID
		    ,$page_id
		    ));
		}
		
		return $tc;
		
	}
	/**
	 * Get Survey form
	 *
	 * @since  1.0.0
	 * @param  String $post_type 
	 * @param  String $post_status
	 * @return Boolean       
	 */
    public function get_survey_by_post_type( $post_type, $post_status ){
    	
    	global $wpdb;

    	if( $post_type != "pafb-survey" ){

    		if( in_array( $post_type, array("post","page") ) ) {
    		
    			$post_type = $post_type."s";
    		
    		}
    		
    		$posts = $wpdb->get_results($wpdb->prepare("SELECT post_id 
    			FROM {$wpdb->prefix}postmeta 
    			WHERE 
    			meta_key = 'pafb_survey_settings' 
    			AND 
    			meta_value LIKE %s
    			AND 
    			meta_value LIKE %s
    			GROUP BY 
    			post_id
    			", "%".$post_type."%","%pafb_prepend_to_all%"), ARRAY_A);

    		if( $wpdb->num_rows > 0 ) {

    			$arr_posts_IDs = array();
    			foreach ( $posts as $key => $post ) {
						$post = array_shift( $post );
						$arr_posts_IDs[ $key ] = $post;
				}
	    	
	    		return $arr_posts_IDs;
	    	
	    	}
	   }

    }
	/**
	 * Saves the user feedback of a survey
	 */
	public function ajax_save_feedback(){
			global $user_ID;
			$inputs = $_POST;

			$data = array(
			      'comment_post_ID' => $inputs['survey_id'],
			      'comment_content' => 'User Review',
			         'comment_type' => 'pafb_survey',
			              'user_id' => $user_ID,
			         'comment_date' => current_time('mysql'),
			     'comment_approved' => 0,
			    'comment_author_IP' => $this->get_the_user_ip() ,
			);

			$comment_id  = wp_insert_comment($data);

			add_comment_meta( $comment_id, 'pafb-survey', $inputs['fb'] );
			add_comment_meta( $comment_id, 'pafb-survey-'.$inputs['survey_id'].'-prepend-id', $inputs['post_ID'] );
			add_comment_meta( $comment_id, 'pafb-shortcode', $inputs['is_shortcode'] );
			
			wp_send_json( array("success" => true) );
	}
	/**
	 * Get client IP address
	 */
	public  function get_the_user_ip(){
			if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				//check ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				//to check ip is pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return apply_filters( 'wpb_get_ip', $ip );
	}
	/**
	 * Add inline codes
	 */
	public function add_inline_codes(){
			$skelet_pafb = new Skelet("pafb");

		    echo "<script type='text/javascript'>var pafb_survey_instances = []; </script>\n";

		    $arr_settings = $skelet_pafb->get("options");
		    
		     $html_form = "";

		    if( ! empty( $arr_settings ) ){
				$html_form .= "<style type=\"text/css\">\n";
					$html_form .= "#pafb-feedback-form{";
						
						if( isset( $arr_settings['pafb_background'] ) ){

							if( isset( $arr_settings['pafb_background']['image'] )  && ! empty(  $arr_settings['pafb_background']['image'] ) ){
								$html_form .= "background-image: url(".$arr_settings['pafb_background']['image'].");";
							}
							if( isset( $arr_settings['pafb_background']['repeat'] ) && ! empty( $arr_settings['pafb_background']['repeat'] ) ){
								$html_form .= "background-repeat:".$arr_settings['pafb_background']['repeat'].";";
							}
							if( isset( $arr_settings['pafb_background']['position'] ) && ! empty( $arr_settings['pafb_background']['position'] ) ){
								$html_form .= "background-position:".$arr_settings['pafb_background']['position'].";";
							}
							if( isset( $arr_settings['pafb_background']['attachment'] ) && ! empty( $arr_settings['pafb_background']['attachment'] ) ){
								$html_form .= "background-attachment:".$arr_settings['pafb_background']['attachment'].";";
							}
							if( isset( $arr_settings['pafb_background']['color'] ) && ! empty( $arr_settings['pafb_background']['color'] ) ){
								$html_form .= "background-color:".$arr_settings['pafb_background']['color'].";";
							}

						}
						
						if( isset( $arr_settings['pafb_border_radius'] ) && ! empty( $arr_settings['pafb_border_radius'] ) ){
								$html_form .= "border-radius: ".$arr_settings['pafb_border_radius']."px;";
						}
						if( isset( $arr_settings['pafb_text_color'] ) && ! empty( $arr_settings['pafb_text_color'] ) ){
								$html_form .= "color: ".$arr_settings['pafb_text_color'].";";
						}
					$html_form .= "}\n";

					if( isset( $arr_settings['pafb_image_overlay_color'] ) && ! empty( $arr_settings['pafb_image_overlay_color'] ) ){
					$html_form .= " .pafb-feedback-overlay{";
								$html_form .= "background: ".$arr_settings['pafb_image_overlay_color'].";";
						if( isset( $arr_settings['pafb_border_radius'] ) && ! empty( $arr_settings['pafb_border_radius'] ) ){
								$html_form .= "border-radius: ".$arr_settings['pafb_border_radius']."px ".$arr_settings['pafb_border_radius']."px 0px 0px;";
						}
					$html_form .= "}\n";
					}
					
					if( isset( $arr_settings['pafb_controls_color'] ) && ! empty( $arr_settings['pafb_controls_color'] ) ){
					$html_form .= "  #pafb-feedback-form #pafb-bar {";
								$html_form .= "background-color: ".$arr_settings['pafb_controls_color']." !important ;";
					$html_form .= "}\n";
					
					$html_form .= "  #pafb-survey-questions [data-type='rating'] input[type='radio']:checked ~ label, #pafb-survey-questions [data-type='choice'] input[type='radio']:checked ~ label, #pafb-survey-questions [data-type='positive-negative'] input[type='radio']:checked ~ label {";
								$html_form .= "background-color: ".$arr_settings['pafb_controls_color'].";";
					$html_form .= "}\n";
					}

					if( isset( $arr_settings['pafb_radio_color'] ) && ! empty( $arr_settings['pafb_radio_color'] ) ){
					$html_form .= "  #pafb-survey-questions input[type='radio'] ~ label {";
								$html_form .= "color: ".$arr_settings['pafb_radio_color'].";";
					$html_form .= "}\n";
					}

					if( isset( $arr_settings['pafb_radio_background'] ) && ! empty( $arr_settings['pafb_radio_background'] ) ){
					$html_form .= "  #pafb-survey-questions input[type='radio'] ~ label {";
								$html_form .= "background-color: ".$arr_settings['pafb_radio_background'].";";
					$html_form .= "}\n";
					}

					if( isset( $arr_settings['pafb_navigation_background'] ) && ! empty( $arr_settings['pafb_navigation_background'] ) ){
					$html_form .= "  .pafb-feedback-controls {";
								$html_form .= "background-color: ".$arr_settings['pafb_navigation_background'].";";
					$html_form .= "}\n";
					}

					if( isset( $arr_settings['pafb_border_radius'] ) && ! empty( $arr_settings['pafb_border_radius'] ) ){
					$html_form .= " #pafb-survey-questions textarea, #pafb-survey-questions input[type='text'], #pafb-survey-questions [data-type='choice'] label, #pafb-survey-questions [data-type='positive-negative'] label, #pafb-survey-questions [data-type='rating'] label {";
								$html_form .= "border-radius: ".$arr_settings['pafb_border_radius']."px;";
					$html_form .= "}\n";
					$html_form .= " #pafb-survey-questions [data-type='choice'] input[type='radio']:empty ~ label:before, #pafb-survey-questions [data-type='positive-negative'] input[type='radio']:empty ~ label:before{";
								$html_form .= "border-radius: ".$arr_settings['pafb_border_radius']."px 0 0 ".$arr_settings['pafb_border_radius']."px;";
					$html_form .= "}\n";
					}

				$html_form .= "</style>";
				echo $html_form;
			}
			
	}

}
