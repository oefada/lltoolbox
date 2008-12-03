<? echo $form->hidden('MenuItem.externalLink'); ?>
<?php
echo $form->input('MenuItem.linkTo',
				array(
					'options' => $styles,
					'label' => 'Which Style? (or '.$ajax->link('enter custom link', 
															array('action' => 'url_input_form'),
															array('update' => 'link_to', 'complete' => 'closeModalbox()')
													).')'
					)
				);
?>