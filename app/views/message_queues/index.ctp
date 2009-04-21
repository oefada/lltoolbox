<style>
	.unread td {
		font-weight: bold;
	}
	.rollover td {
		background-color: #ff9 !important;
		cursor: pointer;
	}
	.selected td {
		background-color: #ffc !important;
	}
</style>

<script>
	function rollover(type, item) {
		if (type == 1) {
			item.addClassName('rollover');
		} else {
			item.removeClassName('rollover');
		}
	}
	
	function click(messageQueueId, item) {
		$$('.selected').each(function(s) { s.removeClassName('selected')});
		
		item.addClassName('selected');
		item.removeClassName('unread');
		
		new Ajax.Request('/message_queues/view/'+messageQueueId, {
		  method: 'get',
		  onSuccess: function(transport) {
		    var messageWindow = $('rightPane');
			messageWindow.update(transport.responseText);
		}});
	}
</script>
<? $this->pageTitle = 'Message Queue'?>
<div class="messageQueues index">
<div style="float: left; clear: none; width: 50%" id='messageQueueIndex'>
<?=$this->renderElement('../message_queues/list')?>
</div>
<div style="float: left; clear: none; padding-left: 20px; width: 45%" id='rightPane'>
</div>

</div>