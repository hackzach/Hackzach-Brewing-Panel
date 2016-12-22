<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
				<?php include plugin_dir_path(__FILE__)."/common/header.php"; ?>	
 				<select onChange="window.forms.load_brew_detail_form(jQuery(this).val());" id="type" name="type">
<?php					for ($j = 0; $j < sizeof($type_list); $j++) {
?>						<option value="<?php print $type_list[$j]['type']; ?>"<?php ( $type_list[$j]['type'] == "Beer" ? print " selected" : print "" );  ?>><?php print $type_list[$j]['type']; ?></option>
<?php					}      ?>
				</select><br>
				<?php include plugin_dir_path(__FILE__)."/common/stages.php"; 
				print_stages_array( Array(
					Array( "frm" , "ferment" , "Fermenting" ) ,
					Array( "btl" , "bottle" , "Bottled" ) ,
					Array( "cnd" , "condition" , "Conditioning" ) 
				) );
				?>
					</div>
				</div>
				<br>
				<?php include plugin_dir_path(__FILE__)."/common/gravity_yeast.php"; ?>	
				<?php include plugin_dir_path(__FILE__)."/common/fermentables.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/hops.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/meta_details.php"; ?>	
			</div>
			<div id="<?php print $this->_token; ?>_stage_details" class="brew-column">
				<div id="notes_holder">
					<?php include plugin_dir_path(__FILE__)."/common/stage_notes.php"; 
					print_notes_array( Array(
						Array( "fmt" , "ferment" , "Fermenting" ) ,
						Array( "btl" , "bottle" , "Bottling" ) ,
						Array( "cnd" , "condition" , "Conditioning" ) 
					) ); ?>
				</div>
			</div>