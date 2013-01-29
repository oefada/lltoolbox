<div class="blockImageModuleList"></div>
<script type="text/javascript">
	(function($) {
		$.ajax({
			'url' : '/blocks/images',
			'data' : {
				'block' : $('#blockPageUrl').text()
			},
			'dataType' : 'json',
			'cache' : false,
			'type' : 'POST',
			'success' : function(data, textStatus, jqXHR) {
				$('.blockImageModuleList').empty();
				for (var i = 0; i < data.length; i++) {
					var newImg = $('<img/>');
					newImg.attr('src', data[i]);
					newImg.css({
						'max-width' : '256px',
						'max-height' : '256px',
						'display' : 'inline-block',
						'border-width' : '2px',
						'border-style' : 'solid',
						'border-color' : 'blue',
						'padding' : '1px',
						'margin' : '5px'
					});
					newImg.click(function(e) {
						$('#editorDiv input[name="src"]').val($(this).attr('src')).change();
						jQuery('html,body').animate({
							scrollTop : 300
						}, 'fast', function(e) {
							$('#editorDiv input[name="src"]').effect('highlight', {}, 1000);
						});
					});
					$('.blockImageModuleList').append(newImg);
				}
			}
		});
	})(jQuery); 
</script>
