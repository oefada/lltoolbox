<?php

// ADD
/*************************************************************************************************/

if ($masterState != 1) {
    
    echo $form->input('packageId', array('value' => $packageId, 'type' => 'hidden'));
            
    ?>

    <link href="/css/scheduling-master.css" type="text/css" rel="stylesheet" />
    
    <div id="schedulingmaster-add">            
    
        <!-- PRICE POINT -->
        <h2>Choose a Price Point</h2>
        <table id="data-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th></th>
                <th>Name</th>
                <th>Validity</th>
                <th>Maximum<br />Number Sales</th>
                <th>Retail</th>
                <th>% of Retail<br />(Auction)</th>
                <th>% of Retail<br />(Buy Now)</th>
            </tr>
        <?php
            foreach ($pricePoints as $key => $pricePoint) {
                $checked = ($data['SchedulingMaster']['pricePointId'] == $pricePoint['PricePoint']['pricePointId']) ? 'checked' : '';
                echo "
                    <tr>
                        <td>
                            <input type='radio' class='price-point-options' id='price-point-{$pricePoint['PricePoint']['pricePointId']}' name='data[SchedulingMaster][pricePointId]' value='{$pricePoint['PricePoint']['pricePointId']}' $checked/>
                        </td>
                        <td><label for='price-point-{$pricePoint['PricePoint']['pricePointId']}'>{$pricePoint['PricePoint']['name']}</label></td>
                        <td style='font-size:11px;'>{$pricePoint[0]['dateRanges']}</td>
                        <td>{$pricePoint['PricePoint']['maxNumSales']}</td>
                        <td>{$pricePoint['PricePoint']['retailValue']}</td>
                        <td>{$pricePoint['PricePoint']['percentRetailAuc']}</td>
                        <td>{$pricePoint['PricePoint']['percentRetailBuyNow']}</td>
                    </tr>
                ";
            }
        ?>
        </table>
    
        <!-- SCHEDULING -->
        <h2>Scheduling</h2>
        <table id="data-table" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th>Track</th>
                <th>Schedule As</th>
                <th>Start Date</th>
                <th>Start Time</th>
                <th>End Date</th>
            </tr>
            <tr>
                <td><?php if ($singleClientPackage) { echo $form->input('Track', array('options' => $trackIds, 'empty' => true, 'multiple' => false, 'disabled' => ($masterState) ? true : false)); } ?></td>
                <td>
                    <?php if ($package['Format'][0]['formatId'] != 3) { // NOT HOTEL OFFER
                        $isAuctionChecked = ($data['isAuction']) ? 'checked' : '';
                        $isBuyNowChecked = ($data['isBuyNow']) ? 'checked' : '';
                    ?>                        
                        <input id="isAuction" type="checkbox" name="data[isAuction]" <?php echo $isAuctionChecked; ?>/> <label for="isAuction">Auction</label><br /><br />
                        <input id="isBuyNow" type="checkbox" name="data[isBuyNow]" <?php echo $isBuyNowChecked; ?>/> <label for="isBuyNow">Buy Now</label>
                    <?php
                        } else {
                            echo 'Hotel Offer';
                            echo '<input type="hidden" name="data[isHotelOffer]" value="1"/>';
                            echo $form->input('offerTypeId', array('value' => 7, 'type' => 'hidden'));
                            echo $form->input('numDaysToRun', array('value' => 2, 'type' => 'hidden')); 
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
                        <td><?php echo $form->input('numDaysToRun', array('label' => ' ', 'type' => 'select', 'options' => array(2 => '2 Days', 3 => '3 Days', 7 => '7 Days'), 'disabled' => ($masterState) ? 'disabled' : false)); ?></td>
                        <td><?php echo $form->input('schedulingDelayCtrlId', array('label' => ' ')); ?></td>
                        <td>
                            <input type='radio' id='by-end-date' name='data[SchedulingMaster][iterationSchedulingOption]' value='1' 'checked' /> <label for='by-end-date'>Determined By End Date</label><br />
                            <input type='radio' id='by-iteration' name='data[SchedulingMaster][iterationSchedulingOption]' value='0' <?php echo ($data[SchedulingMaster][iterationSchedulingOption] == 0) ? 'checked' : ''; ?> /> <label for='by-iteration'>Or Enter Number of Iterations:</label> <input name="data[SchedulingMaster][iterations]" value="<?php echo ($data[SchedulingMaster][iterations]) ? $data[SchedulingMaster][iterations] : 1; ?>" size="2"/>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- BUY NOW OPTIONS -->
            <div id="buynow-options" style="padding:0px; margin:0px; <?php if (!$data['isBuyNow']) echo 'display:none;'; ?>">
                <h2>Buy Now Options</h2>
                <input type='radio' id='bonus-miles' name='data[buyNowOfferTypeId]' value='4' checked /> <label for='bonus-miles'>Includes Bonus Miles</label><br/>
                <input type='radio' id='suppress-retail' name='data[buyNowOfferTypeId]' value='3' /> <label for='suppress-retail'>Suppress Retail Value</label>        
            </div>
        
        <?php } ?>
        
        <!-- NOTES -->
        <br />
        <h2>Package Notes</h2>
        <textarea name="data[Package][notes]" rows="5"><?php echo $package[Package][notes]; ?></textarea>

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
                //$('auction-options').toggle();
                Effect.toggle('auction-options', 'appear');            
            } else if (element.identify() == 'isBuyNow') {
                //$('buynow-options').toggle();
                Effect.toggle('buynow-options', 'appear');            
                        
            }
        }   
        $('isAuction').observe('change', respondToClick);
        $('isBuyNow').observe('change', respondToClick);
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
			<?php echo 'Package Validity: '.date('M j, Y', strtotime($package['Package']['validityStartDate'])).' to '.date('M j, Y', strtotime($package['Package']['validityEndDate'])) ?>
			</p>
	<?php
	
		if ($package['Format'][0]['formatId'] != 3) {
			echo $form->input('offerTypeId', array('label' => 'Offer Type', 'empty' => true, 'disabled' => ($masterState) ? true : false));		
			if ($singleClientPackage) {
				echo $form->input('Track', array('options' => $trackIds, 'empty' => true, 'multiple' => false, 'disabled' => ($masterState) ? true : false));
			}
            
            echo $form->input('maxNumSales', array('value' => $package['Package']['maxNumSales'], 'disabled' => 'disabled'));
            
			//echo "<strong>For Fixed Price offer types, number of days to run, scheduling delay, and number of iterations will be ignored. You must choose an end date for fixed price offers.</strong>";		
			echo $form->input('retailValue', array('disabled' => 'disabled'));
			
			/* TODO: Marketing/Judy needs to be able to override the defaults*/
			echo '<div id="defaults" style="margin: 0; padding: 0">';
			if (isset($defaultFile)) {
				echo $this->renderElement('../scheduling_masters/'.$defaultFile);
			}
			echo '</div>';
			echo $form->input('reserveAmt', array('value' => $package['Package']['reservePrice'], 'disabled' => 'disabled'));
	
			echo '<span id="numDays"';
				/* TODO: Marketing/Judy need to be able to se num days to an arbitrary #, and unlock scheduling delay */
				echo $form->input('numDaysToRun', array('type' => 'select',  'empty' => true, 'options' => array(2 => '2', 3 => '3', 7 => '7'), 'disabled' => ($masterState) ? 'disabled' : false));
				
					echo $form->input('schedulingDelayCtrlId', array('label' => 'Scheduling Delay'));

			echo '</span>';
		} else {
			echo $form->input('Track', array('options' => $trackIds, 'empty' => true, 'multiple' => false, 'disabled' => ($masterState) ? true : false));
			echo $form->input('offerTypeId', array('value' => 7, 'type' => 'hidden'));
			echo $form->input('numDaysToRun', array('value' => 2, 'type' => 'hidden'));
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
