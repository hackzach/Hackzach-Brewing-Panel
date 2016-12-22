<?php
if ( ! defined( 'ABSPATH' ) ) exit;
 ?>
	<div id="distill_calculators">
		<div id="yield_calculator" class="brew-calculator">
			<h3>Distill Yield</h3>
			<input size="6" id="yield_fermentable" placeholder="Sugars" onChange="window.calculators.yield_result()">
				<select id="yield_fermentable_measure" onChange="window.calculators.yield_result()">
				 	<option value="1">kg</option>
				 	<option value="0.45359237">lb</option>
				</select><br>
			<input size="6" id="yield_strength" placeholder="Efficiency"  onChange="window.calculators.yield_result()">%<br>
			<input size="6" id="yield_result" placeholder="Yield" readonly>
				<select id="yield_distillate_measure" onChange="window.calculators.yield_result()">
				   <option value="1">Liter</option>
				   <option value="33.8140227018">OZ</option>
			 	   <option value="4.22675283773">Cups</option>			
			   	   <option value="2.11337641887">Pints</option>
				   <option value="1.05668820943">Qt</option>
				   <option value="0.264172052358">Gal</option>
				</select>
		</div>
		<div id="target_sugar_calculator" class="brew-calculator">
			<h3>Target Sugars</h3>
   			<input type="text" placeholder="volume" id="amount" size="4" value="20"> L
   			at <input type="text" placeholder="strength" id="strength" size="4" value="12"> %<br>
			<input type="text" size="6" id="sugar" placeholder="Requires"> kg of sugar
   			<input type="button" value="Calculate" onClick="jQuery('#sugar').val(jQuery('#amount').val() * 0.017 *jQuery('#strength').val() )">
		</div>
		<div id="dilution_calculator" class="brew-calculator">
			<h3>Spirit Dilution</h3>
			<input type="text" id="dilute_total" placeholder="Current Volume (ML)" onChange="window.calculators.dilute_result()" style="width:163px"><br>
			<input type="text" id="dilute_strong" placeholder="Current Strength" onChange="window.calculators.dilute_result()" style="width:163px"><br>
			<input type="text" id="dilute_weak" placeholder="Desired Strength" onChange="window.calculators.dilute_result()" style="width:163px"><br><br>
			Add <input type="text" id="dilute_titrate_result" placeholder="Titration Amount" style="width:136px" readonly><br />
			Totaling <input type="text" id="dilute_total_result" placeholder="Total Volume" style="width:112px" readonly>
		</div>
	</div>
	<div id="separator"><hr></div>
	<div id="yield_calculators">
		<div id="target_sg_calculator" class="brew-calculator">
			<h3>Target Specific Gravity</h3>
			<input type="text" id="target_gravity_abv" placeholder="Alcohol by Volume" onChange="window.calculators.target_gravity_result()"><br>
			<input type="text" id="target_gravity_fg" placeholder="Final Gravity" onChange="window.calculators.target_gravity_result()"><br><br>
			<input type="text" id="target_gravity_result" placeholder="Target Gravity" readonly>
		</div>
		<div id="abv_calculator" class="brew-calculator">
			<h3>Alcohol By Volume</h3>
			<input type="text" id="abv_og" placeholder="Original Specific gravity" onChange="window.calculators.abv_result()"><br>
			<input type="text" id="abv_fg" placeholder="Final Gravity" onChange="window.calculators.abv_result()"><br><br>
			<input type="text" id="abv_result" placeholder="Expected ABV" readonly>
		</div>
		<div id="bottles_calculator" class="brew-calculator">
			<h3>Bottle Yield</h3>
			<input type="text" id="total_volume" placeholder="Total Volume(ML)" onChange="window.calculators.bottles_result()"><br>
			<input type="text" id="bottle_volume" placeholder="Bottle Volume(ML)" onChange="window.calculators.bottles_result()"><br><br>
			<input type="text" id="bottles_result" placeholder="Total # Bottles" readonly>
		</div>
	</div>