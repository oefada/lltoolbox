<?php

// ADD
/*************************************************************************************************/

if (!isset($masterState) || $masterState != 1) {
    
    echo $form->input('packageId', array('value' => $packageId, 'type' => 'hidden'));

    ?>

    <link href="/css/scheduling-master.css" type="text/css" rel="stylesheet" />
    
    <div id="schedulingmaster-add"> 
    <?php if ($isHotelOffer): ?>
      <input type="hidden" name="data[SchedulingMaster][pricePointId]" value="<?php echo $pricePointId; ?>" />
    <?php else: ?>
		<!-- PRICE POINT -->
		<h2>Choose a Price Point</h2>
		<table id="data-table" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<th></th>
				<th>Name</th>
				<th>Validity</th>
				<th width="50">Max Num Sales</th>
				<th>Retail</th>
				<th>% of <br />Retail<br />(Auction)</th>
				<th>% of <br />Retail<br />(Buy Now)</th>
				<?php if ($package['Package']['isFlexPackage']): ?>
					<th>Retail Price/<br />Extra Night</th>
                    <!-- <th>Auction/<br />Extra Night</th> -->
                    <th>Buy Now/<br />Extra Night</th>
				<?php endif; ?>
			</tr>
		<?php

    foreach ($pricePoints as $key => $pricePoint){
			$data_ppid=0;
			if (isset($data['SchedulingMaster']['pricePointId'])){
				$data_ppid=$data['SchedulingMaster']['pricePointId'];
			}
      $checked = ($data_ppid== $pricePoint['PricePoint']['pricePointId']) ? 'checked' : ''; ?>
      <tr>
      <td>
      <input type="checkbox" class="price-point-options" id="price-point-<?php echo $pricePoint['PricePoint']['pricePointId']; ?>" name="data[SchedulingMaster][pricePointId][<?=$key?>]" value="<?php echo $pricePoint['PricePoint']['pricePointId']; ?>" <?php echo $checked; ?>/>
      </td>
      <td><label for="price-point-<?php echo $pricePoint['PricePoint']['pricePointId']; ?>"><?php echo $pricePoint['PricePoint']['name']; ?></label></td>
      <td style='font-size:11px;'><?php echo $pricePoint[0]['dateRanges']; ?></td>
			<td><?php echo (int)$pricePoint['PricePoint']['maxNumSales']; ?></td>
			<td><?php 
			echo $pricePoint['PricePoint']['retailValue']; ?></td>
				
			<td>
				<?php echo $pricePoint['PricePoint']['percentRetailAuc']; ?>
				<input type='hidden' name='auction_arr[<?=$pricePoint['PricePoint']['pricePointId'];?>]' value='<?=$pricePoint['PricePoint']['percentRetailAuc'];?>'>
			</td>
			<td>
				<?php echo $pricePoint['PricePoint']['percentRetailBuyNow']; ?>
				<input type='hidden' name='buynow_arr[<?=$pricePoint['PricePoint']['pricePointId'];?>]' value='<?=$pricePoint['PricePoint']['percentRetailBuyNow'];?>'>
			</td>
			<?php if ($package['Package']['isFlexPackage']): ?>
				<td><?php echo $pricePoint['PricePoint']['flexRetailPricePerNight']; ?></td>
                <!--
                <td><?php echo $pricePoint['PricePoint']['pricePerExtraNightDNG']; ?>
                <span>(<?php echo (round($pricePoint['PricePoint']['pricePerExtraNightDNG'] / $pricePoint['PricePoint']['flexRetailPricePerNight'], 2) * 100); ?>%)</span>
                </td>
                -->
				<td><?php echo $pricePoint['PricePoint']['pricePerExtraNight']; ?>
				<span>(<?php echo (round($pricePoint['PricePoint']['pricePerExtraNight'] / $pricePoint['PricePoint']['flexRetailPricePerNight'], 2) * 100); ?>%)</span>
				</td>
			<?php endif; ?>
			</tr>
      <?php } ?>
    </table>
  <?php endif; ?>
  
  <!-- DISCOUNTABLE -->
  <br/><input type="checkbox" id="scheduling-master-discountable" name="data[SchedulingMaster][isDiscountedOffer]" value="1" <? if ($data['SchedulingMaster']['isDiscountedOffer'] == 1) { echo 'checked'; } ?> />&nbsp;&nbsp;Apply Discount to Buy Now
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  Discount % <input type="text" id="scheduling-master-percent-discount" name="data[SchedulingMaster][percentDiscount]" value="<?= $data['SchedulingMaster']['percentDiscount'];?>" style="width:20px;" maxlength="2" />
  
  
        <!-- TRACK SELECTION (MULTICLIENT PACKAGES ONLY) -->
        <?php if (!$singleClientPackage): ?>
            <h2>Choose a Track for Each Client</h2>
            <table id="data-table" cellpadding="0" cellspacing="0" border="0">
                <?php foreach($package['ClientLoaPackageRel'] as $packageClient): ?>
                    <tr>
                        <td><?php echo $packageClient['clientName']; ?></td>
                        <td><?php echo $form->input('Track', array('options' => $packageClient['trackIds'], 'empty' => true, 'multiple' => false, 'disabled' => ($masterState) ? true : false)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        <!-- SCHEDULING -->
        <h2>Scheduling</h2>
        <table id="data-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="5">LOA valid <?php echo date('M d, Y', strtotime($loa['Loa']['startDate'])); ?> to <?php echo date('M d, Y', strtotime($loa['Loa']['endDate'])); ?> Status: 
								<?php echo $loa['LoaLevel']['loaLevelName']; ?>	
								<br /><br /></td>
            </tr>
            <tr>
                <?php if ($singleClientPackage): ?><th>Track</th><?php endif; ?>
                <th>Schedule As</th>
                <th>Start Date</th>
                <th>Start Time</th>
                <th>End Date</th>
            </tr>
            <tr>
                <?php if ($singleClientPackage): ?><td><?php echo $form->input('Track', array('options' => $trackIds, 'empty' => true, 'multiple' => false, 'disabled' => ($masterState) ? true : false)); ?></td><?php endif;  ?>
                <td>
                    <?php if ($package['Format'][0]['formatId'] != 3) { // NOT HOTEL OFFER
                        $isAuctionChecked = ($data['isAuction']) ? 'checked' : '';
                        $isBuyNowChecked = ($data['isBuyNow']) ? 'checked' : '';
                        $isOptimizedChecked = ($data['isOptimized']) ? 'checked' : '';
                    ?>                        
                    <input value='<?=$pricePoint['PricePoint']['pricePointId'];?>' id="isAuction" type="checkbox" name="data[isAuction]" <?php echo $isAuctionChecked; ?>/> <label for="isAuction">Auction</label><br /><br />
                    <input value='<?=$pricePoint['PricePoint']['pricePointId'];?>' id="isBuyNow" type="checkbox" name="data[isBuyNow]" <?php echo $isBuyNowChecked; ?>/> <label for="isBuyNow">Buy Now</label><br /><br />
                    
                    <!--
                    <div style="background-color: #ffffaa;">
                        <input value='<?=$pricePoint['PricePoint']['pricePointId'];?>' id="isOptimized" type="checkbox" name="data[isOptimized]" <?php echo $isOptimizedChecked; ?>/> <label for="isOptimized">NEW!!!</label>
                    </div>
                    -->
                    <?php
                        } else {
                            echo 'Hotel Offer';
                            echo '<input type="hidden" name="data[isHotelOffer]" value="1"/>';
                            echo $form->input('offerTypeId', array('value' => 7, 'type' => 'hidden'));
                            echo $form->input('numDaysToRun', array('value' => 1, 'type' => 'hidden')); 
                        } 
                    ?>
                </td>
                <td><?php echo $form->input('startDatePicker', array('class' => 'format-m-d-y divider-dash highlight-days-06 no-transparency range-low-today fill-grid-no-select', 'label' => ' ', 'readonly' => 'readonly', 'style' => 'width:100px;')); ?></td>
                <td>
                    <?php
                        echo $form->input('startDateTime', array('label' => ' ', 'disabled' => ($masterState) ? 'disabled' : false,
                			'options' => array(
                				'00:00:00' => '12 AM', '01:00:00' => '1 AM', '02:00:00' => '2 AM', '03:00:00' => '3 AM', '04:00:00' => '4 AM',
                				'05:00:00' => '5 AM', '06:00:00' => '6 AM', '07:00:00' => '7 AM', '08:00:00' => '8 AM', '09:00:00' => '9 AM',
                				'10:00:00' => '10 AM', '11:00:00' => '11 AM', '12:00:00' => '12 PM', '13:00:00' => '1 PM', '14:00:00' => '2 PM',
                				'15:00:00' => '3 PM', '16:00:00' => '4 PM', '17:00:00' => '5 PM', '18:00:00' => '6 PM', '19:00:00' => '7 PM',
                				'20:00:00' => '8 PM', '21:00:00' => '9 PM', '22:00:00' => '10 PM', '23:00:00' => '11 PM'
                			)
                		));
                    ?>
                </td>
                <td><?php echo $form->input('endDatePicker2', array('class' => 'format-m-d-y divider-dash highlight-days-06 no-transparency range-low-today fill-grid-no-select', 'label' => ' ', 'readonly' => 'readonly', 'style' => 'width:100px;')); ?></td>
            </tr>
        </table>
        
        <?php if ($package['Format'][0]['formatId'] != 3) { // NOT HOTEL OFFER ?>

            <!-- AUCTION OPTIONS -->
            <div id="auction-options" style="padding:0px; margin:0px; <?php if (!$data['isAuction']) echo 'display:none;'; ?>">    
                <h2>Auction Options</h2>
                <table id="data-table" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>Auction Length</th>
                        <th>Scheduling Delay</th>
                        <th>Number of Iterations</th>
                    </tr>
                    <tr>
                        <td><?php echo $form->input('numDaysToRun', array('label' => ' ', 'type' => 'select', 'options' => array(1 => '1 Day', 2 => '2 Days', 3 => '3 Days', 5 => '5 Days', 7 => '7 Days'), 'disabled' => ($masterState) ? 'disabled' : false)); ?></td>
                        <td><?php echo $form->input('schedulingDelayCtrlId', array('label' => ' ')); ?></td>
                        <td>
                            <input type='radio' id='by-end-date' name='data[SchedulingMaster][iterationSchedulingOption]' value='1' checked="checked" /> <label for='by-end-date'>Determined By End Date</label><br />
                            <input type='radio' id='by-iteration' name='data[SchedulingMaster][iterationSchedulingOption]' value='0' /> <label for='by-iteration'>Or Enter Number of Iterations:</label> <input name="data[SchedulingMaster][iterations]" value="<?php echo ($data[SchedulingMaster][iterations]) ? $data[SchedulingMaster][iterations] : 1; ?>" size="2"/>
                        </td>
                    </tr>
                </table>
                
                <input type="checkbox" id="isMystery" name="data[MerchandisingFlag][MerchandisingFlag]" value="3"/> <b>Mystery Auction</b>
                
            </div>
            
            <!-- BUY NOW OPTIONS -->
            <div id="buynow-options" style="<?php if (!$data['isBuyNow']) echo 'display:none;'; ?>">
                <h2>Buy Now Options</h2>
                <input type='radio' id='bonus-miles' name='data[buyNowOfferTypeId]' value='4' checked="checked" /> <label for='bonus-miles'>Includes Bonus Miles (type 4)</label><br/>
                <input type='radio' id='suppress-retail' name='data[buyNowOfferTypeId]' value='3' /> <label for='suppress-retail'>Suppress Retail Value (type 3)</label>
                <?php if ($package['Package']['isFlexPackage']): ?>
                    <h3>Flex Package Info (Buy Now)</h3>
                    <table class="flex-info">
                        <tr>
                            <th>Range of Nights:</th>
                            <td><?php echo $package['Package']['flexNumNightsMin']; ?> - <?php echo $package['Package']['flexNumNightsMax']; ?> Nights</td>
                        </tr>
                        <tr>
                            <th>Notes:</th>
                            <td><?php echo htmlentities($package['Package']['flexNotes']); ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>

            <!-- OPTIMIZED OPTIONS -->
            <div id="optimized-options" style="<?php if (!$data['isOptimized']) echo 'display:none;'; ?>">
                <h2>NEW!!! Options</h2>              
                <?php if ($package['Package']['isFlexPackage']): ?>
                    <h3>Flex Package Info (NEW!!!)</h3>
                    <table class="flex-info">
                        <tr>
                            <th>Range of Nights:</th>
                            <td><?php echo $package['Package']['flexNumNightsMin']; ?> - <?php echo $package['Package']['flexNumNightsMax']; ?> Nights</td>
                        </tr>
                        <tr>
                            <th>Notes:</th>
                            <td><?php echo htmlentities($package['Package']['flexNotes']); ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>
        
        <?php } ?>
        
        <!-- MYSTERY AUCTIONS -->
        <div id="mystery-options" style="padding:0px; margin:0px; <?php if (!$data['isAuction']) echo 'display:none;'; ?>">
            <br />
            <h2>Mystery Auctions</h2>
            <?php
                $openingBid 	= (!empty($this->data['SchedulingMaster']['openingBid'])) 	? $this->data['SchedulingMaster']['openingBid'] 	: 1;
                $bidIncrement 	= (!empty($this->data['SchedulingMaster']['bidIncrement'])) ? $this->data['SchedulingMaster']['bidIncrement'] 	: 1;
                $packageName 	= (!empty($this->data['SchedulingMaster']['packageName'])) 	? $this->data['SchedulingMaster']['packageName'] 	: '';
                $subtitle       = (!empty($this->data['SchedulingMaster']['subtitle'])) ? $this->data['SchedulingMaster']['subtitle'] 	: '';
                $shortBlurb 	= (!empty($this->data['SchedulingMaster']['shortBlurb'])) 	? $this->data['SchedulingMaster']['shortBlurb'] 	: '';
                echo $form->input('Mystery.openingBid', array('value' => $openingBid, 'size' => 2));
                echo $form->input('Mystery.bidIncrement', array('value' => $bidIncrement, 'size' => 2));
                echo $form->input('Mystery.packageName', array('value' => $packageName, 'size' => 2));
                echo $form->input('Mystery.subtitle', array('value' => $subtitle, 'size' => 2));
                echo $form->input('Mystery.shortBlurb', array('value' => $shortBlurb, 'rows' => 2));
                echo $form->input('additionalDescription', array('rows' => 2));
                echo $form->input('mysteryIncludes', array('rows' => 2));
            ?>
        </div>

        
        <!-- NOTES -->
        <br />
        <h2>Package Notes</h2>
		<input name="data[Package][packageId]" value="<?php echo $packageId?>" type="hidden"/>
        <textarea name="data[Package][notes]" rows="5"><?php echo $package[Package][notes]; ?></textarea>
        
        <br /><br /><br /><br /><br /><br />

    </div> <!-- END #schedulingmaster-add -->
    

    <!-- JS -->    
    <script>
        // startDate calendar
		delete datePickerController.datePickers['SchedulingMasterStartDatePicker'];
		datePickerController.addDatePicker('SchedulingMasterStartDatePicker',
			{
				'id':'SchedulingMasterStartDatePicker',
				'highlightDays':'0,0,0,0,0,1,1',
				'disableDays':'',
				'divider':'-',
				'format':'m-d-y',
				'locale':true,
				'splitDate':0,
				'noTransparency':true,
				'staticPos':false,
				'hideInput':false,
				'low':datePickerController.dateFormat((new Date().getMonth() + 1) + "/" + new Date().getDate() + "/" + new Date().getFullYear(), true)
			}
		);		
    </script>
    
    <script>
        // endDate calendar
		delete datePickerController.datePickers['SchedulingMasterEndDatePicker2'];
		datePickerController.addDatePicker('SchedulingMasterEndDatePicker2',
			{
				'id':'SchedulingMasterEndDatePicker2',
				'highlightDays':'0,0,0,0,0,1,1',
				'disableDays':'',
				'divider':'-',
				'format':'m-d-y',
				'locale':true,
				'splitDate':0,
				'noTransparency':true,
				'staticPos':false,
				'hideInput':false,
				'low':datePickerController.dateFormat((new Date().getMonth() + 1) + "/" + new Date().getDate() + "/" + new Date().getFullYear(), true)
			}
        );
    </script>
    
    <script>
        // change endDate according to price point selection
        pricePointDefaultEndDates = new Array();
        <?php
            foreach ($pricePointDefaultEndDates as $key => $row) {
                echo "pricePointDefaultEndDates[$key] = '" . $row['endDate'] . "';";
            }
        ?>
        function setEndDate(event) {
            var element = Event.element(event);
            $('SchedulingMasterEndDatePicker2').value = pricePointDefaultEndDates[$('price-point-' + $(element).getValue()).getValue()];
        }
        $$('.price-point-options').each(function(item,index){
            $(item).observe('change', setEndDate);
        });
    </script>

    <script>
        // toggle auction and buy now options
        function respondToClick(event) {
            var element = Event.element(event);
            if (element.identify() == 'isAuction') {
                if (element.checked) {
                    $('auction-options').appear();
                    $('optimized-options').fade();
                    $('isOptimized').setValue(false);
                } else {
                    $('auction-options').fade();
                }
            } else if (element.identify() == 'isBuyNow') {
                if (element.checked) {
                    $('buynow-options').appear();
                    $('optimized-options').fade();
                    $('isOptimized').setValue(false);
                } else {
                    $('buynow-options').fade();
                }
            } else if (element.identify() == 'isMystery') {
                if (element.checked) {
                    $('mystery-options').appear();
                    $('optimized-options').fade();
                    $('isOptimized').setValue(false);
                } else {
                    $('mystery-options').fade();
                }                       
            } else if (element.identify() == 'isOptimized') {

                if (element.checked) {
                    $('optimized-options').appear();
                    $('auction-options').fade();
                    $('isAuction').setValue(false);
                    $('buynow-options').fade();
                    $('isBuyNow').setValue(false);
                    $('mystery-options').fade();
                    $('isMystery').setValue(false);
                } else {
                    $('optimized-options').fade();
                }  
            }
        }   
        $('isAuction').observe('change', respondToClick);
        $('isBuyNow').observe('change', respondToClick);
        $('isMystery').observe('change', respondToClick);
        $('isOptimized').observe('change', respondToClick);
    </script>
    
<?php



// EDIT
/*************************************************************************************************/

} else { 

?>

	<fieldset>
		<div id='one'>
			<p class='clean-gray' style="margin-bottom:15px;">
				<? if(!empty($this->data['SchedulingMaster']['schedulingMasterId'])): ?>
				<strong>Package Id:</strong> <?php echo $this->data['SchedulingMaster']['packageId'] ?><br />
				<strong>Scheduling Master Id:</strong> <?php echo $this->data['SchedulingMaster']['schedulingMasterId'] ?><br />
				<? endif; ?>
			<?php echo $package['Package']['shortBlurb'] ?><br />
			<?php echo 'Package Validity: '.date('M j, Y', strtotime($this->data['SchedulingMaster']['validityStartDate'])).' to '.date('M j, Y', strtotime($this->data['SchedulingMaster']['validityEndDate'])) ?>
			</p>
	<?php
//print "<pre>";print_r($package);exit;
		if ($package['Format'][0]['formatId'] != 3) {
			echo "<div style='display:none;'>";
			echo $form->input('offerTypeId', array('value'=>$value,'label' => 'Offer Type', 'empty' => true, 'disabled' => ($masterState) ? true : false));		
			echo "</div>";
			if ($singleClientPackage) {
				echo $form->input('Track', array('options' => $trackIds, 'empty' => true, 'multiple' => false, 'disabled' => ($masterState) ? true : false));
			}
      echo $form->input('maxNumSales', array('value' => (int)$pricePoints['PricePoint']['maxNumSales'], 'disabled' => 'disabled'));

			$rv=(int)$this->data['SchedulingMaster']['pricePointRetailValue'];
			echo $form->input('retailValue', array("value"=>$rv ,'disabled' => 'disabled'));

			/* TODO: Marketing/Judy needs to be able to override the defaults*/
			echo '<div id="defaults" style="margin: 0; padding: 0">';
			if (isset($defaultFile)) {
				echo $this->renderElement('../scheduling_masters/'.$defaultFile);
			}
			echo '</div>';
			echo $form->input('reserveAmt', array('value' => (int)$package['Package']['reservePrice'], 'disabled' => 'disabled'));
	
			echo '<span id="numDays"';
			/* TODO: Marketing/Judy need to be able to se num days to an arbitrary #, and unlock scheduling delay */
			echo $form->input('numDaysToRun', array('type' => 'select',  'empty' => true, 'options' => array(1 => '1 Day', 2 => '2', 3 => '3', 7 => '7'), 'disabled' => ($masterState) ? 'disabled' : false));

			echo $form->input('schedulingDelayCtrlId', array('label' => 'Scheduling Delay'));

			echo '</span>';

		} else {
			echo $form->input('Track', array('options' => $trackIds, 'empty' => true, 'multiple' => false, 'disabled' => ($masterState) ? true : false));
			echo $form->input('offerTypeId', array('value' => 7, 'type' => 'hidden'));
			echo $form->input('numDaysToRun', array('value' => 1, 'type' => 'hidden'));
		}
        
        
        
		// START DATE/TIME
		echo $form->input('startDatePicker', array('class' => 'format-m-d-y divider-dash highlight-days-06 no-transparency range-low-today fill-grid-no-select', 'label' => 'Start Date', 'readonly' => 'readonly'));
		if ($masterState != 1) {
			?>
			<script>
			delete datePickerController.datePickers['SchedulingMasterStartDatePicker'];
			datePickerController.addDatePicker('SchedulingMasterStartDatePicker',
				{
					'id':'SchedulingMasterStartDatePicker',
					'highlightDays':'0,0,0,0,0,1,1',
					'disableDays':'',
					'divider':'-',
					'format':'m-d-y',
					'locale':true,
					'splitDate':0,
					'noTransparency':true,
					'staticPos':false,
					'hideInput':false,
					'low':datePickerController.dateFormat((new Date().getMonth() + 1) + "/" + new Date().getDate() + "/" + new Date().getFullYear(), true)
				}
			);		
			</script>
			<?			
		}
		echo $form->input('startDateTime', array('label' => 'Start Time', 'disabled' => ($masterState) ? 'disabled' : false,
			'options' => array(
				'00:00:00' => '12 AM', '01:00:00' => '1 AM', '02:00:00' => '2 AM', '03:00:00' => '3 AM', '04:00:00' => '4 AM',
				'05:00:00' => '5 AM', '06:00:00' => '6 AM', '07:00:00' => '7 AM', '08:00:00' => '8 AM', '09:00:00' => '9 AM',
				'10:00:00' => '10 AM', '11:00:00' => '11 AM', '12:00:00' => '12 PM', '13:00:00' => '1 PM', '14:00:00' => '2 PM',
				'15:00:00' => '3 PM', '16:00:00' => '4 PM', '17:00:00' => '5 PM', '18:00:00' => '6 PM', '19:00:00' => '7 PM',
				'20:00:00' => '8 PM', '21:00:00' => '9 PM', '22:00:00' => '10 PM', '23:00:00' => '11 PM'
			)
		));	    



		// FIRST OFFER END DATE/TIME
		echo '<span id="firstOffer">';
			if ($masterState != 1) {
				echo $form->input('endDatePicker', array('class' => 'format-m-d-y divider-dash highlight-days-06 no-transparency range-low-today fill-grid-no-select', 'label' => 'First Offer End Date', 'readonly' => 'readonly'));	
				?>
				<script>
				delete datePickerController.datePickers['SchedulingMasterEndDatePicker'];
				datePickerController.addDatePicker('SchedulingMasterEndDatePicker',
					{
						'id':'SchedulingMasterEndDatePicker',
						'highlightDays':'0,0,0,0,0,1,1',
						'disableDays':'',
						'divider':'-',
						'format':'m-d-y',
						'locale':true,
						'splitDate':0,
						'noTransparency':true,
						'staticPos':false,
						'hideInput':false,
						'low':datePickerController.dateFormat((new Date().getMonth() + 1) + "/" + new Date().getDate() + "/" + new Date().getFullYear(), true)
					}
				);
				</script>
				<?
				echo $form->input('firstIterationEndDateTime', array('label' => 'First Offer End Time', 'disabled' => ($masterState) ? 'disabled' : false,
					'options' => array(
						'' => '',
						'07:00:00' => '7 AM',
						'08:00:00' => '8 AM',
						'09:00:00' => '9 AM',
						'10:00:00' => '10 AM',
						'11:00:00' => '11 AM',
						'12:00:00' => '12 PM',
						'13:00:00' => '1 PM',
						'14:00:00' => '2 PM',
						'15:00:00' => '3 PM',
						'16:00:00' => '4 PM',
					)
				));
			}
		echo '<br/></span>';

		// END DATE / ITERATIONS
		$iterationsStyle = $endDateStyle = ' style="padding: 0; margin: 0"';
		//shows only when fixed number of iterations is selected
		if (empty($this->data['SchedulingMaster']['iterationSchedulingOption']) || $this->data['SchedulingMaster']['iterationSchedulingOption'] == 1) {
			$iterationsStyle = ' style="padding: 0; margin: 0; display: none"';
		} else {
			$endDateStyle = ' style="padding: 0; margin: 0; display: none"';
		}
		//the scheduling iteration option is 0 = iterations, 1 = endDate
		echo $form->input('iterationSchedulingOption', array('type' => 'hidden'));
		echo '<div id="iterations"'.$iterationsStyle.'>';
		if (empty($this->data['SchedulingMaster']['iterations'])) {
			echo $form->input('iterations', array('value' => 1, 'after' => '<div style="padding-left:170px;">Or, <a href="#" onclick=\'javascript:$("SchedulingMasterIterationSchedulingOption").value = "1"; $("iterations").toggle(); $("endDate").toggle() \'>choose end date</a></div>'));
		} else {
			echo $form->input('iterations', array('after' => '<div style="padding-left:170px;">Or, <a href="#" onclick=\'javascript:$("SchedulingMasterIterationSchedulingOption").value = "1"; $("iterations").toggle(); $("endDate").toggle() \'>choose end date</a></div>'));
		}
		echo '</div>';
		echo '<div id="endDate"'.$endDateStyle.'>';
		echo $form->input('endDatePicker2',
			array(
				'class' => 'format-m-d-y divider-dash highlight-days-06 no-transparency range-low-today fill-grid-no-select', 'label' => 'End Date', 'readonly' => 'readonly',
				'after' => '<div style="padding-left:170px;">Or, <a href="#" onclick=\'$("SchedulingMasterIterationSchedulingOption").value = "0"; javascript:$("iterations").toggle(); $("endDate").toggle() \'>choose fixed number of iterations</a></div>'
			)
		);
		echo '</div>';

		echo "<div>";
		echo $form->input("isMystery",array("value"=>$package['packageIsMystery'],"disabled"=>true));
		echo "</div>";



		?>

		<script>
		delete datePickerController.datePickers['SchedulingMasterEndDatePicker2'];
		datePickerController.addDatePicker('SchedulingMasterEndDatePicker2',
			{
				'id':'SchedulingMasterEndDatePicker2',
				'highlightDays':'0,0,0,0,0,1,1',
				'disableDays':'',
				'divider':'-',
				'format':'m-d-y',
				'locale':true,
				'splitDate':0,
				'noTransparency':true,
				'staticPos':false,
				'hideInput':false,
				'low':datePickerController.dateFormat((new Date().getMonth() + 1) + "/" + new Date().getDate() + "/" + new Date().getFullYear(), true)
			}
		);
		</script>
        <?

		echo $form->input('packageId', array('value' => $packageId, 'type' => 'hidden'));

		echo $ajax->observeField('SchedulingMasterOfferTypeId', array(
																'url' => '/scheduling_masters/getOfferTypeDefaults/packageId:'.$packageId,
																'frequency' => 0.2,
			 													'update' => 'defaults',
																'complete' => 'new Effect.Highlight("defaults")'));
        ?>
		<script>
			Event.observe("SchedulingMasterOfferTypeId", "change", toggle_offertype);			
			function toggle_offertype(event) {
				if ($("SchedulingMasterOfferTypeId").getValue() != 7) {
					if ($("SchedulingMasterOfferTypeId").getValue() == 3 || $("SchedulingMasterOfferTypeId").getValue() == 4) {
						$("firstOffer").hide();
						$("numDays").hide();
						$("iterations").hide();
						$("endDate").show();
					} else {
						$("firstOffer").show();
						$("numDays").show();
					}					
				}
			}
			toggle_offertype();
		</script>
		
		
		</div>
		<div id='two' style="display: none">	
		<?php echo $form->input('MerchandisingFlag'); ?>
		</div>
		<div id='three' style="display: none">
			<?php echo $this->renderElement('../scheduling_masters/_mystery_fields'); ?>
		</div>
		<div id='four' style="display: none">
			<?php echo $form->input('previewDate'); ?>
		</div>
	</fieldset>

<?php

}

?>
