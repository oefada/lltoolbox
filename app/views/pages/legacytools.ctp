<? $this->pageTitle = 'Site Tools / Merchandising' ?>
<?php $this->set('hideSidebar', true); ?>
<h2>Site Tools</h2>
<ul>
<li><?=$html->link("Promo Tool", '/promos')?></li>
<li><?=$html->link("Help FAQ", 'legacytools/help_faq')?></li>
<li><?=$html->link("Landing Pages", '/landing_pages')?></li>
<li><?=$html->link("Client Scores", '/client_scores')?></li>
<li><?=$html->link("Articles", '/articles')?></li>
<li><?=$html->link("Deal of the Day", '/deal_of_the_days')?></li>
<li><?=$html->link("Missing PPV Images", 'legacytools/ppv_client_images')?></li>
<li><?=$html->link("Clients Without Live Packages Report", '/reports/active_loa_and_packages_check')?></li>
<li><?=$html->link('Site Merchandising Tool', '/merchandising')?></li>
<li><?=$html->link('Blocks Editor', array('controller'=>'blocks'));?></li>
</ul>
<br>
<h2>Search Tools</h2>
<ul>
	<li>Geo Matching
		<ul>
			<li><?=$html->link("City List Maintenance",array('action'=>'index', 'controller' => 'cities')) ?></li>
			<li><?=$html->link("State List Maintenance",array('action'=>'index', 'controller' => 'states')) ?></li>
			<li><?=$html->link("Country List Maintenance",array('action'=>'index', 'controller' => 'countries')) ?></li>
		</ul>
	</li>
	<li><?=$html->link("Keyword Search Redirect Tool", '/search_redirects')?></li>
</ul>
<br>
<h2>Legacy Tools</h2>
<ul>
<li><?=$html->link("Merchandise Images Tool", 'legacytools/merchandise_images_tool')?></li>
<li><?=$html->link("Featured Escapes Tool", 'legacytools/featured_escapes_tool')?></li>
<li><?=$html->link("Homepage Merchandising Modules", '/homepage_merchandisings')?></li>
<li><?=$html->link("MasterCard Offer Promo Tool", '/package_promo_rels/mastercard')?></li>
<li><?=$html->link("Menu Tool", '/menus')?></li>
</ul>



<br />

<? 
/*
<li><?=$html->link("Auction Clearance Promo", 'legacytools/auction_exclusive_promo')?></li>
<li><?=$html->link("What's New", 'legacytools/whats_new')?></li>
<li><?=$html->link("Popular Travel Ideas Tool", '/popular_travel_ideas')?></li>
*/
?>
