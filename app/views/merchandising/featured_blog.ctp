<?php echo $form->create(false,array('action'=>'featured_blog'));?>
<?php echo $form->input('fBlogId', array('label'=>'Featured Blog Id: ','value' => $fBlogId));?>
<?php echo $form->submit('Save');?>
<?php echo $form->end();?>
