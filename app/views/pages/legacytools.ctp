<?php
$this->pageTitle = 'Site Tools / Merchandising';
$this->set('hideSidebar', true);
?>
<style>
	ul.shadowMenu li {
		display: inline-block;
		box-shadow: 0px 5px 5px 0 rgba(0, 0, 0, 0.15);
		border: 1px solid #000088;
		margin: 5px;
		padding: 5px;
		text-align: center;
		vertical-align: bottom;
	}
	ul.shadowMenu li:hover {
		border-color: blue;
		box-shadow: 0px 5px 5px 0 rgba(0, 0, 255, 0.15);
	}
	ul.shadowMenu li span {
		display: inline-block;
		width: 64px;
		height: 64px;
	}
	ul.shadowMenu li span img {
		max-width: 64px;
		max-height: 64px;
	}
	ul.shadowMenu li a {
		text-decoration: none;
	}
</style>
<h2>Site Tools</h2>
<div>
	<div style="float: left; opacity: 0.33; width: 200px;">
		<ul>
		<li><?=$html->link("Promo Tool", '/promos', array('escape'=>false))?></li>
		<li><?=$html->link("Help FAQ", 'legacytools/help_faq')?></li>
		<li><?=$html->link("Landing Pages", '/landing_pages')?></li>
		<li><?=$html->link("Client Scores", '/client_scores')?></li>
		<li><?=$html->link("Articles", '/articles')?></li>
		<li><?=$html->link("Deal of the Day", '/deal_of_the_days')?></li>
		<li><?=$html->link("Missing PPV Images", 'legacytools/ppv_client_images')?></li>
		<li><?=$html->link("Clients Without Live Packages Report", '/reports/active_loa_and_packages_check')?></li>
		<li><?=$html->link('Site Merchandising Tool', '/merchandising')?></li>
		<li><?=$html->link('Blocks Editor', array('controller'=>'blocks'));?></li>
		<li><?=$html->link('CMS Tools', array('controller'=>'cms'));?></li>
		</ul>
	</div>
	<div>
		<ul class="shadowMenu">
			<?php
			$data = array();
			$data['Promo Tool'] = array('link'=>array('controller'=>'promos'), 'icon'=>'coupon.png');
			$data['Help FAQ'] = array('link'=>array('action'=>'legacytools','help_faq'), 'icon'=>'help.png');
			$data['Landing Pages'] = array('link'=>array('controller'=>'landing_pages'), 'icon'=>'landingpages.png');
			$data['Client Scores'] = array('link'=>array('controller'=>'client_scores'), 'icon'=>'clientscore.png');
			$data['Articles'] = array('link'=>array('controller'=>'articles'), 'icon'=>'article.png');
			$data['Deal of the Day'] = array('link'=>array('controller'=>'deal_of_the_days'), 'icon'=>'dealoftheday.png');
			$data['Missing PPV Images'] = array('link'=>array('action'=>'legacytools','ppv_client_images'), 'icon'=>'missingimage.png');
			$data['Clients w/o Live Packges'] = array('link'=>array('controller'=>'reports','action'=>'active_loa_and_packages_check'), 'icon'=>'package.png');
			$data['Site Merchandising Tool'] = array('link'=>array('controller'=>'merchandising'), 'icon'=>'storeshelf.png');
			$data['Blocks Editor'] = array('link'=>array('controller'=>'blocks'), 'icon'=>'blocks.png');
			$data['CMS Tools'] = array('link'=>array('controller'=>'cms'), 'icon'=>'cmstools.png');
			$data['Nile Guide'] = array('link'=>array('controller'=>'nile_guide'), 'icon'=>'nileguide.png');
			ksort($data);
			foreach ($data as $t=>$d) {
				//echo '<li>' . $html->link(($d['icon']?$html->image($d['icon']):'').$t, $d['link'], array('escape' => false)) . '</li>';
				echo $html->link('<li>'.($d['icon']?'<span>'.$html->image($d['icon']).'</span><br/>':'').$t.'</li>', $d['link'], array('escape' => false)) . '</li>';
			}
			?>
		</ul>
	</div>
	<div style="clear: both;"></div>
</div>
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
