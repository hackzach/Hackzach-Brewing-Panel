<script type="text/javascript">
(function($) {
	batch = {
		init: function() {
			batch.ajax();
		},
		ajax : function( element ) {
			$.post(ajaxurl, {
               		    		action		:	'batch_lookup_ajax_response',
					sort		:	(element ? element.attr('sort') : '<?php print (!empty($sort) ? $sort : 'serial'); ?>'),
					order		:	(element ? element.attr('order') : '<?php print (!empty($order) ? $order : 'ASC'); ?>'),
					search		:	$('#brew_search').val(),
					pagenum		:	'<?php print $current_page ?>',
					parent		:	'<?php print $post->ID ?>',
					session		:       '<?php print wp_create_nonce( "batch-search" ); ?>'
                		}, function (response) { 
					//put the data in a container
					$('#ajax_response').html(response);
					batch.register_event();
			});
		},
		register_event : function() {
			$('#hackzach_container').off();
			$('#hackzach_container').on('click', 'a.sort', function(e) {
				e.preventDefault();
				batch.ajax($(this));
			});
		},
	}
		batch.init();
})(jQuery);
</script>