<?php if(!isset($newPassword)): ?>
<div class='icon-yellow' style="padding-left: 40px; text-align: left">
Are you sure you want to reset this user's password? An email confirmation will be sent to them with their new password, and you will be shown the generated password momentarily.
</div>
<div style="text-align: center">
<?php echo $ajax->form('resetPassword', 'post', array('update' => 'MB_content', 'model' => 'User', 'complete' => 'closeModalbox()'));?>
	<input type="hidden" name="data[reset]" value="true" />
	<input type="button" name="nevermind" value="Nevermind..." onclick="Modalbox.hide()" />
	<input type="submit" name="Continue" value="Yes, continue" />
</form>
</div>
<?php else: ?>
	<div class='icon-yellow' style="padding-left: 40px; text-align: left">
	The user's password has been reset to '<strong><?=$newPassword?></strong>'. An email with the new password will be sent to their default email address.
	</div>
<?php endif;?>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>