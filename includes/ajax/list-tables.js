<script type="text/javascript">
	(function($) {
		list = {
			init: function() {
				var list_type = '<?php print $list_type; ?>';
				var timer;
				var delay = 500;
				$('#hackzach_container').off();
				$('#hackzach_container').on('click', '.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a', function(e) {
					e.preventDefault();
					list.loading(false);
					var query = this.search.substring( 1 );
					var data = {
						search	: 	list.__query( query, 'search' ) 	|| '',
						paged	: 	list.__query( query, 'paged' ) 		|| '1',
						order	: 	list.__query( query, 'order' ) 		|| 'asc',
						orderby	: 	list.__query( query, 'orderby' ) 	|| null,
						filter	: 	$('a.nav-tab-active').attr('id'),
						limit	: 	$('select.'+list_type+'_limit').val(),
						page	: 	'<?php print $_REQUEST['page'] ?>'
					};
					list.update( data, list_type );
				});
				$('#hackzach_container').on('click', 'a.delete-action', function(e) {
					e.preventDefault();
					var id = $(this).attr('id');
					var data = {
							action2		:	'delete',
							brew		:	id 	|| null,
							ajax_delete	:	'true'
						};
    					$('#delete-confirm').attr('title','Delete '+list_type+' '+id+'?').dialog({
      						resizable: false,
      						height:180,
						width:350,
      						modal: true,
      							buttons: {
        							"Delete": function() {
					 				list.delete( data, id, list_type );
          								$( this ).dialog( "close" );
        							},
        							Cancel: function() {
          								$( this ).dialog( "close" );
        							}
      							}
					});
				});

				$('#hackzach_container').on('click', 'input#doaction', function(e) {
					e.preventDefault();
 					$('#delete-confirm').attr('title','Delete Selected?').dialog({
      						resizable: false,
      						height:180,
						width:350,
      						modal: true,
      							buttons: {
        							"Delete": function() {
									$('#'+list_type+'_list').submit();
          								$( this ).dialog( "close" );
        							},
        							Cancel: function() {
          								$( this ).dialog( "close" );
        							}
      							}
					});
				});

				$('#hackzach_container').on('click', 'a.nav-tab', function(e) {
					e.preventDefault();
					$('a.nav-tab').removeClass('nav-tab-active');
					$(this).addClass('nav-tab-active');
					var data = {
						paged	: parseInt( $('input[name=paged]').val() ) 	|| '1',
						search	: $('input#'+list_type+'-search-input').val() 	|| null,
						order	: $('input[name=order]').val() 			|| 'asc',
						orderby	: $('input[name=orderby]').val() 		|| null,
						filter	: $('a.nav-tab-active').attr('id'),
						limit	: $('select.'+list_type+'_limit').val(),
						page	: '<?php print $_REQUEST['page'] ?>'
					};
					list.loading(false);
					list.update( data, list_type );
				});

				$('#hackzach_container').on('keyup', 'input[name=paged]', function(e) {
					if ( 13 == e.which ) e.preventDefault();
					list.loading(false);
					var data = {
						paged	: parseInt( $('input[name=paged]').val() ) 	|| '1',
						search	: $('input#'+list_type+'-search-input').val() 	|| null,
						order	: $('input[name=order]').val() 			|| 'asc',
						orderby	: $('input[name=orderby]').val() 		|| null,
						filter	: $('a.nav-tab-active').attr('id'),
						limit	: $('select.'+list_type+'_limit').val(),
						page	: '<?php print $_REQUEST['page'] ?>'
					};
					window.clearTimeout( timer );
					timer = window.setTimeout(function() {
						list.update( data, list_type );
					}, delay);
				});
				$('#hackzach_container').on('change', 'select.'+list_type+'_limit', function() {
					$('select.'+list_type+'_limit').val($(this).val());
					var data = {
						paged	: parseInt( $('input[name=paged]').val() ) 	|| '1',
						search	: $('input#'+list_type+'-search-input').val() 	|| null,
						order	: $('input[name=order]').val() 			|| 'asc',
						orderby	: $('input[name=orderby]').val() 		|| null,
						filter	: $('a.nav-tab-active').attr('id'),
						limit	: $('select.'+list_type+'_limit').val(),
						page	: '<?php print $_REQUEST['page'] ?>'
					};
					window.clearTimeout( timer );
					timer = window.setTimeout(function() {
						list.loading(false);
						list.update( data, list_type );
					}, delay);

				});
				$('#hackzach_container').on('keyup', 'input#'+list_type+'-search-input', function(e) {
					if ( 13 == e.which ) e.preventDefault();
					list.loading(false);
					var data = {
						paged	: parseInt( $('input[name=paged]').val() ) 	|| '1',
						search	: $('input#'+list_type+'-search-input').val() 	|| null,
						order	: $('input[name=order]').val() 			|| 'asc',
						orderby	: $('input[name=orderby]').val() 		|| null,
						filter	: $('a.nav-tab-active').attr('id'),
						limit	: $('select.'+list_type+'_limit').val(),
						page	: '<?php print $_REQUEST['page'] ?>'
					};
					window.clearTimeout( timer );
					timer = window.setTimeout(function() {
						list.update( data, list_type );
					}, delay);
				});
			},
			update: function( data, list_type ) {
				$.ajax({
					url: ajaxurl,
					data: $.extend(
						{
							list_table_nonce	: $('#list_table_nonce').val(),
							action			: ''+list_type+'_list_ajax_response',
						},
						data
					),
					success: function( response ) {
						var response = $.parseJSON( response );
						if ( response.rows.length )
							$('#the-list').html( response.rows );
						if ( response.column_headers.length )
							$('thead tr, tfoot tr').html( response.column_headers );
						if ( response.pagination.bottom.length )
							$('.tablenav.top .pagination').html( $(response.pagination.top).html() );
						if ( response.pagination.top.length )
							$('.tablenav.bottom .pagination').html( $(response.pagination.bottom).html() );
						list.init();
						list.loading(true);
						list.selectAllEvent( list_type );
					}
				});
			},
			delete: function( data, id, list_type ) {
				$.ajax({
					url: ajaxurl,
					data: $.extend(
						{
							list_table_nonce	: $('#list_table_nonce').val(),
							action			: ''+list_type+'_list_ajax_response',
						},
						data
					),
					success: function( response ) {
						$('#ajax_response').append(response);
						$('#row_'+id+'').remove();
						list.init();
					}
				});
			},
			__query: function( query, variable ) {
				var vars = query.split("&");
				for ( var i = 0; i <vars.length; i++ ) {
					var pair = vars[ i ].split("=");
					if ( pair[0] == variable )
						return pair[1];
				}
				return false;
			},
			loading: function(done) {
				if(done) {
					$('#loading').html('');
				} else {
					$('#loading').html('<img src="<?php print admin_url('images/loading.gif'); ?>" width="16px" height="16px">');
				}
			},
			selectAllEvent : function( list_type ) {
					$('#hackzach_container').on('click', '#cb-select-all-1, #cb-select-all-2', function() {
  						var checkedStatus = this.checked;
    						$('#cb-select-all-1, #cb-select-all-2').prop('checked', checkedStatus);
  						$('#'+list_type+'_list tbody tr').find('th:first :checkbox').each(function() {
    							$(this).prop('checked', checkedStatus);
  						});
					});
			},
		}
		list.init();
		})(jQuery);
</script>