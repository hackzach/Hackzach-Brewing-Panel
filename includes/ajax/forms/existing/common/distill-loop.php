<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
			<div id="distill_holder">
				<h4>Distills <input type="button" id="addDistill" class="add-type" name="distills" value="Add"></h4>
				<div id="distills">
<?php				for($i=0;$i<sizeof($result['other']['distill']);$i++) { ?>
					<div id="distill_run_<?php print $i; ?>">
						<input class="form_data" name="other[distill][%IDX%][date]" style="width:90px;text-align:right;" type="text" value="<?php print $result['other']['distill'][$i]['date']; ?>" placeholder="Date">
						<input class="form_data" name="other[distill][%IDX%][type]" style="width:220px" type="text" placeholder="Run Type" value="<?php print $result['other']['distill'][$i]['type']; ?>">
						<input class="form_data" name="other[distill][%IDX%][abv]" style="width:48px;text-align:right;" type="text" value="<?php print $result['other']['distill'][$i]['abv']; ?>" placeholder="ABV">%&nbsp;
						<input class="form_data" name="other[distill][%IDX%][yield]" style="width:52px;text-align:right;" type="text" value="<?php print $result['other']['distill'][$i]['yield']; ?>" placeholder="Yield">ml
						<input name="delete" value="delete" onclick="jQuery('#distill_run_<?php print $i; ?>').remove()" type="button">
					</div>
<?php				} ?>
				</div>
			</div>