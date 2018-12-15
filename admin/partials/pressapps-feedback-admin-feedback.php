<?php

/**
 * Provide a admin feedback area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://pressapps.co/
 * @since      1.0.0
 *
 * @package    Feedback
 * @subpackage Feedback/admin/partials
 */
?>
<?php 	
	global 	$wpdb;
	  		$sk 				= new Skelet("pafb"); 
	    	$survey_id 			= isset( $_GET["pafb-survey"] ) ? (int) $_GET["pafb-survey"] : null; 	
			$prepend_post_id 	= isset( $_GET["pafb-posts"] )  ? (int) $_GET["pafb-posts"]  : null; 	
			$arr_questions 		= $sk->get_meta( $survey_id, "survey_questions" );
			$prepend_to_posts  	= $sk->get_meta( $survey_id, "survey_settings" ,"prepend_to_all" );
	  	  	$arr_columns 		= array();
	  	 	$arr_ratings 		= array(); 
		

	  // Get questions for columns
		if( isset( $arr_questions['pafb_questions_item'] ) ){
			
			foreach ( $arr_questions['pafb_questions_item'] as $key => $value) {
				if( isset( $value["question"] ) ){

					$arr_columns[ "column_".md5( $value["question"] ) ] = $value["question"];
					
					if( isset( $value["type"] ) && $value["type"] == "rating" ){
						$arr_ratings[ "column_".md5( $value["question"] ) ] = array(
							"question" => $value["question"],
							  "column" => "column_".md5( $value["question"] ),
							   "range" => $value["rating_range"],
						);
					}
				}
			}
			
		}

		// Get all surveys
		$options = Pressapps_Feedback_Admin::get_all_surveys();
		            
		// Redirect on page load
		if( ! isset( $survey_id ) && ! empty( $options ) ){

		    	$redirect_post_id = current( array_keys( $options ) );
				echo "<script type='text/javascript'>window.location.href='".admin_url("edit.php?post_type=pafb-survey&page=feedback&pafb-survey=".$redirect_post_id)."';</script>"; 
		    	
		} 
?>
<h1>Feedback</h1>

<?php 
/**
 * Ratings Box
 */
?>
<?php foreach ($arr_ratings as $key => $value) { ?>
	<div class="pafb-rate-box" data-column-id="<?php echo $value["column"];?>" data-range-id="<?php echo $value["range"];?>">
		<h1 class="rate">0</h1>
		<span><?php echo $value["question"];?></span>
	</div>
<?php } ?>
<div class="clear clearfix"></div>
<form method="get" name="pafb-form" action="<?php echo admin_url("edit.php"); ?>">
	<input type="hidden" name="post_type" value="pafb-survey"/>
	<input type="hidden" name="page" value="feedback"/>
	 <?php if( empty( $options ) ){ ?>
		            	<?php 
							echo "<div class=\"clear clearfix\"></div>";
							echo "<div id=\"message\" class=\" notice notice-info  below-h2\">";
							echo "<p>";
							echo sprintf("%s <a href=".admin_url("post-new.php?post_type=pafb-survey").">%s</a>",__("There are no surveys available.", 'pressapps-feedback'),__("Create new Surveys", 'pressapps-feedback') );
							echo "</p>";
							echo "</div>";
						?>
	<?php } ?>
	<table border="0" cellspacing="5" cellpadding="5">
	    <tbody>
	        <tr>
	            <td><?php echo __("Survey",'pressapps-feedback');?>:</td>
	            <td>
		            <select name="pafb-survey" id="pafb-survey" style="width:320px;">
		            <?php if( ! empty( $options ) ){ ?>
			            <?php foreach ($options as $key => $value) {
			            	echo "<option value='".$key."' ".selected( $key, $survey_id ).">".$value."</option>\n";
			            } ?>
		            <?php } ?>
		            </select>
		            <br/>

	            </td>
	            <td></td>
	            <td><?php echo __("Post",'pressapps-feedback');?>:</td>
	            <?php 
	            /**
	             * Optimized query for survey meta to get posts that has the current survey
	             * returns $array associative array 
	             */
	            $posts = $wpdb->get_results($wpdb->prepare( 
				    "SELECT cm.meta_value
				        FROM {$wpdb->prefix}commentmeta AS cm
				        JOIN {$wpdb->prefix}comments AS c ON (c.comment_ID = cm.comment_id)
				        WHERE cm.meta_key = %s AND c.comment_post_ID = %d  
				        GROUP BY cm.meta_value", 
				    'pafb-survey-'.$survey_id.'-prepend-id', $survey_id
				),ARRAY_A);

	            $arr_posts_IDs = array();
				/* Shifts array results to first level array */
				if( $wpdb->num_rows > 0 ){
					foreach ( $posts as $key => $post ) {
						$post = array_shift( $post );
						$arr_posts_IDs[ $key ] = $post;
					}
				}

				$shortcoded_posts = $wpdb->get_results($wpdb->prepare( 
				    "SELECT cm.meta_value
				        FROM {$wpdb->prefix}commentmeta AS cm
				        JOIN {$wpdb->prefix}comments AS c ON (c.comment_ID = cm.comment_id)
				        LEFT JOIN {$wpdb->prefix}commentmeta AS ccm ON (ccm.meta_key = 'pafb-shortcode' AND ccm.meta_value = 1)
				        WHERE (cm.meta_key = %s AND c.comment_post_ID = %d)  
				        GROUP BY cm.meta_value", 
				    'pafb-survey-'.$survey_id.'-prepend-id', $survey_id
				),ARRAY_A);

								/* Shifts array results to first level array */
				if( $wpdb->num_rows > 0 ){
					foreach ( $shortcoded_posts as $key => $post ) {
						$post = array_shift( $post );
						$arr_posts_IDs[ $key ] = $post;
					}
				}
				
			
	            // Retrieve all posts if there's no surveys prepended to posts/pages
	            if( empty( $posts ) ){
	            	$posts = $wpdb->get_results($wpdb->prepare( 
				    "SELECT cm.meta_value
				        FROM {$wpdb->prefix}commentmeta AS cm
				        JOIN {$wpdb->prefix}comments AS c ON (c.comment_ID = cm.comment_id)
				        WHERE  c.comment_post_ID = %d 
				        GROUP BY cm.meta_value", $survey_id
					),ARRAY_A);
	            }

				

				$posts_results = null;
				/* Query posts that have the current survey */
				if( ! empty( $arr_posts_IDs ) ){
					$posts_results = $wpdb->get_results(str_replace("'","",$wpdb->prepare("SELECT ID, post_title, post_type  FROM {$wpdb->prefix}posts WHERE ID IN( %s ) ORDER BY post_title",implode(",",$arr_posts_IDs))));
				}

				if( $wpdb->num_rows > 0 ){
					$i = 0;
					foreach ($posts_results as $key => $post) {
						$prepend_to_posts[ $i ] = $post->post_type;
						$i++;
					}
				}

				if( empty( $prepend_to_posts[0] ) && ! empty( $prepend_to_posts ) ){
					array_splice($prepend_to_posts, 0, 1);
				}

				if( ! empty( $prepend_to_posts ) ){
					foreach( $prepend_to_posts as $post_type ){
						unset( $prepend_to_posts[ $post_type ] );
						if(	in_array( $post_type, array('posts','pages') ) ){
							$ptype = rtrim($post_type,"s");
							$prepend_to_posts[ $post_type ] = $ptype;
						}
					}
				}
				
				$prepend_to_posts = is_array($prepend_to_posts) ? array_unique( $prepend_to_posts ) : array();

				$prepended_post_labels = '';
				if( ! empty( $prepend_to_posts ) ){
					$prepended_post_labels = implode(", ",$prepend_to_posts);
				}
				?>
	            <td>
		            <select name="pafb-posts" style="width:320px;">
		            	<option value='all'><?php _e("All","pressapps"); echo " ".$prepended_post_labels;?></option>
			             <?php 
			             	if( ! empty( $posts_results )  && ! empty( $prepend_to_posts ) ){
					             foreach ( $prepend_to_posts as $post_type	) {
					             	if( ! empty( $post_type ) ) {
						             	echo "<optgroup label=\"".$post_type."\">";
						             	if( ! empty( $posts_results ) && $wpdb->num_rows > 0 && ! empty( $arr_posts_IDs ) ){
						             		foreach ( $posts_results as $key => $post ) {

						             			if( $post->post_type == $post_type ){
						             				echo "<option value='".$post->ID."' ".selected($post->ID,$prepend_post_id).">".$post->post_title."</option>";
						             			}
						             		}
						             	}else{
							             	echo "<option disabled=\"disabled\">".__("No feedbacks to show from",'pressapps-feedback').' '.$post_type."'</option>";
							            }
						             	echo "</optgroup>";
						            }
					             }
				            }
				            else if( ! empty( $posts_results ) )
				            {
					            foreach ( $posts_results as $key => $post ) {
									echo "<option value='".$post->ID."' ".selected($post->ID,$prepend_post_id).">".$post->post_title."</option>";
						        }
						    }
			             ?>
		            </select>
	            </td>
	            <td>
	             	<input type="submit" value="Filter" class="btn button"  />
	            </td>
	            <td></td>
	            
	            <td>
	            	<input type="button" id="pafb_delete_all_feedbacks" value="<?php _e("Delete All","pressapps"); ?>" class="btn button" />
	            </td>

	        </tr>
	        
	    </tbody>
	</table>
</form>

<table id="feedback-table" class="stripe compact" cellspacing="0" width="100%">
	<?php if( ! empty( $options ) ){ ?>
	    <thead>
	        <tr>
	            <?php 
	          	foreach ( $arr_columns as $key => $value ) {
	          		echo "<th>".$value."</th>\n";
	          	}
	          	?>
				<th><?php echo __("Actions","pressapps"); ?></th>
	        </tr>
	     </thead>

		<tfoot>
	        <tr>
	            <?php 
	          	foreach ( $arr_columns as $key => $value ) {
	          		echo "<th>".$value."</th>\n";
	          	}
	          	?>
				<th><?php echo __("Actions","pressapps"); ?></th>
	    	</tr>
	    </tfoot>
	<?php } ?>
</table>


<script type="text/javascript">
	jQuery(document).ready(function() {

			/**
			 * Feedbacks DataTable 
			 */
		     var pafb_table = jQuery('#feedback-table').dataTable( {
		     		// Set dom positions
					"dom": '<"top"i>rt<"bottom"lp><"clear">',
					
		     	 <?php if( ! empty( $arr_columns ) ){ ?>
			    	// Request feedbacks
			        "ajax": {
					    "url": "<?php echo admin_url('admin-ajax.php'); ?>",
					    "type": "POST",
					    "data": function ( d ) {
					        d.action = "pafb_get_feedbacks";
					        d.survey_id = jQuery("select[name=pafb-survey]").val();
					        <?php if( ! empty( $prepend_post_id ) ){?>
					        d.prepend_post_id = jQuery("select[name=pafb-posts]").val();
					        <?php } ?>
					    },
					    /*"success": function( d ){
					    	console.log(d);

					    }*/
					},
				 <?php } ?>
					// Set column IDs
			        "columns": [
			        <?php foreach ( $arr_columns as $key => $val ) {
			         	echo "{data: '".$key."',className: '".$key."'},";
			        }?>
			       
			         	  	  {'data':'action','orderable': false },
			       
			        ],
		       
		        // Entries pagination length
		        "lengthMenu": [[30, 55, 100, -1], [30, 55, 100, '<?php _e("All","pressapps");?>']],
		        drawCallback: function () {
		          var api   = this.api();

		          <?php foreach ( $arr_ratings as $key => $value ) { ?>
			          		var i_<?php     echo $key;?>  = jQuery("table[id=feedback-table] thead tr th.<?php echo $key;?>").index();
						    var total_<?php echo $key;?>  = api.column( i_<?php echo $key;?> ).data().sum();
						    var size_<?php  echo $key;?>  = api.column( i_<?php echo $key;?> ).data().length;
						    var range_<?php echo $key;?>  =  jQuery("div[class=pafb-rate-box][data-column-id=<?php echo $key;?>]").data("range-id");
						    var rate_<?php  echo $key;?>  = parseFloat((( total_<?php echo $key;?> * range_<?php echo $key;?> ) / size_<?php echo $key;?> ) / range_<?php echo $key;?>).toFixed(1); 
						      
						    if( rate_<?php  echo $key;?> > 0){
							    jQuery("div[class=pafb-rate-box][data-column-id='<?php echo $key;?>'] .rate").text(rate_<?php echo $key;?>+'/'+range_<?php echo $key;?>);
							}else{
							  	jQuery("div[class=pafb-rate-box][data-column-id='<?php echo $key;?>'] .rate").text('--');
							}
				 <?php  } ?>
				  
			    },
			    "fnInitComplete": function (oSettings, json) {

				 	// Bind delete function
					jQuery("input.pafb_delete_feedback").bind('click',pafb_bind_delete);
					
			    }
		        
		    } ).DataTable();

			/**
			 * Delete single feedback
			 */
			function pafb_bind_delete(){
							var me = jQuery(this);
							    me.attr("disabled",true);

							var d = confirm('<?php _e("Are you sure you want to delete this feedback?","pressapps"); ?>');
							var feedback_ID = me.data('feedback-id');
							
							if( d ){
								jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>",
									{
										action: 'pafb_delete_feedbacks',
										  fbid: feedback_ID,
									}
								).done(function( d ){
									me.removeAttr("disabled");
							 		pafb_table.row(me.parent("td").parent("tr")).remove().draw();
								});
								
							}else{
								me.removeAttr("disabled");
							}

				
			}

			/**
			 * Delete all feedbacks of a survey
			 */
			jQuery("input[type=button][id=pafb_delete_all_feedbacks]").on('click',function(){
				   
							var me = jQuery(this);
							    me.attr("disabled",true);

							var d = confirm("<?php echo __('Are you sure you want to delete all feedback for this survey?','pressapps-feedback'); ?>");
							if( d ){
								jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>",
									{
										action: 'pafb_delete_feedbacks',
								     survey_id: jQuery("select[id=pafb-survey]").val(),
									}
								).done(function( d ){
									me.removeAttr("disabled");
							 		pafb_table.clear().draw();
								});
								
							}else{
								me.removeAttr("disabled");
							}

				
			});

			jQuery("select[name=pafb-survey]").change(function(){
				jQuery("select[name=pafb-posts]").prop('selectedIndex',0);
				jQuery("select[name=pafb-posts]").attr("disabled",true);
				jQuery("select[name=pafb-posts] option[value='all']").text('<?php _e("Loading...");?>');
				jQuery("form[name=pafb-form]").submit();
			});
	});
</script>

