<?php
/**
 * Feedbaack Survey metaboxes for questions
 */
		// Load Skelet
		$sk = new Skelet("pafb");
		$post_ID = isset($_GET['post'])?$_GET['post']:0;
		$survey_items = $sk->get_meta($post_ID,'survey_questions','questions_item');

		// Translatable labels in js render
		$arr_L10n_labels = array();
		$arr_L10n_labels[] = __("Question","pressapps");
		$arr_L10n_labels[] = __("Choices","pressapps");
		$arr_L10n_labels[] = __("Multiple Choice","pressapps");
		$arr_L10n_labels[] = __("Required","pressapps");
		$arr_L10n_labels[] = __("Positive","pressapps");
		$arr_L10n_labels[] = __("Negative","pressapps");
		$arr_L10n_labels[] = __("Rating Range","pressapps");
		$arr_L10n_labels[] = __("Placeholder Text","pressapps");
		$arr_L10n_labels[] = __("Confirmation","pressapps");
		
		echo "<script type='text/javascript'>\n";
		echo "var pafb_survey_items = [];\n";
		// Load saved survey questions/items
		if( ! empty( $survey_items ) ) {
			foreach ($survey_items as $item) {
				echo "pafb_survey_items.push(". json_encode( $item ).");\n";
			}
		}
		// Load translatable labels
		echo "var pafb_labels = {\n";
		if( ! empty( $arr_L10n_labels ) ){
			foreach ($arr_L10n_labels as $label) {
				echo "\t".str_replace(" ","_",strtolower($label)).":'".$label."',\n";
			}
		}
		echo "}\n";

		echo "</script>\n";

		echo "<div>\n";

		echo "<ul class=\"survey-category\">\n";
				echo "<li>";
						echo "<a href=\"javascript:;\" data-type=\"positive-negative\" class=\"button survey-positive-negative\">";
						echo "<span class=\"survey-add-icon dashicons dashicons-plus\"></span>";
						echo __("Positive/Negative","pressapps")."</a>";
				echo "</li>\n";
				echo "<li>";
						echo "<a href=\"javascript:;\"  data-type=\"choice\" class=\"button survey-choice\">";
						echo "<span class=\"survey-add-icon dashicons dashicons-plus\"></span>";
						echo __("Choice","pressapps")."</a>";
				echo "</li>\n";
				echo "<li>";
						echo "<a href=\"javascript:;\"  data-type=\"rating\" class=\"button survey-rating\">";
						echo "<span class=\"survey-add-icon dashicons dashicons-plus\"></span>";
						echo __("Rating","pressapps")."</a>";
				echo "</li>\n";
				echo "<li>";
						echo "<a href=\"javascript:;\"  data-type=\"email\" class=\"button survey-email-field\">";
						echo "<span class=\"survey-add-icon dashicons dashicons-plus\"></span>";
						echo __("Email","pressapps")."</a>";
				echo "</li>\n";
				echo "<li>";
						echo "<a href=\"javascript:;\"  data-type=\"singlefield\" class=\"button survey-singletext-field\">";
						echo "<span class=\"survey-add-icon dashicons dashicons-plus\"></span>";
						echo __("Text","pressapps")."</a>";
				echo "</li>\n";
				echo "<li>";
						echo "<a href=\"javascript:;\"  data-type=\"multiplefield\" class=\"button survey-multitext-field\">";
						echo "<span class=\"survey-add-icon dashicons dashicons-plus\"></span>";
						echo __("Comment","pressapps")."</a>";
				echo "</li>\n";
				echo "<li>";
						echo "<a href=\"javascript:;\"  data-type=\"confirmation\" class=\"button survey-confirmation-field\">";
						echo __("Confirmation","pressapps")."</a>";
				echo "</li>\n";
				echo "<li class=\"last\">";
						echo "<div class=\"survey-spinner\"></div>";
				echo "</li>\n";
		echo "</ul>\n";
	echo "<div class=\"wrap-survey-section\">";
			echo "<div class='survey-noitem text-center' style='display:none;'>";
				$label_load_tpl = sprintf("<a href='javascript:;' class='add-sample-survey-quetions'>%s</a>",__("Add sample questions.", "pressapps"));
				echo __("No survey questions to show. ".$label_load_tpl, "pressapps");
			echo "</div>";
	echo "</div>\n";

echo "</div>\n";