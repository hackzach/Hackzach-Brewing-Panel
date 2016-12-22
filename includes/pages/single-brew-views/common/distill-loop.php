<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	if(!empty($result['other']['distill'] ) ) { ?>
			<div id="distill_holder">
				<h4>Distills</h4>
				<div id="distills" style="witdh:95%">
<?php				for($i=0;$i<sizeof($result['other']['distill']);$i++) { ?>
					<ul id="distill_run_<?php print $i; ?>"><li>
						<?php print $result['other']['distill'][$i]['date']; ?> 
						<b><i><?php print $result['other']['distill'][$i]['type']; ?></i></b> yields  
						<?php print $result['other']['distill'][$i]['yield']; ?>ml at <?php print $result['other']['distill'][$i]['abv']; ?>% abv
					</li></ul>
<?php				} ?>
				</div>
			</div>
<?php } ?>