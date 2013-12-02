<div class='module collapsible' id='module-schedulingraphs'>
	<div class='handle'>LOA Balance Details</div>
	<div class='collapsibleContent disableAutoCollapse'>
<table cellpadding="0" cellspacing="0">
	<thead>
    	<tr>
        	<th>&nbsp;</th>
            <th class="bar" id="month-bar">
            <span class="left">0</span>
            <span class="right">100%</span>
            <?php echo date('M Y', strtotime($currentLoa['Loa']['startDate'])).'-'.date('M Y', strtotime($currentLoa['Loa']['endDate']))?>
            </th>
            <th class="num">Value</th>
            <th>Over?</th>
         </tr>
         <tr class="hidden">
          	<td>&nbsp;</td>
			<?
			//calculate left value depending on where we are for the LOA expiration date
			$ts_startDate = strtotime($currentLoa['Loa']['startDate']);
			$ts_endDate = strtotime($currentLoa['Loa']['endDate']);
			$ts_today	  = time();
			$balancePaid = $currentLoa['Loa']['totalRevenue'] - $currentLoa['Loa']['membershipBalance'];
			
			$divisor = ($ts_endDate-$ts_startDate)/300;
			if ($divisor==0)$left=0;
			else $left = ($ts_today-$ts_startDate)/$divisor;
			
			if ($left < 0) $left = 0;
			if ($left > 300) $left = 300;
			?>
          	<td><div id="month-container"><div id="month-line" style="left: <?=$left?>px; height: 70px;"><small><?php echo date('M d')?></small></div></div>
          	</td>
         </tr>
     </thead>
            <tbody>
				<? 
					if ($currentLoa['Loa']['totalRevenue'] > 0){
						$width = ($balancePaid)/($currentLoa['Loa']['totalRevenue'])*300;
					} else {
						$width = 0;
					}
					if ($width < 0) $width = 0;
					if ($width > 300) $width = 300;
					
					$class = '';
					if ($width == 300) {
						$class = ' class="overbalance"';
					}
					
					if ($width < 150 && $width < $left - 50) {
						$class = ' class="warning"';
					}
					
				?>
            <tr<?=$class?> id="budget-1">
            	<th>LOA Current Balance</th>
                <td class="bar"><div class="bar" style="width: <?=$width?>px"><span><?='$'.$balancePaid?></span></div></td>
                <td><?='$'.$currentLoa['Loa']['totalRevenue']?></td>
				<td class="overbalance">
				<? 
				$amountOver = ($balancePaid-$currentLoa['Loa']['totalRevenue']);
				if ($amountOver > 0) {
					echo '$'.$amountOver;
				}
				?>
				</td>
            </tr>
			<?
				if (!isset($loaBalanceFlag['totalOpeningBidSum'])) {
					$loaBalanceFlag['totalOpeningBidSum'] = 0;
				}
				if ($currentLoa['Loa']['totalRevenue'] > 0) {
					$width = ($balancePaid+$loaBalanceFlag['totalOpeningBidSum'])/($currentLoa['Loa']['totalRevenue'])*300;
				} else {
					$width = 0;
				}
				
				if ($width < 0) $width = 0;
				if ($width > 300) $width = 300;
				
				$class = '';
				if (isset($loaBalanceFlag['class']) && $loaBalanceFlag['class'] == 'icon-error' || $amountOver > 0) {
					$class = ' class="overbalance"';
				}
				
				if (isset($loaBalanceFlag['class']) && $loaBalanceFlag['class'] == 'icon-yellow') {
					$class = ' class="warning"';
				}
			?>
            <tr<?=$class?> id="budget-1">
            	<th>Projected Balance</th>
                <td class="bar"><div class="bar" style="width: <?=$width?>px"><span <?if(!empty($loaBalanceFlag['class'])) echo 'style="display:none"'?>><?='$'.($balancePaid+$loaBalanceFlag['totalOpeningBidSum'])?></span></div></td>
                <td><?='$'.$currentLoa['Loa']['totalRevenue']?></td>
				<td class='<?php echo isset($loaBalanceFlag['class']) ? $loaBalanceFlag['class'] : "" ?>'>
				<? 
				$amountOver = ($balancePaid+$loaBalanceFlag['totalOpeningBidSum']-$currentLoa['Loa']['totalRevenue']);
				if ($amountOver > 0) {
					echo '$'.$amountOver;
				}
				?>
				</td>
            </tr>
</tbody></table>
</div>
</div>
