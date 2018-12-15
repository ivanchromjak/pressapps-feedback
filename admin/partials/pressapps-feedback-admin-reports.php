<?php
/**
 * Provide a admin reports area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://pressapps.co/
 * @since      1.0.0
 *
 * @package    Feedback
 * @subpackage Feedback/admin/partials
 */

	// Global
	global $wpdb, $sk;
	$sk = new Skelet("pafb");
	$paginate_limit = 40;

	// Get All Surveys
	$options = Pressapps_Feedback_Admin::get_all_surveys();
    // Get Survey ID
	$survey_id = isset( $_GET["pafb-survey"] ) &&  ! empty( $_GET["pafb-survey"] ) || ! empty( $options ) ? @$_GET["pafb-survey"] : 'none';
	// Get Survey Question ID
	$sq_id = isset( $_GET['pafb-survey-questions'] ) && ! empty( $_GET['pafb-survey-questions'] ) ? $_GET['pafb-survey-questions'] : ''; 
	// Prepare array questions types
	$arr_questions_types = array();


	$arr_questions = array();
	if ( ! empty( $survey_id ) ){
		// Get all questions of current survey
		$arr_questions  = $sk->get_meta( $survey_id, "survey_questions" );
	}


	/**
	 * Set active tab
	 * @param  String $tab 
	 * @return echo String
	 */
	function current_tab( $tab ) {

		$current_tab = "engagement";

		if( isset( $_GET["tab"] ) && ! empty( $_GET["tab"] ) ) {

			$current_tab = $_GET["tab"];
		
		}

		if( $tab == $current_tab ){
			echo 'class="sk-section-active"';
		}
	}

		/**
	 * Get rating post IDs
	 * @param  Array 	$rating_posts        
	 * @param  Integer 	$sq_id               
	 * @param  Array 	$arr_rating_posts    
	 * @param  Array 	$arr_questions_types 
	 * @return Array                        
	 */
	function get_rating_post_IDs( $rating_posts, $sq_id, $arr_questions_types, $arr_questions){
		$arr_rating_posts = array();
		$question = isset(  $arr_questions['pafb_questions_item'][ $sq_id ]['question']  ) ? md5( $arr_questions['pafb_questions_item'][ $sq_id ]['question'] ) : null;
		$column = 'column_'.$question;
		
		// Map columns and field types
		foreach( $rating_posts as $key => $post){
			$meta = unserialize( $post->meta_value );
			if( $arr_questions_types[$sq_id ][0] == 'rating' ){
				$arr_rating_posts[ $post->prepend_post_id ][] = $meta[ $column ];
			}else if( $arr_questions_types[$sq_id ][0] == 'positive-negative' ){
				$arr_rating_posts[ $post->prepend_post_id ][] = $meta[ $column ];
			}
			
		} 
	    
	    // Rating compute average
	    $arr_computed_rating_average = array();
	    foreach ( $arr_rating_posts as $key => $value ) {

			if( $arr_questions_types[$sq_id ][0] == 'rating' ){
				
				 $SF = array_sum( $value );
				$TRF = count( $value );
				$RRD = floatval( '.'.$arr_questions_types[ $sq_id ][1] );
				 $RR = $arr_questions_types[ $sq_id ][1] ;
				/** 
				 * Formula:
				 * SF  = Sum of rating all feedbacks
				 * TRF = Total number of Rating Feedback items
				 * RRD = Rating Range in Deicmal
				 * RR  = Rating Range
				 * Average =  ( (  SF / TRF ) / RRD ) / RR 
				 */
				//$average = (  ( $SF / $TRF ) / $RRD  ) / $RR ;
	   			$average =  ( $SF / $TRF ) ;
	   
		    	$arr_computed_rating_average[ $key ] = $average;

			}
		    else if( $arr_questions_types[$sq_id ][0] == 'positive-negative' ){

		    	$positive = isset( $arr_computed_rating_average[ $key ]['positive'] ) ? $arr_computed_rating_average[ $key ]['positive'] : 0;
		    	$negative = isset( $arr_computed_rating_average[ $key ]['negative'] ) ? $arr_computed_rating_average[ $key ]['negative'] : 0;
		    	$response = isset( $arr_computed_rating_average[ $key ]['response'] ) ? $arr_computed_rating_average[ $key ]['response'] : 0;
		    	
		    	foreach( $value as $v ){
		    		if( $v == 0 ) { // positive
		    			$positive++;
		    		}elseif( $v == 1 ) { // negative
			    		$negative++;
			    	}
			    	$response++;
		    	}
		    	$arr_computed_rating_average[ $key ]['positive'] = $positive;
			    $arr_computed_rating_average[ $key ]['negative'] = $negative;
			    $arr_computed_rating_average[ $key ]['response'] = $response;
			    $arr_computed_rating_average[ $key ]['key']      = $key;
			   
			   $order = array();
				foreach ( $arr_computed_rating_average as $k => $row ) {
					 $order[ $k ] = $row['response'];
				}

				array_multisort( $order, SORT_DESC, $arr_computed_rating_average );
				
				$arr_sort_posneg = $arr_computed_rating_average; 
				$arr_computed_rating_average = array();
				
				foreach ($arr_sort_posneg as $kkk => $vv) {
					$kk = $vv['key'];
					$arr_computed_rating_average[ $kk ] = $vv;
				}
				
			}

	    }
	    	return $arr_computed_rating_average;
			
	}
	
	

	if( ! empty( $options ) || $survey_id != 'none' ){
    	// Redirect on page load
		if(  ! isset( $_GET["tab"] )  && empty( $survey_id ) ||  isset( $_GET["tab"] )  && $_GET["tab"] == "engagement" && empty( $survey_id ) || isset( $_GET["tab"] )  && $_GET["tab"] == "engagement"  && empty( $options ) &&  empty( $arr_questions ) ){

			$redirect_post_id = current( array_keys( $options ) );
			echo "<script type='text/javascript'>window.location.href='".admin_url("edit.php?post_type=pafb-survey&page=reports&tab=engagement&pafb-survey=".$redirect_post_id)."&e';</script>"; 
			    	
		}
		
		if(  ! isset( $_GET["tab"] )  && empty( $survey_id ) ||  isset( $_GET["tab"] )  && $_GET["tab"] == "rating" && empty( $survey_id ) || isset( $_GET["tab"] )  && $_GET["tab"] == "rating"  && empty( $options ) &&  empty( $arr_questions ) ){

			$redirect_post_id = current( array_keys( $options ) );
			echo "<script type='text/javascript'>window.location.href='".admin_url("edit.php?post_type=pafb-survey&page=reports&tab=rating&pafb-survey=".$redirect_post_id)."&r';</script>"; 
			    	
		}  

	}
		
?>

<div class="sk-framework sk-option-framework">
<header class="sk-header">
	<h1><?php _e("Reports","pressapps");?></h1>
	<div class="clear"></div>
</header>
<div class="sk-body">
	<div class="sk-nav">
		<ul>
			<li>
				<a href="<?php echo admin_url('edit.php?post_type=pafb-survey&page=reports&tab=engagement'); ?>" <?php current_tab( 'engagement' ); ?>><i class="sk-icon fa fa-area-chart"></i><?php _e("Engagement","pressapps");?></a>
			</li>
			<li>
				<a href="<?php echo admin_url('edit.php?post_type=pafb-survey&page=reports&tab=rating'); ?>" <?php current_tab( 'rating' ); ?> ><i class="sk-icon fa fa-bar-chart"></i><?php _e("Rating","pressapps");?></a>
			</li>
		</ul>
	</div>
<div class="sk-content">
<div class="sk-sections pafb-reports-section">
	<div class="reports-nav"style="padding:10px;">
	<form id="reports-form" action="" method="get">
	
		<?php if( isset( $_GET["tab"] ) && $_GET["tab"] == "engagement" ) { ?>
			<input type="hidden" name="post_type" value="pafb-survey"/>
			<input type="hidden" name="page" value="reports"/>
			<input type="hidden" name="tab" value="engagement"/>
		<?php }else{ ?>
			<input type="hidden" name="post_type" value="pafb-survey"/>
			<input type="hidden" name="page" value="reports"/>
			<input type="hidden" name="tab" value="rating"/>
		<?php } ?>

		<?php if( ! empty( $options ) ){ ?><br/>
			<div style="float:left;">
		    <h2 style="float:left;line-height:10px;font-size:14px;margin-right:10px;"><?php _e("Survey","pressapps");?></h2>
				<select  name="pafb-survey" id="pafb-survey" style="width:320px;">
				    <?php if( ! empty( $options ) ){ ?>
					    <?php foreach ($options as $key => $value) {
					       	echo "<option value='".$key."' ".selected( $key, $survey_id ).">".$value."</option>\n";
					    } ?>
				    <?php } ?>
				</select>
			</div>

		
			<?php if( isset( $_GET["tab"] ) && $_GET["tab"] == "rating" ) { ?>
				<div style="float:left;margin-left:20px;">
				    	<?php if( ! empty( $options ) ){ ?>
							<h2 style="float:left;line-height:10px;font-size:14px;margin-right:10px;"><?php _e("Question","pressapps");?></h2>
							<select onchange='this.form.submit();' name="pafb-survey-questions" id="pafb-survey-questions" style="width:320px;">
								<option value=""><?php _e("Select a question","pressapps");?></option>
								<?php if( ! empty( $arr_questions  ) ) { ?>

									<?php foreach( $arr_questions as $questions ){?>

										    <?php foreach( $questions as $key => $question ){ ?>

														<?php if( in_array( $question["type"], array("rating","positive-negative") ) ) { ?>
															<?php $options_attributes = 'data-type="'.$question["type"].'" data-negative="'.$question['negative'].'" data-positive="'.$question['positive'].'" '; ?>
															<?php $arr_questions_types[$key][0] = $question["type"]; ?>
															<?php $arr_questions_types[$key][1] = isset($question["rating_range"])?$question["rating_range"]:''; ?>
															<option <?php selected($key, isset($_GET['pafb-survey-questions'])?$_GET['pafb-survey-questions']:'' ); ?> value="<?php echo $key;?>" <?php echo $options_attributes; ?> ><?php echo $question["question"];?></option>
														<?php } ?>

											<?php } // end foreach ?>

									<?php } // end foreach ?>

								<?php } //endif empty arr questions ?>
							</select>
						<?php } //endif empty options ?>
				</div>
			<?php } ?>

			<?php if( ! empty( $sq_id ) ||  isset( $_GET["tab"] ) && $_GET["tab"] == "engagement"  ){ ?>

				<select name="sort" style="float:right;">
					<option value="h2l"><?php echo __("High to Low",'pressapps-feedback');?></option>
					<option value="l2h"><?php echo __("Low to High",'pressapps-feedback');?></option>
				</select>
			
			<?php }else if( isset( $_GET["tab"] ) && $_GET["tab"] == "rating"  ) { ?>
				<?php 
					echo "<div class=\"clear clearfix\"></div>";
					echo "<div id=\"message\" class=\" notice notice-info  below-h2\">";
					echo "<p>";
					echo __("Select a question to show the rating data.",'pressapps-feedback');
					echo "</p>";
					echo "</div>";
				?>
			<?php } ?>

		<?php } else{ // else there's no survey available ?>
			<?php 
				echo "<div class=\"clear clearfix\"></div>";
				echo "<div id=\"message\" class=\" notice notice-info  below-h2\">";
				echo "<p>";
				echo sprintf("%s <a href=".admin_url("post-new.php?post_type=pafb-survey").">%s</a>",__("There are no surveys available to display reports.", 'pressapps-feedback'),__("Create new Surveys", 'pressapps-feedback') );
				echo "</p>";
				echo "</div>";
			?>
		<?php } ?>

		<?php if( isset( $_GET["tab"] ) && $_GET["tab"] == "rating" && isset($arr_questions_types[$sq_id ][0]) && $arr_questions_types[$sq_id ][0] == 'positive-negative' ) { ?>
			<select name="karma" style="float:right;width:120px;">
				<option value="positive"><?php _e("Positive","pressapps");?></option>
				<option value="negative"><?php _e("Negative","pressapps");?></option>
			</select>
		<?php } ?>
		
	</form>
	</div>
	<?php if( isset( $_GET["tab"] ) && $_GET["tab"] == "engagement" ) { ?>

		<?php 
		$items = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(*) AS IDS  
				   FROM {$wpdb->prefix}posts AS pt 
				  WHERE ID 
				     IN( 
				     	SELECT cm.meta_value 
				     	  FROM {$wpdb->prefix}commentmeta AS cm 
				     	  JOIN {$wpdb->prefix}comments AS c 
				     	    ON (
				     	    	c.comment_ID = cm.comment_id 
				     	    	AND 
				     	    	c.comment_type = 'pafb_survey'
				     	    	) 
						  WHERE cm.meta_key = %s 
						    AND c.comment_post_ID = %d 
					   GROUP BY cm.meta_value
					   )
				",
				'pafb-survey-'.$survey_id.'-prepend-id', 
				$survey_id
			)
		);
		
		if( $items > 0 ){
			$p = new PAFB_Pagination;
			$p->items($items);
			// Limit entries per page
			$p->limit(40); 
			$p->target("edit.php?post_type=pafb-survey&page=reports&tab=engagement&pafb-survey=".$survey_id);
			// Gets and validates the current page
			if(isset($p->paging)){
				$p->currentPage($_GET[$p->paging]); 
			}
			// Calculates what to show
			$p->calculate(); 
			$p->parameterName('paging');
			 //No. of page away from the current page
			$p->adjacents(1);

			if (!isset($_GET['paging'])) {
				$p->page = 1;
			} else {
				$p->page = $_GET['paging'];
			}
			//Query for limit paging
			$limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;
		} else {
			$limit = "";
		}

		/**
		 * Optimized query for survey meta to get posts that has the current survey
		 * returns $array associative array 
		*/
		
		$posts = $wpdb->get_results(
			$wpdb->prepare(
			"SELECT pt.*,  
				(
					SELECT count(meta_id) 
					  FROM {$wpdb->prefix}commentmeta as pcm 
					 WHERE pcm.meta_key = %s 
					   AND pcm.meta_value = pt.ID 
			    ) 
			    AS fb_count
			  FROM {$wpdb->prefix}posts as pt 
		   	 WHERE ID 
			    IN (	
				    SELECT cm.meta_value 
					  FROM {$wpdb->prefix}commentmeta AS cm 
					  JOIN {$wpdb->prefix}comments AS c 
						ON (
							c.comment_ID = cm.comment_id 
							AND 
							c.comment_type = 'pafb_survey') 
					 WHERE cm.meta_key = %s 
					   AND c.comment_post_ID = %d 
				  GROUP BY cm.meta_value
				) 	  
			      ORDER BY fb_count  
			          DESC {$limit}",
			'pafb-survey-'.$survey_id.'-prepend-id',
			'pafb-survey-'.$survey_id.'-prepend-id', 
			$survey_id
		),ARRAY_A);


		/**
		 * Shifts array results to first level array
		 */
		$arr_posts_IDs = array();
		if( $wpdb->num_rows > 0 ){
			foreach ( $posts as $k => $post ) {
				$arr_posts_IDs[ $k ] = $post;
			}
		}

		if(  empty( $arr_posts_IDs ) && is_numeric( $survey_id ) ){
			echo "<div class=\"clear clearfix\"></div>";
			echo "<div id=\"message\" class=\" notice notice-info  below-h2\">";
			echo "<p>";
			echo __("There are no feedbacks engagement data to show.", 'pressapps-feedback');
			echo "</p>";
			echo "</div>";
		}
		?>

		<?php 
		/**
		 * Prepare data for barchart
		 */
		?>
		<script type="text/javascript">
		var pafb_engangement_data = [];
		<?php
		foreach ( $arr_posts_IDs as $post ) {

		 	$comments =  $wpdb->get_var(
		 		$wpdb->prepare(
		 			"SELECT count(*) AS tc 
		 			   FROM {$wpdb->prefix}commentmeta AS cm 
		 			   JOIN {$wpdb->prefix}comments AS c 
		 			     ON (
		 			     	c.comment_ID = cm.comment_id 
		 			     	AND 
		 			     	c.comment_type = 'pafb_survey'
		 			     	) 
		 		      WHERE cm.meta_key = %s 
		 		        AND c.comment_post_ID = %d 
		 		        AND cm.meta_value = %s",
		 		        'pafb-survey-'.$survey_id.'-prepend-id', 
		 		        $survey_id ,
		 		        $post['ID']
		 		)
		 	);

		 	if( $comments > 0 ){
				echo "pafb_engangement_data.push( [ '".$post['post_title']."' , ".$comments." ] );\n";
			}
		}
		?>
		</script>

		<canvas id="pafb-feedback-chart" style="width:95%;"></canvas>
		
		<?php 
		// Show pagination
		echo "<div class=\"tablenav\">\n";
		echo "  <div class='tablenav-pages'>\n";
		if ($items > 0) {
			echo $p->show();  // Echo out the list of paging. 
		}
		echo "  </div>\n";
		echo "</div>\n";
		?>

	<?php } else if( isset( $_GET["tab"] ) && $_GET["tab"] == "rating" ) { ?>

	<?php 
	/**
	 * Get posts of the current Survey Question
	 * @var $array
	 */
	$question = isset( $arr_questions['pafb_questions_item'][ $sq_id ][ 'question' ] )? md5($arr_questions['pafb_questions_item'][ $sq_id ][ 'question' ]) :null;
	$rating_posts =  $wpdb->get_results(
		 		$wpdb->prepare(
		 			"SELECT cm.*,
		 			   (
		 			   	SELECT meta_value 
		 			   	  FROM {$wpdb->prefix}commentmeta 
		 			   	    AS pcm 
		 			   	 WHERE c.comment_id = pcm.comment_id 
		 			   	   AND pcm.meta_key = CONCAT('pafb-survey-',c.comment_post_ID,'-prepend-id')
					   ) 
		 				 AS prepend_post_id
		 			   FROM {$wpdb->prefix}commentmeta AS cm 
		 			   JOIN {$wpdb->prefix}comments    AS c 
		 			     ON (
		 			     	c.comment_ID 	  = cm.comment_id 
		 			     	AND 
		 			     	c.comment_type 	  = 'pafb_survey'
		 			     	AND
		 			     	c.comment_post_ID = %d
		 			     	AND 
		 			     	cm.meta_key       = 'pafb-survey' 
		 			     	AND 
		 			     	cm.meta_value LIKE %s 
		 			     	) 
		 		      ",
		 		       $survey_id,
		 		      '%"column_'.$question.'"%'
		 		)
	);
   $items = $wpdb->num_rows;
    
    // Setup pagination for ratings
	if( $items > 0 ){
			$p = new PAFB_Pagination;
			$p->items($items);
			// Limit entries per page
			$p->limit($paginate_limit); 
			$p->target("edit.php?post_type=pafb-survey&page=reports&tab=rating&pafb-survey=".$survey_id."&pafb-survey-questions=".$sq_id);
			// Gets and validates the current page
			if(isset($p->paging)){
				$p->currentPage($_GET[$p->paging]); 
			}
			// Calculates what to show
			$p->calculate(); 
			$p->parameterName('paging');
			 //No. of page away from the current page
			$p->adjacents(1);

			if (!isset($_GET['paging'])) {
				$p->page = 1;
			} else {
				$p->page = $_GET['paging'];
			}
			//Query for limit paging
			$limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;
	} else {
			$limit = "";
	}
	?>
	<?php 

	// Sorted Post IDs by Average Ratings
	$arr_order_values = implode(",",array_keys( get_rating_post_IDs( $rating_posts, $sq_id, $arr_questions_types, $arr_questions ) ) );
	
		if( ! empty( $sq_id ) && ! empty( $arr_order_values  ) ) {
			/**
			 * Get all questions prepended posts
			 *
			 * Retrieve posts to allow pagination order
			 * @var Array
			 */
			$rating_posts =  $wpdb->get_results(
					$wpdb->prepare(
				 			"SELECT cm.*,
				 			   (
				 			   	SELECT meta_value 
				 			   	  FROM {$wpdb->prefix}commentmeta 
				 			   	    AS pcm 
				 			   	 WHERE c.comment_id = pcm.comment_id 
				 			   	   AND pcm.meta_key = CONCAT('pafb-survey-',c.comment_post_ID,'-prepend-id')
							   ) 
				 				 AS prepend_post_id
				 			   FROM {$wpdb->prefix}commentmeta AS cm 
				 			   JOIN {$wpdb->prefix}comments    AS c 
				 			     ON (
				 			     	c.comment_ID 	  = cm.comment_id 
				 			     	AND 
				 			     	c.comment_type 	  = 'pafb_survey'
				 			     	AND
				 			     	c.comment_post_ID = %d
				 			     	AND 
				 			     	cm.meta_key       = 'pafb-survey' 
				 			     	AND 
				 			     	cm.meta_value LIKE %s 
				 			     	) 
							     ORDER BY FIELD(prepend_post_id,{$arr_order_values})
							     {$limit}
							   
				 		      ",
				 		       $survey_id,
				 		      '%"column_'.md5( $arr_questions['pafb_questions_item'][ $sq_id ][ 'question' ] ).'"%'
		 			)
			);
		}else if( ! empty( $sq_id ) ){
			echo "<div class=\"clear clearfix\"></div>";
			echo "<div id=\"message\" class=\" notice notice-info  below-h2\">";
			echo "<p>";
			echo __("No data available.", 'pressapps-feedback');
			echo "</p>";
			echo "</div>";
		}
		
			$arr_computed_rating_average = get_rating_post_IDs( $rating_posts, $sq_id,  $arr_questions_types, $arr_questions );
		
	if( isset(  $arr_questions_types[$sq_id ][0]  ) && $arr_questions_types[$sq_id ][0] == 'rating' ){
    ?>

		<script type="text/javascript">
			var pafb_engangement_data = [];
			<?php
			foreach ( $arr_computed_rating_average as $key => $value ) {

			 	$post_title =  $wpdb->get_var(
			 		$wpdb->prepare("SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = %d", $key)
			 	);

			 		echo "pafb_engangement_data.push( [ '".$post_title."' , ".$value." ] ); //{$value} \n";
			}
			?>
		</script>
		<canvas id="pafb-feedback-chart" style="width:95%;"></canvas>
	<?php 
	}
	else if( isset(  $arr_questions_types[$sq_id ][0]  ) && $arr_questions_types[$sq_id ][0] == 'positive-negative' ){
	 ?>
	 	<script type="text/javascript">
			var pafb_rating_data = [];
			<?php
			foreach ( $arr_computed_rating_average as $key => $value ) {

			 	$post_title =  $wpdb->get_var(
			 		$wpdb->prepare("SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = %d", $key)
			 	);

			 		echo "pafb_rating_data.push( {label:'".$post_title."', positive:".$value['positive'].", negative:".$value['negative']."} );\n";
			}
			?>
		</script> 

		<canvas id="pafb-feedback-rating-chart" style="width:95%;"></canvas>
	<?php 
	}
	?>
	
	<?php 
		echo "<div class=\"tablenav\">\n";
		echo "  <div class='tablenav-pages'>\n";
		if ($items > 0) {
			echo $p->show();  // Echo out the list of paging. 
		}
		echo "  </div>\n";
		echo "</div>\n";
		?>
	<?php } ?>
</div>

<script type="text/javascript">
	(function($){
		$(".sk-nav ul li a").on("click",function(){
			 window.location.href = $(this).attr("href");
		});

		$('select[id="pafb-survey-questions"]').change(function(){
			var me = $(this).find('option:selected');
			karmaUpdate(me);
		});

		var me = $('select[id="pafb-survey-questions"]').find('option:selected');
		karmaUpdate(me);

		$('select[name=pafb-survey]').change(function(){
			 $('select[id=pafb-survey-questions] option:selected').removeAttr('selected');
			 karmaUpdate('');
			 $("form[id=reports-form]").submit();
		});

		function karmaUpdate(me){
			var karma_positive = $("select[name=karma] option[value=positive]");
			var karma_negative = $("select[name=karma] option[value=negative]");
			if(me == ''){
				karma_positive.text('Positive');
				karma_negative.text('Negative');
			}else{
				karma_positive.text(me.data('positive'));
				karma_negative.text(me.data('negative'));
			}
		}
	})(jQuery);
</script>	