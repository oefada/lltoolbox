<div class="articles form">
<div class="collapsible">
	<div class="handle"><?php __('Destination Attribution');?></div>
	<div class="collapsibleContent related">
		<?php foreach ($this->data['ArticleRel'] as $articleRel) : ?>
		<div><?php echo $articleRel['refName'];?></div>
		<?php endforeach;?>
		<?php
		echo $html->link('Add Destination Attribution',
			'/article_rels/add/1/' . $this->data['Article']['articleId'],
			array(
				'title' => 'Add Destination Attribution',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	</div>
</div>

</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Articles', true), array('action'=>'index')); ?></li>
	</ul>
</div>
