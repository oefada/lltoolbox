<?php echo $form->hidden('MenuItem.externalLink'); ?>
<?php
echo $form->input('MenuItem.linkTo',
				array('label' => 'Which URL? (or '.$ajax->link('choose style', 
															array('action' => 'landing_pages_select_form'),
															array('update' => 'link_to', 'complete' => 'closeModalbox()')
													).')',
					'rows' => 2
					)
				);
?>