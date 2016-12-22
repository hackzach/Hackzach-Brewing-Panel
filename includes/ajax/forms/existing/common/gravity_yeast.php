<?php
if ( ! defined( 'ABSPATH' ) ) exit;
		$original_gravity = $result['og']; // Start with original gravity

		 if( !empty( $result['other']['additions'] ) ) { // Are there any additions
			foreach( $result['other']['additions'] as $addition ) {
				if( $addition['gravity'] > $result['og'] ) { // The gravity is theoretical in nature
					$addition['gravity'] = ($addition['gravity']-$result['og'])+1; // Convert to subjective gravity
				}
					$original_gravity += ($addition['gravity']-1); // Add all additions to original gravity
			} 
		} 
?>
		<div id="ferment_holder">
			<div id="ferment">
				Original Sp. Gravity: <input id="og" class="abv" name="og" maxlength="5" style="width:55px" type="text" value="<?php print $result['og'] ?>"><br>
		<?php	if( $result['stage'] == "ferment" ) { ?>
				Current Sp. Gravity: <input id="fg_actl" class="abv" name="fg_actl" maxlength="5" style="width:55px" type="text" value="<?php print $result['actual_fg'] ?>"><br>
		<?php	} else { ?>
				Final Sp. Gravity&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input id="fg_actl" class="abv" name="fg_actl" maxlength="5" style="width:55px" type="text" value="<?php print $result['actual_fg'] ?>"><br>
		<?php	} ?>
				Expected FG&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input id="fg_expt" class="abv" name="fg_expt" placeholder="0.990" maxlength="5" style="width:55px" type="text" value="<?php print $result['expect_fg'] ?>"><br>
				Current ABV&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input id="curr_abv" class="abv" name="curr_abv" maxlength="5" style="width:67px" type="text" readonly><br>
				Expected ABV&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input id="expt_abv" class="abv" name="expt_abv" maxlength="5" style="width:68px" type="text" readonly><br><br>
<?php if($result['type'] == "Kombucha") { ?>
				Culture&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input id="yeast" name="yeast" style="width:185px" type="text" value="<?php print $result['yeast'] ?>"><br>
<?php } else { ?>
				Yeast&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input id="yeast" name="yeast" style="width:185px" type="text" value="<?php print $result['yeast'] ?>"><br>
<?php } ?>
			</div>
		</div>