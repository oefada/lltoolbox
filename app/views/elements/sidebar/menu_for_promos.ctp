
<ul id="menu" class="tree">
	<li><?=$html->link('List Promos', "/promos/")?></li>
	<li><?=$html->link('Add New Promo', "/promos/edit/")?></li>
	<? if (isset($menuPromoIdEdit)) { ?>
		<li><?=$html->link('Edit This Promo', "/promos/edit/" . $menuPromoIdEdit)?></li>
	<? } ?>
	<? if (isset($menuPromoIdViewCodes)) { ?>
		<li><?=$html->link('View Promo Codes', "/promo_code_rels/index/" . $menuPromoIdViewCodes)?></li>
	<? } ?>
	<? if (isset($menuPromoIdAddCodes)) { ?>
		<li><?=$html->link('Add Promo Codes', "/promo_codes/add/" . $menuPromoIdAddCodes)?></li>
	<? } ?>
</ul>
