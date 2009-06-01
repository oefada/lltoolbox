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
		//item.removeClassName('unread');
		
		new Ajax.Request('/message_queues/view/'+messageQueueId, {
		  method: 'get',
		  onSuccess: function(transport) {
		    var messageWindow = $('rightPane');
			messageWindow.update(transport.responseText);
		}});
	}
	
	function complete(item) {
			var row = item.up("tr");
			if (item.checked) {
				var status = 'read';
			} else {
				var status = 'unread';
			}
			new Ajax.Request('/message_queues/change_status/status:'+status+'/messageQueueId:'+item.value, {
			  method: 'get',
			  onLoading: function(request) { Element.show('spinner'); },
			  onSuccess: function(request) { Element.hide('spinner');
					var clone = $(row.cloneNode(true));
					clone.removeClassName('rollover');
					clone.removeClassName('selected');

					Element.remove(item.up("tr"));
					
					if (item.checked) {
						clone.removeClassName('unread');
						var newRow = $('readMessages').down("table").insert(clone);
					} else {
						clone.addClassName('unread');
						var newRow = $('unreadMessages').down("table").insert(clone);
					}
				}
			});
	}
</script>
<? $this->pageTitle = 'Message Queue'?>
<div class="messageQueues index">
<div style="float: left; clear: none; width: 50%" id='messageQueueIndex'>
	<div id="unreadMessages">
		<?=$this->renderElement('../message_queues/list', array('messages' => $unreadMessages))?>
	</div>
	<div id="readMessages" class="collapsible" style="clear: both">
		<h3 class="handle">Read Messages</h3>
		<div class="collapsibleContent">
			<?=$this->renderElement('../message_queues/list', array('messages' => $readMessages, 'read' => true))?>
		</div>
	</div>
</div>
<div style="float: left; clear: none; padding-left: 20px; width: 45%" id='rightPane'>
</div>

</div>