<?php
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
<script type="text/javascript">
(function($) {
	forms = {
		init : function() {
			forms.load_brew_detail_form( $( '#type' ).val() );
<?php if('hackzach_brewing_panel_edit' === $_REQUEST['page']) { ?>
			forms.lockBrew();
<?php } ?>
			$(document).keydown(function(e) { // https://css-tricks.com/snippets/javascript/javascript-keycodes/
        			if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) { // Ctrl + s
					forms.saveBrew();
					e.preventDefault(); // Stops from original hotkey from firing
        			}
        			if (e.keyCode == 88 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) { // Ctrl + x
					tb_show('Calculators','#TB_inline?width=600&height=550&inlineId=calculators');
					e.preventDefault(); // Stops from original hotkey from firing
        			}
    			});
		},
		register_events : function() {
			$('#hackzach_container').off();
<?php if('hackzach_brewing_panel_edit' === $_REQUEST['page']) { ?>
			$(window).on('beforeunload', function(){ // Fired on Refresh, close
				forms.unlockBrew();
			});
			$(window).on('unload', function(){ // Fired on Navigate away from page
				forms.unlockBrew();
			});

			$('#hackzach_container').on('click', 'a.nav-tab', function(e) {
				e.preventDefault();
				$('a.nav-tab').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				forms.load_brew_detail_form( $( '#type' ).val(), $(this).attr('id') );
			});
<?php } ?>
		
			$('#hackzach_container').on('click', 'input.add-type', function(e) {
				e.preventDefault();
				var func = $(this).attr('id');	
				var arg  = $(this).attr('name');
				window.forms[func](arg); 
			});
		
			$('#hackzach_container').on('change', 'input.abv', function(e) {
				e.preventDefault();
				forms.calc_abv( <?php print (!empty($original_gravity) ? "parseFloat('".$original_gravity."')" : "parseFloat($('#og').val())" ); ?>, parseFloat($('#fg_actl').val()), 'curr_abv' );
				forms.calc_abv( <?php print (!empty($original_gravity) ? "parseFloat('".$original_gravity."')" : "parseFloat($('#og').val())" ); ?>, parseFloat($('#fg_expt').val()), 'expt_abv' );
			});
		},
		saveBrew : function() {
			$(window).off('beforeunload');
			jQuery('#brew_form').submit();
		},
<?php if('hackzach_brewing_panel_edit' === $_REQUEST['page']) { ?>
		lockBrew : function() {
			$.post(ajaxurl, {
				action	:     	'brew_lock_ajax_response', 
				serial	: 	'<?php print $brew ?>',
				request	:	'lock'
			}, function(response) {
				if(parseInt(response) < 1) {
					if(parseInt(response) == 0) {
						tb_show('Warning','#TB_inline?width=300&height=100&inlineId=lock-error');
					}
					else if(parseInt(response) == -1) {
						tb_show('Warning','#TB_inline?width=300&height=100&inlineId=in-use');
					}
				}
			});
		},
		unlockBrew : function() {
			$.post(ajaxurl, {
				action	:     	'brew_lock_ajax_response', 
				serial	: 	'<?php print $brew ?>',
				request	:	'unlock'
			}, function(response) {
				if(parseInt(response) == 0) {
					tb_show('Warning','#TB_inline?width=300&height=100&inlineId=unlock-error');
				}
			});
		},
<?php } ?>

		reindexer : function(container, items) {
			var counter = 0;
				container.children().each(function() {
						$(this).children('.'+items+'').each(function(){
						$(this).attr('name', $(this).attr('name').replace('%IDX%', counter));
					});
					counter++;
				});
		},

		reindex_all : function() {
			if($('#fermentables').children().length !==0) 	forms.reindexer( $('#fermentables'), 'form_data');
			if($('#hops').children().length !==0) 		forms.reindexer( $('#hops'), 'form_data');
			if($('#additions').children().length !==0) 	forms.reindexer( $('#additions'), 'form_data');
			if($('#distills').children().length !==0) 		forms.reindexer( $('#distills'), 'form_data');
		},
		load_brew_detail_form : function( form_type, tab ) {
			this_tab = (tab ? tab : '<?php print $tab ?>');
			$( '#brew_detail_form' ).html('<center><img src="<?php print plugins_url( 'hackzach-brewing-panel/assets/ajax-loader.gif' ) ?>" title="Loading..." /></center>');
			$.post(ajaxurl, {
						action		:	'brew_forms_ajax_response',
						form_name	:	form_type,  
<?php if('hackzach_brewing_panel_edit' === $_REQUEST['page']) { ?>
						edit		:	'true',
<?php	} ?>
						brew		:	'<?php print $brew ?>',
						tab		:	this_tab
                		}, function (response) { 
					//put the data in a container
					$('#brew_detail_form').html(response);
					$('#brew_detail_form').append('<input type="hidden" name="tab" value="'+this_tab+'" />');
					$('#frm_holder').datetimepicker( { step:5 , format:'Y-m-d H:i:s' , value: $('#frm_holder').val() } );
					$('#dst_holder').datetimepicker( { step:5 , format:'Y-m-d H:i:s' , value: $('#dst_holder').val() } );
					$('#cnd_holder').datetimepicker( { step:5 , format:'Y-m-d H:i:s' , value: $('#cnd_holder').val() } );
					$('#age_holder').datetimepicker( { step:5 , format:'Y-m-d H:i:s' , value: $('#age_holder').val() } );
					$('#btl_holder').datetimepicker( { step:5 , format:'Y-m-d H:i:s' , value: $('#btl_holder').val() } );

					$('#frm_datepicker').toggle(function(){ $('#frm_holder').show().datetimepicker('show').hide(); }, function() { $('#frm_holder').show().datetimepicker('hide').hide(); });
					$('#dst_datepicker').toggle(function(){ $('#dst_holder').show().datetimepicker('show').hide(); }, function() { $('#dst_holder').show().datetimepicker('hide').hide(); });
					$('#cnd_datepicker').toggle(function(){ $('#cnd_holder').show().datetimepicker('show').hide(); }, function() { $('#cnd_holder').show().datetimepicker('hide').hide(); });
					$('#age_datepicker').toggle(function(){ $('#age_holder').show().datetimepicker('show').hide(); }, function() { $('#age_holder').show().datetimepicker('hide').hide(); });
					$('#btl_datepicker').toggle(function(){ $('#btl_holder').show().datetimepicker('show').hide(); }, function() { $('#btl_holder').show().datetimepicker('hide').hide(); });

				forms.calc_abv( <?php print (!empty($original_gravity) ? "parseFloat('".$original_gravity."')" : "parseFloat($('#og').val())" ); ?>, $('#fg_actl').val(), 'curr_abv' );
				forms.calc_abv( <?php print (!empty($original_gravity) ? "parseFloat('".$original_gravity."')" : "parseFloat($('#og').val())" ); ?>, $('#fg_expt').val(), 'expt_abv' );
				forms.register_events();
			});

		},

		noteEditable : function( divID ) {
			$('#'+divID+'_editable').show();
			$('#'+divID+'_view').hide();
			window.forms.setCursorPosition($('#'+divID+'_textarea'), $('#'+divID+'_textarea').val().length);
		},

		noteViewable : function( divID ) {
			$('#'+divID+'_view').show();
			$('#'+divID+'_editable').hide();
			$('#'+divID+'_note').html($('#'+divID+'_textarea').val());
		},

		noteSave : function( divID, event ) {
			if(!event.shiftKey&&event.keyCode==13){ // Enter saves, Shift+Enter returns in textarea
				event.preventDefault();
				$('#'+divID+'_view').show();
				$('#'+divID+'_editable').hide();
				$('#'+divID+'_note').html($('#'+divID+'_textarea').val());
				$('#'+divID+'_textarea').unbind('keydown');
			}
		},
		addAddition : function( divID ) {
			var n = $("#" + divID + "").children().length;
			$("#" + divID + "").append(
			"		<div id=\"addition_"+n+"\">\n" +
			"						<input class=\"form_data\" name=\"other[additions][%IDX%][date]\" type=\"hidden\">\n" +
			"						<select class=\"form_data\" name=\"other[additions][%IDX%][id]\">\n" +
<?php
					for ($i = 0; $i < sizeof($fermentable_list); $i++) {
						if($fermentable_list[$i]['category'] == "Sugar" || $fermentable_list[$i]['category'] == "Adjunct") {
?>
			"							<option value=\"<?php print $fermentable_list[$i]['id']; ?>\"><?php print $fermentable_list[$i]['name']; ?></option>\n" +
<?php						}
					}
?>
			"						</select>\n" +
			"						<input class=\"form_data\" name=\"other[additions][%IDX%][amount]\" style=\"width:60px;text-align:right;\" type=\"text\" placeholder=\"Weight\">\n" +
			"						<select class=\"form_data\" name=\"other[additions][%IDX%][measure]\">\n" +
<?php
					for ($i = 0; $i < sizeof($measures_list); $i++) {
						if($measures_list[$i]['type'] == "weight") {
?>
			"							<option value=\"<?php print $measures_list[$i]['id']; ?>\" <?php print ($measures_list[$i]['id'] == "5" ? "selected" : ""); ?>><?php print $measures_list[$i]['name']; ?></option>\n" +
<?php						}
					}
?>
			"						</select>\n" +
			"						<input class=\"form_data\" name=\"other[additions][%IDX%][gravity]\" style=\"width:60px;text-align:right;\" type=\"text\" placeholder=\"Gravity\" onChange=\"if(parseFloat(jQuery(this).val())<<?php print $result['og']; ?>)window.forms.calc_abv( parseFloat(<?php print ($original_gravity-1); ?>)+parseFloat(jQuery(this).val()) , parseFloat(jQuery('#fg_expt').val()) , 'expt_abv' )\">\n" +
			"						<input name=\"delete\" value=\"delete\" onclick=\"window.forms.calc_abv( parseFloat(<?php print ($original_gravity); ?>) , parseFloat(jQuery('#fg_expt').val()) , 'expt_abv' );jQuery('#addition_"+n+"').remove()\" type=\"button\">\n" +
			"					</div>\n");
			forms.get_date($("#addition_"+n+" [name='other[additions][%IDX%][date]']"));
		},
		addDistill : function( divID ) {
			var n = $("#" + divID + "").children().length;
			$("#" + divID + "").append(
			"		<div id=\"distill_run_"+n+"\">\n" +
			"						<input class=\"form_data\" name=\"other[distill][%IDX%][date]\" style=\"width:90px;text-align:right;\" type=\"text\" placeholder=\"Date\">\n" +
			"						<input class=\"form_data\" name=\"other[distill][%IDX%][type]\" style=\"width:220px\" type=\"text\" placeholder=\"Run Type\">\n" +
			"						<input class=\"form_data\" name=\"other[distill][%IDX%][abv]\" style=\"width:48px;text-align:right;\" type=\"text\" placeholder=\"ABV\">%&nbsp;\n" +
			"						<input class=\"form_data\" name=\"other[distill][%IDX%][yield]\" style=\"width:52px;text-align:right;\" type=\"text\" placeholder=\"Yield\">ml\n" +
			"						<input name=\"delete\" value=\"delete\" onClick=\"jQuery('#distill_run_"+n+"').remove()\" type=\"button\">\n" +
			"					</div>\n");
		},
		addFermentable : function( divID ) {
			var n = $("#" + divID + "").children().length;
			$("#" + divID + "").append(
			"			<div id=\"fermentable_" + n + "\">\n" +
			"				<select class=\"form_data\" name=\"fermentable[%IDX%][id]\">\n" +
<?php
					for ($i = 0; $i < sizeof($fermentable_list); $i++) {
?>
			"					<option value=\"<?php print $fermentable_list[$i]['id']; ?>\"><?php print $fermentable_list[$i]['name']; ?></option>\n" +
<?php					}
?>
			"				</select>\n" +
			"				<input class=\"form_data\" name=\"fermentable[%IDX%][amount]\" style=\"width:75px;text-align:right;\" type=\"text\" placeholder=\"Amount\">\n" +
			"				<select class=\"form_data\" name=\"fermentable[%IDX%][measure]\">\n" +
<?php
					for ($i = 0; $i < sizeof($measures_list); $i++) {
						if($measures_list[$i]['type'] == "weight") {
?>
			"							<option value=\"<?php print $measures_list[$i]['id']; ?>\" <?php print ($measures_list[$i]['id'] == "5" ?  "selected" : ""); ?>><?php print $measures_list[$i]['name']; ?></option>\n" +
<?php						}
					}
?>
			"				</select>\n" +
			"				<input type=\"button\" name=\"delete\" value=\"delete\" onClick=\"jQuery('#fermentable_" + n + "').remove()\">\n" +
			"			</div>\n" +
			"		");
		},
		addHop : function( divID ) {
			var n = $("#" + divID + "").children().length;
			$("#" + divID + "").append(
			"			<div id=\"hop_" + n + "\">\n" +
			"				<select class=\"form_data\" name=\"other[hops][%IDX%][id]\">\n" +
<?php
					for ($i = 0; $i < sizeof($hops_list); $i++) {
?>
			"					<option value=\"<?php print $hops_list[$i]['id']; ?>\"><?php print $hops_list[$i]['name']; ?></option>\n" +
<?php					}
?>
			"				</select>\n" +
			"				<input class=\"form_data\" name=\"other[hops][%IDX%][amount]\" style=\"width:75px;text-align:right;\" type=\"text\" placeholder=\"Amount\">\n" +
			"				<select class=\"form_data\" name=\"other[hops][%IDX%][measure]\">\n" +
<?php
					for ($i = 0; $i < sizeof($measures_list); $i++) {
						if($measures_list[$i]['type'] == "weight") {
?>
			"							<option value=\"<?php print $measures_list[$i]['id']; ?>\" <?php print ($measures_list[$i]['id'] == "4" ? "selected" : ""); ?>><?php print $measures_list[$i]['name']; ?></option>\n" +
<?php						}
					}
?>
			"				</select>\n" +
			"				<input class=\"form_data\" name=\"other[hops][%IDX%][time]\" style=\"width:95px;text-align:right;\"  type=\"text\" placeholder=\"Boil Time\">\n" +
			"				<input type=\"button\" name=\"delete\" value=\"delete\" onClick=\"jQuery('#hop_" + n + "').remove()\">\n" +
			"			</div>\n" +
			"		");
		},
		addNote : function( divID, formID, stage ) {
			var n = $("#" + divID + "").children().length;
			$("#" + divID + "").prepend(
			"	<div id=\""+divID+"_"+(n+1)+"\">\n" +
			"		<div id=\""+divID+"_"+(n+1)+"_editable\" class=\"note_editable\" style=\"display:block\">\n" +
			"			<span onClick=\"window.forms.noteViewable('"+divID+"_"+(n+1)+"');\">\n" +
			"			<span id=\""+divID+"_"+(n+1)+"_date\"><strong><?php print date_format(date_create(current_time( 'mysql' )), $options['hbp_date_format']); ?> by <?php print $this->functions->get_display_name(get_current_user_id()) ?></strong></span> <input name=\"delete\" value=\"delete\" onClick=\"jQuery('#"+divID+"_"+(n+1)+"').remove();\" type=\"button\"></span>\n" +
			"			<input id=\""+divID+"_"+(n+1)+"_stage\" class=\"form_data\" name=\""+formID+"_notes["+n+"][stage]\" type=\"hidden\" value=\""+stage+"\">\n" +
			"			<textarea class=\"form_data\" name=\""+formID+"_notes["+n+"][notes]\" rows=\"6\" cols=\"69\" id=\""+divID+"_"+(n+1)+"_textarea\" onkeydown=\"window.forms.noteSave('"+divID+"_"+(n+1)+"', event);\" placeholder=\"Enter Notes\"></textarea>\n" +
			"		</div>\n" +
			"		<div id=\""+divID+"_"+(n+1)+"_view\" class=\"note_view\" style=\"display:none\" onClick=\"window.forms.noteEditable('"+divID+"_"+(n+1)+"');\">\n" +
			"			<span id=\""+divID+"_"+(n+1)+"_date\"><strong><?php print date_format(date_create(current_time( 'mysql' )), $options['hbp_date_format']); ?> by <?php print $this->functions->get_display_name(get_current_user_id()) ?></strong></span><br>\n" +
			"			<p id=\""+divID+"_"+(n+1)+"_note\"></p>\n" +
			"		</div>\n" +
			"	</div>\n");
		 	forms.setCursorPosition($('#'+divID+'_'+(n+1)+'_textarea'),$('#'+divID+'_'+(n+1)+'_textarea').val().length);
		},
		deleteNote : function( divID, formID, n ) {
			$("#"+ divID + "").append(
				"<input class=\"form_data\" name=\""+formID+"_notes["+n+"][delete]\" type=\"hidden\" value=\"true\">"
			).attr("style", "display:none");
		},
		calc_abv : function( og , fg , resultDiv ) {
			if((!isNaN(og) && og != '') && (!isNaN(fg) && fg != '') && ( (fg > 0.000) && (og > 0.000) ) ) {
				$('#'+resultDiv+'').val(((og-fg)*131).toFixed(3)+'%');
			}
		},
		get_date : function( divId ) {
			var d = new Date();
			var month = d.getMonth()+1;
			var day = d.getDate();
			divId.val(((''+month).length<2 ? '0' : '') + month + '/' +
 				((''+day).length<2 ? '0' : '') + day + '/' +
				d.getFullYear());
		},
		setCursorPosition : function(context, position) {
			if(context.length == 0) return context;
    			input = context[0];

    			if (input.createTextRange) {
				var range = input.createTextRange();
        			range.collapse(true);
        			range.moveEnd('character', position);
        			range.moveStart('character', position);
        			range.select();
    			} else if (input.setSelectionRange) {
        			input.focus();
        			input.setSelectionRange(position, position);
    			}

    			return this;
		},
		setSelection : function(selectionStart, selectionEnd) {
    			if(this.length == 0) return this;
    			input = this[0];

    			if (input.createTextRange) {
				var range = input.createTextRange();
        			range.collapse(true);
        			range.moveEnd('character', selectionEnd);
        			range.moveStart('character', selectionStart);
        			range.select();
    			} else if (input.setSelectionRange) {
        			input.focus();
        			input.setSelectionRange(selectionStart, selectionEnd);
    			}

    			return this;
		},
		getCaret : function(el) {
        		if (el.prop("selectionStart")) {
            			return el.prop("selectionStart");
        		}
			else if (document.selection) {
            			el.focus();

            			var r = document.selection.createRange();
            			if (r == null) {
              	  			return 0;
            			}

            			var re = el.createTextRange(),
                    		rc = re.duplicate();
            			re.moveToBookmark(r.getBookmark());
            			rc.setEndPoint('EndToStart', re);

            			return rc.text.length;
        		}
        		return 0;
		},
		appendAtCaret : function(element, caret, text) {
        		var value = element.val();
        		if (caret != value.length) {
            			var startPos = element.prop("selectionStart");
            			var scrollTop = element.scrollTop;
	    			element.val(value.substring(0, caret) + text + value.substring(caret, value.length));
            			element.prop("selectionStart", startPos + text.length);
            			element.prop("selectionEnd", startPos + text.length);
            			element.scrollTop = scrollTop;
        		} else if (caret == 0) {
            			element.val(text + value);
        		} else {
            			element.val(value + text);
        		}
		},
	},
	calculators = {
			init : function() {
				var final_yield = $('#brew_form input#final_yield').val();
				if( !isNaN(final_yield) && final_yield != '') {
					$('#dilute_total').val(final_yield);
					$('#total_volume').val(final_yield);
					calculators.yield_result();
				}
				var final_abv = $('#brew_form input#final_abv').val();
				if( !isNaN(final_abv) && final_abv != '') {
					$('#dilute_strong').val(final_abv);
					calculators.dilute_result();
				}
			},
			yield_result : function() {
				var yield_fermentable 		= Number( $('#yield_fermentable').val() );
				var yield_fermentable_measure 	= Number( $('#yield_fermentable_measure').val() );
				var yield_strength 		= Number( $('#yield_strength').val() );
				var yield_distillate_measure 	= Number( $('#yield_distillate_measure').val() );
				if((!isNaN(yield_strength) && yield_strength != '') &&
					(!isNaN(yield_fermentable) && yield_fermentable != '')) {
					var yield_result = Math.round( ( yield_fermentable * 0.55 * yield_fermentable_measure / ( yield_strength / 100 ) ) * yield_distillate_measure * 100 ) / 100;
					$('#yield_result').val( yield_result );
				}
			},

			target_gravity_result : function() {
				var target_gravity_abv=$('#target_gravity_abv').val();
				var target_gravity_fg=$('#target_gravity_fg').val();
				if((!isNaN(target_gravity_abv) && target_gravity_abv != '') &&
					(!isNaN(target_gravity_fg) && target_gravity_fg != '')) {
						var target_gravity_result = ( (target_gravity_abv/129)+parseFloat(target_gravity_fg) ).toFixed(3);
						$('#target_gravity_result').val( target_gravity_result );
				}
			},

			abv_result : function() {
				var abv_og=$('#abv_og').val();
				var abv_fg=$('#abv_fg').val();
				if((!isNaN(abv_og) && abv_og != '') &&
					(!isNaN(abv_fg) && abv_fg != '')) {
						$('#abv_result').val(((abv_og-abv_fg)*131).toFixed(3)+'%');
				}
			},

			dilute_result : function() {
				var total=parseInt( $('#dilute_total').val() );
				var weak=$('#dilute_weak').val();
				var strong=$('#dilute_strong').val();
				if((!isNaN(total) && total != '') &&
					(!isNaN(weak) && weak != '') &&
						(!isNaN(strong) && strong != '')) {
					var titrate = parseInt( ( total * ( ( strong/weak )-1 ) ) );
					$('#dilute_titrate_result').val(titrate+'ml');
					$('#dilute_total_result').val((total+titrate)+'ml');
				}
			},

			bottles_result : function() {
				var total_volume 		= Number( $('#total_volume').val() );
				var bottle_volume	 	= Number( $('#bottle_volume').val() );

				if((!isNaN(bottle_volume) && bottle_volume != '') &&
					(!isNaN(total_volume) && total_volume != '')) {
						var bottle_result = Math.floor( total_volume / bottle_volume );
						$('#bottles_result').val( bottle_result+' bottles' );
				}
			},
	}
		forms.init();
})(jQuery);
</script>