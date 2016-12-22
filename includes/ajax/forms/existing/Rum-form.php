<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
				<?php include plugin_dir_path(__FILE__)."/common/header.php"; ?>
 				<select onChange="window.forms.load_brew_detail_form(jQuery(this).val());" id="type" name="type">
<?php					for ($j = 0; $j < sizeof($type_list); $j++) {
?>						<option value="<?php print $type_list[$j]['type']; ?>"<?php ( $type_list[$j]['type'] == "Rum" ? print " selected" : print "" );  ?>><?php print $type_list[$j]['type']; ?></option>
<?php					}      ?>
				</select><br>
				<?php include plugin_dir_path(__FILE__)."/common/stages.php"; 
				print_stages_array($this, Array( 
					Array( "frm" , "ferment" , "Fermenting" ) ,
					Array( "dst" , "distill" , "Distilled" ) , 
					Array( "cnd" , "condition" , "Conditioning" ) , 
					Array( "age" , "aging" , "Aging" ) ,
					Array( "btl" , "bottle" , "Bottled" )  
				) , $result , $options );
				?>
					</div>
				</div>
				<br>
				<?php include plugin_dir_path(__FILE__)."/common/gravity_yeast.php"; ?>				
				<?php include plugin_dir_path(__FILE__)."/common/fermentables-loop.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/additions-loop.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/distill-loop.php"; ?>
				<?php include plugin_dir_path(__FILE__)."/common/notes.php"; ?>

			</div>
			<div id="<?php print $this->_token; ?>_stage_details" class="brew-column">
			<div id="notes_holder">
				</select>
				<?php include plugin_dir_path(__FILE__)."/common/stage_notes.php"; 
				print_notes_array($this, Array( 
					Array( "fmt" , "ferment" , "Fermenting" ) , 
					Array( "dst" , "distill" , "Distilling" ) ,
					Array( "cnd" , "condition" , "Conditioning" ) ,
					Array( "age" , "aging" , "Aging" ) ,
					Array( "btl" , "bottle" , "Bottling" ) 
				), $options );

				?>
			</div>
			</div>