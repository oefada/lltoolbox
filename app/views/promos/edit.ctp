
<style>
.hr-promo {
    height:1px; 
    background: #ccc; 
    width: 90%; 
    font-size: 1px; 
    overflow: hidden; 
    margin-bottom:10px; 
    margin-top:10px; 
    border: none;
}
.localLabel, .localLabelNoLeftMargin {
    color: #898989;
    font-weight: bold;
    vertical-align: top;
    margin-right: 10px;
}
.localLabel {
    margin-left: 20px;
}

.destinationGroupParentColumn {
    float: left;
    width: 250px;
    clear: none;
    margin: 0 0 0 40px;
}

.destinationGroupParent {
    clear: none;
    margin: 0 5px 0 0;
    padding: 0px;
}
.destinationGroupChildren {
    clear: none;
    margin: 0 0 0 25px;
}
.destinationChild {
    clear: none;
    margin: 0 0 0 0;
}
.listCheckbox {
    float: left;
    clear: none;
    width: 200px;
    margin: 0 0 0 0;
}
.restrictionContainer {
    padding: 0px; 
    margin: 0 0 0 40px;
}
.listClient {
    padding: 0px; 
    margin: 0px 0 8px 0px;
}
</style>

<script type="text/javascript">
    
    // client info
    restrictedClients = new Array();
    <? foreach ($displayRestrictedClients as $clientId=>$clientName) { ?>
    clientListAdd(<?= $clientId; ?>, "<?= str_replace('"', '\"', $clientName); ?>");
    <? } ?>

    function toggleContainer(img) {
        var div = img.id.split('-')[1];
        if ($(div).visible()) {
            $(div).hide();
            $(img).writeAttribute('src', '/img/icon_click_to_open.png');
        } else {
            $(div).show();
            $(img).writeAttribute('src', '/img/icon_click_to_close.png');
        }
    }

    function selectChildDestinations(chk, id) {
        if (chk.checked) {
            var prefix = 'childDestination-' + id + '-';     	
            $$('[id^=' + prefix + ']').each(function(item) {
            	var childId = item.id.split('-')[2];
                var child = 'PromoRestrictDestination' + childId;
                $(child).checked = true;
                selectChildSubDestinations($(child), childId);
            });
        }
    }

    function selectChildSubDestinations(chk, id) {
        if (chk.checked) {
            var prefix = 'childDestinationSub-' + id + '-';     	
            $$('[id^=' + prefix + ']').each(function(item) {
                var child = 'PromoRestrictDestination' + item.id.split('-')[2];
                $(child).checked = true;
            });
        }
    }

    function clientListDisplay() {
	var ids = '';
	var display = '';
	for (var i=0, len=restrictedClients.length; i<len; ++i) {
	    var clientInfo = restrictedClients[i].split('|');
	    display = display + '<div class="listClient">' + clientInfo[1] + ' (' + clientInfo[0] +  ')&nbsp;&nbsp;';
            display = display + '<a href="javascript:void(0);" onclick="removeClientRestriction(' + i + ');">Remove</a></div>';
            if (ids != '') {
                ids = ids + ',';
            }
            ids = ids + clientInfo[0];
        }
        $('clientListContainer').update(display);
        $('PromoListRestrictedClients').update(ids);
        $('PromoListRestrictedClients').value = ids;
    }

    function clientListRemove(index) {
        restrictedClients.splice(index, 1);
    }
    
    function clientListAdd(id, name) {
        var str = id + '|' + name;
        restrictedClients.push(str);
    }
    
    function removeClientRestriction(index) {
        // alert(index);
        clientListRemove(index);
        clientListDisplay();
    }

    function addClientRestriction() {
        var client = $('PromoAddClient').value;
        if (!isNaN(client) && client > 0) {
            clientListAdd(client, '** name lookup pending **');
        }
        clientListDisplay();
        $('PromoAddClient').value = '';
    } 
    
    function addClientRestrictionList() {
        var clients = $('addClientByList').value.split("\n");
        for (var i=0, len=clients.length; i<len; ++i) {
            if (!isNaN(clients[i]) && clients[i] > 0) {
                clientListAdd(clients[i], '** name lookup pending **');
            }
        }
        clientListDisplay();
        $('addClientByList').value = '';
    }   
    
    Event.observe(window, 'load', function() {
        clientListDisplay();
    });

</script>

<? $pageTitle = ($isNewPromo) ? 'Add New Promo' : 'Edit Promo'; ?>
<h2><?php __($pageTitle);?></h2>
<? 
if (isset($formErrors)) {
    echo '<div style="color: #990000; line-height:18px;">';
    foreach ($formErrors as $e) {
    	echo $e . '<br>';
    }
    echo '</div>';
}
?>

<div class="promos form">
<?php echo $form->create('Promo', array('action'=>'edit'));?>
	<fieldset>
		<?= $form->input('promoId', array('type'=>'hidden')); ?>
		<?= $form->input('promoName'); ?>
		<?= $form->input('siteId', array('options'=> array('0'=>'All', '1'=>'LuxuryLink', '2'=>'Family'))); ?>
		<?= $form->input('promoCategoryTypeId', array('label'=>'Category', 'options'=> $promoCategoryTypeIds, 'empty'=> '-- Select Category')); ?>
                
                <? if ($isNewPromo) { ?>
			<hr class="hr-promo"/>
			<?= $form->input('promoCode', array('style'=>'width:200px')); ?>

			<div style="padding:0; margin:0 0 10px 100px; font-weight: bold; color: #990000;">-- OR --</div>

			<div class="input text">
				<label for="GeneratePromoCodes">Generate Codes</label>
				<span class="localLabelNoLeftMargin">Prefix</span><?= $form->input('generatePrefix', array('div'=>false, 'label'=>false, 'style'=>'width:160px')); ?>
				<span class="localLabel">Quantity</span><?= $form->input('generateQuantity', array('div'=>false, 'label'=>false, 'style'=>'width:100px')); ?>
			</div>
		<? } ?>
		<hr class="hr-promo"/>
		<div class="input text">
			<label for="Amount">Amount</label>
			<?= $form->input('amount', array('div'=>false, 'label'=>false, 'style'=>'width:100px')); ?>
			<?= $form->input('percentOrDollar', array('div'=>false, 'label'=>false, 'options'=> array(''=>'--', 'D'=>'$', 'P'=>'%'))); ?>
			<span class="localLabel">Minimum Purchase Amount</span><?= $form->input('minPurchaseAmount', array('div'=>false, 'label'=>false, 'style'=>'width:100px')); ?>
		</div>
		<div>
			<label for="StartDate">Start Date</label>
			<?= $datePicker->picker('startDate', array('label'=>false, 'div'=>false)); ?>
			<span class="localLabel">End Date</span><?= $datePicker->picker('endDate', array('label'=>false, 'div'=>false)); ?>
		</div>

		<hr class="hr-promo">
		<div>
			<label for="UsageRestrictions">Usage Restrictions</label>
			
			<?= $form->input('oneUsagePerCode', array('type'=>'checkbox', 'label'=>false, 'div'=>false)); ?>
			<span class="localLabelNoLeftMargin">One Usage Per Code</span>

			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?= $form->input('oneUsagePerUser', array('type' => 'checkbox', 'label'=>false, 'div'=>false)); ?>
			<span class="localLabelNoLeftMargin">One Usage Per User</span>

			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?= $form->input('newBuyersOnly', array('type' => 'checkbox', 'label'=>false, 'div'=>false)); ?>
			<span class="localLabelNoLeftMargin">New Buyers Only</span>
		</div>
				
		
		<hr class="hr-promo"/>
		<?= displayContainerHeader('destination', 'Destination Restrictions', $this->data['Promo']['restrictDestination']); ?>
	<?php	$parentCount = 0;
	        $parentColumnOpen = false;
	        $parentBreakArray = array(0, 5, 6, 8);
		foreach ($destinations as $parent) {
		    if (intval($parent['Destination']['parentId']) == 0) { 
		       if (in_array($parentCount, $parentBreakArray)) { 
		           echo '<div class="destinationGroupParentColumn">';
		           $parentColumnOpen = true;
		       } 
		       echo '<div class="destinationGroupParent">';
		       echo $form->input('Promo.restrictDestination.'.$parent['Destination']['destinationId'], 
		                             array('type'=>'checkbox' 
		                                   , 'label'=>false
		                                   , 'value'=>$parent['Destination']['destinationId']
		                                   , 'onClick' => 'selectChildDestinations(this, ' . $parent['Destination']['destinationId'] . ')'
		                                   , 'div' => false
		                             )); 
		       echo ' <span style="font-weight: bold;">' . $parent['Destination']['destinationName'] . '</span>';
		       echo '<div class="destinationGroupChildren">';
		                             
		       foreach ($destinations as $child) {
		            if ($child['Destination']['parentId'] == $parent['Destination']['destinationId']) { 
				echo '<div class="destinationChild">';
				echo '<input type="hidden" id="childDestination-' . $parent['Destination']['destinationId'] . '-' . $child['Destination']['destinationId'] . '" value="">';
				echo $form->input('Promo.restrictDestination.'.$child['Destination']['destinationId'], 
						     array('type'=>'checkbox' 
							   , 'label'=>false
							   , 'value'=>$child['Destination']['destinationId']
							   , 'onClick' => 'selectChildSubDestinations(this, ' . $child['Destination']['destinationId'] . ')'
							   , 'div' => false
						     )); 
				echo ' ' . $child['Destination']['destinationName'];
				echo '</div>';
				
				foreach ($destinations as $childSub) {
				    if ($childSub['Destination']['parentId'] == $child['Destination']['destinationId']) { 
					echo '<div class="destinationChild">';
					echo '<input type="hidden" id="childDestinationSub-' . $child['Destination']['destinationId'] . '-' . $childSub['Destination']['destinationId'] . '" value="">';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $form->input('Promo.restrictDestination.'.$childSub['Destination']['destinationId'], 
							     array('type'=>'checkbox' 
								   , 'label'=>false
								   , 'value'=>$childSub['Destination']['destinationId']
								   , 'div' => false
							     )); 
					echo ' ' . $childSub['Destination']['destinationName'];
					echo '</div>';
				    }
				}
				
		            }
		        }
		        echo '</div></div>';
		        $parentCount++;
		        if (in_array($parentCount, $parentBreakArray)) { 
		            echo '</div>'; 
		            $parentColumnOpen = false;
		        }
		    } 
		}
		if ($parentColumnOpen) { echo '</div>'; }
		?>
		</div>
		
		
		<hr class="hr-promo"/>
		<?= displayContainerHeader('interest', 'Interest Restrictions', $this->data['Promo']['restrictTheme']); ?>
		    <div class="restrictionContainer">
		    <? foreach ($themes as $theme) {
			   echo '<div class="listCheckbox">';
			   echo $form->input('Promo.restrictTheme.'.$theme['Theme']['themeId'], 
					     array('type'=>'checkbox' 
						   , 'label'=>false
						   , 'value'=>$theme['Theme']['themeId']
						   , 'div' => false
					     ));
			   echo ' ' . $theme['Theme']['themeName']; 
			   echo '</div>';
		       } ?>
		     </div>
		</div>
		
		
		<div style="padding:0px; margin:0px;"></div>
		<hr class="hr-promo"/>
		<?= displayContainerHeader('propertyType', 'Property Type Restrictions', $this->data['Promo']['restrictClientType']); ?>
		    <div class="restrictionContainer">
		    <? foreach ($clientTypes as $clientType) {
			   echo '<div class="listCheckbox">';
			   echo $form->input('Promo.restrictClientType.'.$clientType['ClientType']['clientTypeId'], 
					     array('type'=>'checkbox' 
						   , 'label'=>false
						   , 'value'=>$clientType['ClientType']['clientTypeId']
						   , 'div' => false
					     ));
			   echo ' ' . $clientType['ClientType']['clientTypeName']; 
			   echo '</div>';
		       } ?>
		     </div>
		</div>
		
		
		<div style="padding:0px; margin:0px;"></div>
		<hr class="hr-promo"/>
		<?= $form->input('listRestrictedClients', array('type'=>'hidden')); ?>
		<?= displayContainerHeader('client', 'Client Restrictions', $this->data['Promo']['restrictClient']); ?>

			     <?= $this->renderElement("input_search",array('name'=>'addClient', 'controller'=>'selectclients', 'label'=>'Client Name /ID', 'style'=>'width:200px', 'callingId'=>'promoEdit')); ?>
			    <div style="float:left; clear: none;"><a href="javascript:void(0);" onClick="addClientRestriction();">ADD</a></div>
			    <div style="float:left; clear: none; font-weight: bold; color: #990000; margin: 0 20px 0 20px;">-- OR --</div>
			    <div style="float:left; clear: none;"><span class="localLabelNoLeftMargin">List IDs</span><br/><span style="font-size:10px;">(one per line)</span></div>
			    <div style="float:left; clear: none;">
				    <textarea id="addClientByList" name="addClientByList" style="width:100px; height:50px;"></textarea>
				    &nbsp;&nbsp;<a href="javascript:void(0);" onClick="addClientRestrictionList();">ADD</a>
			    </div>
			    <div style="padding-top:0px;" id="clientListContainer"></div>
		</div>
	</fieldset>
<?= $form->end('Submit'); ?>
</div>

<? function displayContainerHeader($prefix, $label, $arr) {
       $itemCount = (is_array($arr) && sizeof($arr) > 0) ? sizeof($arr) : 0;
       $str = '<img align="top" id="toggle-' . $prefix . 'Container" src="/img/icon_click_to_open.png"';       
       $str .= ' onClick="toggleContainer(this);">&nbsp;<span class="localLabel">' . $label . ' (' . $itemCount . ')</span>';
       $str .= ' <div id="' . $prefix . 'Container" style="display: none;">';
       return $str;
   } 
?>