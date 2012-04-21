<style>
	table#Schedulomatic {
		width: auto;
	}
	table#Schedulomatic tr td {
		padding: 10px;
	}
	table#Schedulomatic tr:nth-child(even) {
		background: #eeffee;
	}
	table#Schedulomatic tr:nth-child(odd) {
		background: #e8e8ff;
	}
</style>
<div>
	<div>
		<h3 style="font-size: 16px; padding: 0;">Featured Auction</h3>
	</div>
	<div style="float:right;background:#eee;padding:8px;margin:16px;">
		<div>
			<h4>Today is:</h4>
		</div>
		<div>
			<a href="#" onclick="jQuery('#scheduleDate').val('<?php echo date('Y-m-d');?>');jQuery('#GoButton').click();return false;"><?php echo date('Y-m-d');?></a>
		</div>
		<?php if (isset($lastDate)):
		?>
		<div>
			<h4>Last Scheduled Date:</h4>
		</div>
		<div>
			<a href="#" onclick="jQuery('#scheduleDate').val('<?php echo $lastDate;?>');jQuery('#GoButton').click();return false;"><?php echo $lastDate;?></a>
		</div>
		<?php endif;?>
		<?php if (isset($futureDates)&&is_array($futureDates)&&$futureDates):
		?><div>
		<h4>Future Scheduled Dates:</h4>
		</div>
		<?php foreach ($futureDates as $fd):
		?>
		<div>
			<a href="#" onclick="jQuery('#scheduleDate').val('<?php echo $fd;?>');jQuery('#GoButton').click();return false;"><?php echo $fd;?></a>
		</div>
		<?php endforeach;?>
		<?php endif;?>
	</div>
	<div style="float:left;">
		<form method="get">
			<?php echo $datePicker->picker('scheduleDate', array(
				'label' => 'Select a date to schedule: ',
				'value' => (isset($scheduleDate) ? $scheduleDate : '')
			));
			?>
			<button id="GoButton">
				Go
			</button>
		</form>
	</div>
	<div style="clear:both;"></div>
	<br/>
	<?php if (isset($scheduleDate)):
	?>
	<div>
		<h2>Listing and Destination Page Featured Auction Scheduling</h2>
	</div>
	<br/>
	<div>
		<div>
			<div style="display:inline-block;width:420px;text-align:center;font-weight:bold;">
				Pages module appears on:
			</div>
			<div style="display:inline-block;width:420px;text-align:center;font-weight:bold;">
				Pages to link to:
			</div>
		</div>
		<table id="Schedulomatic"></table>
	</div>
	<br/>
	<div style="background:#eee;font-size:16pt;font-weight:bold;padding:8px;color:#666666;" id="MrStatus"></div>
	<pre style="background:#eee;font-family:Courier;display:none;" id="jsonDebug"></pre>
	<?php else:?>
	<div>
		<h2>Pick a date to schedule, and then press the Go button.</h2>
		<div>
			Or, click on of the date links on the right hand side.
		</div>
	</div>
	<?php endif;?>
</div>
<script type="text/javascript">
	function updateStatus(message, color) {
		var $ = jQuery;
		var $statusBar = $('#MrStatus');
		$statusBar.text(message).css('background-color', color);
	}

	function addNewRow(a, b) {
		if( typeof a == 'undefined') {
			a = '';
		}
		if( typeof b == 'undefined') {
			b = '';
		}
		var $ = jQuery, $sch = $('#Schedulomatic'), $newCell, $newText;
		var $newRow = $('<tr/>');

		var $textSource = $('<textarea/>');
		$textSource.addClass('sourcePage').text(a).attr('rows', a.split("\n").length);
		$textSource.change(doChange).keyup(doChange);
		var $textDestination = $('<textarea/>');
		$textDestination.addClass('destinationPage').text(b).attr('rows', b.split("\n").length);
		$textDestination.change(doChange).keyup(doChange);
		var $tdA = $('<td/>');
		$tdA.append($textSource);
		$newRow.append($tdA);
		var $tdB = $('<td/>');
		$tdB.append($textDestination);
		$newRow.append($tdB);
		var $tdX = $('<td/>');
		$tdX.append('X');
		$tdX.click(function(e) {
			$(this).parent().remove();
			doChange();
		});
		$newRow.append($tdX);

		$sch.append($newRow);

	}

	function doChange() {
		var $ = jQuery, $sch = $('#Schedulomatic');
		var data = [];
		$sch.find('tr').each(function(i) {
			var a, b, record = [];
			var $textSource = $(this).find('textarea.sourcePage');
			var $textDest = $(this).find('textarea.destinationPage');
			a = $textSource.val();
			b = $textDest.val();
			$textSource.attr('rows', a.split("\n").length);
			$textDest.attr('rows', b.split("\n").length);
			a = $.trim(a);
			b = $.trim(b);
			var source, dest, newSource = [], newDest = [];
			source = a.split("\n");
			dest = b.split("\n");

			for(var i = 0; i < source.length; i++) {
				var value = $.trim(source[i]).replace(/^(http:\/\/|)[^\/]*\//, '/').replace(/\?.*$/, '');
				if(value.charAt(0) == '/') {
					newSource.push(value);
				}
			}
			for(var i = 0; i < dest.length; i++) {
				var value = $.trim(dest[i]).replace(/^(http:\/\/|)[^\/]*\//, '/').replace(/\?.*$/, '');
				if(value.charAt(0) == '/') {
					newDest.push(value);
				}
			}
			if(newSource.length > 0 && newDest.length > 0) {
				data.push({
					'source' : newSource,
					'destination' : newDest
				});
			}
		});
		$('#jsonDebug').text(JSON.stringify(data));
		updateStatus('Unsaved changes', '#ffffdd');
		if($sch.find('tr').length > 0 && ($sch.find('tr').length == data.length)) {
			addNewRow();
		}
		return data;
	}

	jQuery(function() {
		var $ = jQuery;
		var $addLink = $('<a href="#" style="display:inline-block;padding:10px;">Add row</a>');
		jQuery('#Schedulomatic').parent().append($addLink);
		$addLink.click(function(e) {
			addNewRow();
			e.preventDefault();
		});
		jQuery('#Schedulomatic').parent().append($('<br/>'));
		var $saveButton = $('<button>Save</button>');
		jQuery('#Schedulomatic').parent().append($saveButton);
		$saveButton.click(function(e) {
			e.preventDefault();
			updateStatus('Saving...', '#ddddff');
			$.ajax({
				'type' : 'post',
				'data' : {
					'saveData' : doChange()
				},
				'success' : function(d, t, j) {
					updateStatus('Saved!  Reloading page...', '#ddffdd');
					$('#jsonDebug').text(d);
					setTimeout(function() {
						window.location.reload();
					}, 1250);
				},
				'error' : function() {
					updateStatus('Error', '#ffdddd');
				},
			});
		});
	});

</script>
<script type="text/javascript">var data =<?php echo isset($merchDataJSON) ? ($merchDataJSON) : '[]';?>
		;
		for(var i = 0; i < data.length; i++) {
			var value = data[i];
			if(( typeof value.source == 'object') && ( typeof value.destination == 'object')) {
				addNewRow(value.source.join("\n"), value.destination.join("\n"));
			}
		}
</script>
</script>
<script type="text/javascript">
	jQuery(function() {
		addNewRow();
	});

</script>
