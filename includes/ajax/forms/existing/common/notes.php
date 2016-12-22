<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
			<div id="notes_holder">
				<div id="notes">
					<hr>
						Final ABV&nbsp;&nbsp;&nbsp;: <input id="final_abv" name="other[final_abv]" style="width:55px;text-align:right;" type="text" value="<?php print $result['other']['final_abv'] ?>">%&nbsp;
						Final Yield&nbsp;: <input id="final_yield" name="other[final_yield]" style="width:65px;text-align:right;" type="text" value="<?php print $result['other']['final_yield'] ?>">
						<select name="other[final_yield_units]">
<?php					for ($j = 0; $j < sizeof($measures_list); $j++) {
						if($measures_list[$j]['type'] == "volume") {
?>								<option value="<?php print $measures_list[$j]['id']; ?>"<?php ((!empty($result['other']['final_yield_units']) ? $result['other']['final_yield_units'] : "10") == $measures_list[$j]['id'] ? print " selected" : print "" );  ?>><?php print $measures_list[$j]['name'];($result['other']['final_yield'] > 1 ? print "s" : print "&nbsp;&nbsp;" ); ?></option>
<?php						}
					} 
?>						</select>
					<div id="meta_description"><textarea id="meta_description" name="other[meta_desc]" rows="10" cols="50" placeholder="Meta Description"><?php print $result['other']['meta_desc'] ?></textarea><br></div>
				</div>
			</div>