<?php if(isset($grid) && $grid): ?>
	<div class='gBackgroundGrid'>
<?php else: ?>
	<div id='mDayContainer' class='clearfix'>
<?php endif; ?>
	<?php for($day = 1; $day <= $monthDays; $day++):
		$classes = array();
		$classes[] = 'cDay';
		
		if ($day % 3 == 0) {
			$classes[] = 'cThirdDay';
		}
		
		if ($day == $monthDays) {
			$classes[] = 'cLastDay';
		}
		
		if (strtotime($year.'-'.$month.'-'.$day) == strtotime(date('Y-m-j'))) {
			$classes[] = 'cDayToday';
		} else {
			$classes[] = 'cDayNotToday';
		}
		
		$class = ' class="'.implode($classes, ' ').'"';
	?>
	<?php if(isset($grid) && $grid): ?>
		<div <?=$class?> style="width: <?=100/$monthDays?>%; left: <?=100/$monthDays*($day-1)?>%"></div>
	<?php else: ?>
		<div id="<?=$clientId.$month.$year.$day?>" <?=$class?> style="width: <?=100/$monthDays?>%; left: <?=100/$monthDays*($day-1)?>%"><?=$day?></div>
	<?php endif; ?>
	<?php endfor; ?>
</div>