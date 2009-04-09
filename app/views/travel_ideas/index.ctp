<div class="travelIdeas index">

<h2><?php __('Travel Ideas for ' . $landingPage['LandingPage']['landingPageName']);?></h2>
	<div style="font-weight:bold;margin-bottom:15px;">
		<?php
			echo $html->link('New Travel Idea',
				'/travel_ideas/add/' . $landingPage['LandingPage']['landingPageId'] ,
				array(
					'title' => 'Add Travel Idea for ' . $landingPage['LandingPage']['landingPageName'],
					'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
					'complete' => 'closeModalbox()'
					),
				null,
				false
			);
		?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<?php
			echo $html->link('Back to Landing Pages','/landing_pages');
		?>
	</div>
<?php
$i = 0;
foreach ($travelIdeas as $travelIdea):
?>
<div style="margin-bottom:20px;background-color:#f5f2e2;padding:10px;">
	<h3 style="font-size:14px;margin-top:0px;margin-bottom:5px;padding-top:0px;">
		<?php echo $travelIdea['TravelIdea']['travelIdeaHeader'];?> &nbsp;&nbsp;
		<?php
			echo $html->link('Edit',
				'/travel_ideas/edit/' . $travelIdea['TravelIdea']['travelIdeaId'],
				array(
					'title' => 'Edit Travel Idea',
					'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
					'complete' => 'closeModalbox()'
					),
				null,
				false
			);
		?>
	</h3>
	<div style="font-size:13px;"><span style="font-weight:bold;">Blurb: </span><?php echo $travelIdea['TravelIdea']['travelIdeaBlurb'];?></div>
	<div style="font-size:13px;"><span style="font-weight:bold;">Trip Advisor Award: </span><?php echo $travelIdea['TravelIdea']['tripAdvisorAward'];?></div>
	<div style="font-size:13px;"><span style="font-weight:bold;">Link Text: </span><?php echo $travelIdea['TravelIdea']['travelIdeaLinkText'];?></div>
	<div style="font-size:13px;margin-bottom:3px;"><span style="font-weight:bold;">Url: </span><?php echo $travelIdea['TravelIdea']['travelIdeaUrl'];?></div>
	<?php echo $html->link(__('Delete Travel Idea', true), array('action'=>'delete', 'id'=>$travelIdea['TravelIdea']['travelIdeaId'] . '/' . $landingPage['LandingPage']['landingPageId']), null, sprintf(__('Are you sure you want to delete %s?', true), $travelIdea['TravelIdea']['travelIdeaHeader'])); ?>

	<?php if (isset($travelIdea['TravelItems'])) :?>
	<?php foreach ($travelIdea['TravelItems'] as $travelItemType => $travelItem) :?>
	<div style="width:200px;float:left;background-color:#fff;border:1px solid #eaeaea; margin-top:10px; margin-right:20px;padding:10px;">
		<div style="color:#990000;font-weight:bold;margin-bottom:5px;"><?php echo $travelItemType;?></div>	
		<?php foreach ($travelItem as $tItem) :?>
			<div style="margin-bottom:2px;">
				&raquo; 

				<?php
					echo $html->link($tItem['travelIdeaItemName'],
						'/travel_idea_items/edit/'. $tItem['travelIdeaItemId'] . '/' . $landingPage['LandingPage']['landingPageId'],
						array(
							'title' => 'Edit Item',
							'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
							'complete' => 'closeModalbox()'
							),
						null,
						false
					);
				?>

				<?php echo $html->link(__('Delete', true), array('controller' => 'travel_idea_items', 'action'=>'delete', 'id'=> $tItem['travelIdeaItemId'] . '/' . $landingPage['LandingPage']['landingPageId']), null, sprintf(__('Are you sure you want to delete item: %s?', true), $tItem['travelIdeaItemName'])); ?>
			</div>
		<?php endforeach;?>
	</div>	
	<?php endforeach;?>
	<?php endif;?>
	<div style="clear:both;"></div>

	<div style="margin-top:10px;font-weight:bold;font-size:13px;">
		<?php
			echo $html->link('New Item',
				'/travel_idea_items/add/' . $travelIdea['TravelIdea']['travelIdeaId'] . '/' . $landingPage['LandingPage']['landingPageId'],
				array(
					'title' => 'Add Item for ' . $travelIdea['TravelIdea']['travelIdeaHeader'],
					'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
					'complete' => 'closeModalbox()'
					),
				null,
				false
			);
		?>
	</div>
</div>
<?php endforeach; ?>
</div>
