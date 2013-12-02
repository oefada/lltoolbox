<h2><?=$client['Client']['name'];?></h2>
<p>Sites:  <?=ucwords(strtolower(implode(', ', $client['Client']['sites'])));?><br /><br /></p>
<p><strong>Are you sure you want to delete all future scheduled offers for this client?</strong><br /><br /></p>


<div class="submit">
	<?php echo $ajax->form('close_offers', 'post', array('url' => "/scheduling/close_offers/clientId:{$client['Client']['clientId']}", 'update' => 'MB_content', 'model' => 'Scheduling', 'complete' => 'closeModalbox()'));?>
	<input type="hidden" name="data[clientId]" value="<?=$client['Client']['clientId'];?>" />
	<input type="hidden" name="data[closeIt]" value="1" />
	<input value="Yes - close all offers" type="submit" /> 
	</form>
	<input value="Nope!" type="submit" style="margin-top:60px;" onclick="Modalbox.hide();return false;" />
</div>
<?php if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";?>
