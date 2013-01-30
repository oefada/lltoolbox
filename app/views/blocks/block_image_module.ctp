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
				var $pathDiv = $('<div></div>');
				var $pathBox = $('<input type="text" readonly/>');
				if (navigator && navigator.userAgent && navigator.userAgent.indexOf('Macintosh') >= 0) {
					$pathBox.val('smb://images/images/blocks' + $('#blockPageUrl').text());
				} else {
					$pathBox.val('\\\\images\\images\\blocks' + ($('#blockPageUrl').text().replace(/\//g, '\\')));
				}
				$pathBox.click(function(e) {
					this.select();
				});
				$pathDiv.append($pathBox);
				$('.blockImageModuleList').empty().append($pathDiv);
				$('.blockImageModuleList').append($('<div>You must run an imagesync before new images will appear on this page.</div>'));
				for (var i = 0; i < data.length; i++) {
					var $newImg = $('<img/>');
					$newImg.attr('src', data[i]);
					$newImg.css({
						'max-width' : '256px',
						'max-height' : '256px',
						'display' : 'inline-block',
						'border-width' : '2px',
						'border-style' : 'solid',
						'border-color' : 'blue',
						'padding' : '1px',
						'margin' : '5px'
					});
					$newImg.click(function(e) {
						$('#editorDiv input[name="src"]').val('http://img.llsrv.us' + $(this).attr('src')).change();
						jQuery('html,body').animate({
							scrollTop : 300
						}, 'fast', function(e) {
							$('#editorDiv input[name="src"]').effect('highlight', {}, 1000);
						});
					});
					$('.blockImageModuleList').append($newImg);
				}
			}
		});
	})(jQuery); 
</script>
