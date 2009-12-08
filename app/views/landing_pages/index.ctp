<div class="landingPages index">
<h2><?php __('LandingPages');?></h2>
<div class="sitesDropdown">
    <strong>Filter packages by site</strong>
    <select name="site" id="selectSite" />
        <option value="all" selected>All</option>
        <option value="LL">Luxury Link</option>
        <option value="Family">Family</option>
    </select>
</div>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('landingPageId');?></th>
	<th><?php echo $paginator->sort('landingPageName');?></th>
    <th><?php echo $paginator->sort('Site', 'siteId');?></th>
	<th><?php echo $paginator->sort('landingPageTypeId');?></th>
	<th><?php echo $paginator->sort('referenceId');?></th>
	<th><?php echo $paginator->sort('isSponsored');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th>Travel Ideas</th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($landingPages as $landingPage):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow '.$landingPage['LandingPage']['siteId'].'"';
	}
    else {
        $class = ' class="'.$landingPage['LandingPage']['siteId'].'"';
    }
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $landingPage['LandingPage']['landingPageId']; ?>
		</td>
		<td>
			<?php echo $html->link(__($landingPage['LandingPage']['landingPageName'], true), array('action'=>'edit', $landingPage['LandingPage']['landingPageId'])); ?>
		</td>
        <td>
            <?php echo ucwords($siteIds[$landingPage['LandingPage']['siteId']]); ?>
        </td>
		<td>
			<?php echo $landingPage['LandingPageType']['landingPageTypeName']; ?>
		</td>
		<td>
			<?php echo $landingPage['LandingPage']['referenceId']; ?>
		</td>
		<td>
			<?php echo $landingPage['LandingPage']['isSponsored']; ?>
		</td>
		<td>
			<?php echo $landingPage['LandingPage']['inactive']; ?>
		</td>
		<td>
			<?php 
			if ($landingPage['LandingPage']['landingPageTypeId'] == 1) {
				echo $html->link(__('Edit Travel Ideas', true), array('controller' => 'travel_ideas', 'action'=>'index', $landingPage['LandingPage']['landingPageId'])); 
			}
			?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $landingPage['LandingPage']['landingPageId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>

<script type="text/javascript">
    Event.observe(window, 'load', function() {
        $('selectSite').observe('change', function() {
                switch (this.value) {
                    case 'LL':
                        $$('tr.2').each(function(divItem) {
                                    $(divItem).hide();
                                });
                        $$('tr.1').each(function(divItem) {
                                    $(divItem).show();
                                });
                        break;
                    case 'Family':
                        $$('tr.1').each(function(divItem){
                                    $(divItem).hide();
                                });
                        $$('tr.2').each(function(divItem) {
                                    $(divItem).show();
                                });
                        break;
                    case 'all':
                    default:
                        ['1','2'].each(function(item) {
                            var elem = "tr."+item;
                            $$(elem).each(function(divItem) {
                                    $(divItem).show();
                            });
                        });
                }
        });
    });
</script>