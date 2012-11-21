<style type="text/css">

	.cms th {
		padding: 5px;
		background-color: #555;
		color: #fff;
	}
	
</style>


<?php $this->pageTitle = 'CMS Tools' ?>
<?php $this->set('hideSidebar', true); ?>

<table class="cms">
	<thead>
		<tr>
			<th>ID</th>
			<th>Site</th>
			<th>Key</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach( $cmsResults as $cms ){
			echo "<tr>";
			echo "<td>" . $cms['c']['id'] . "</td>";
			echo "<td><a href=\"cms/edit/" . $cms['c']['id'] . "\">" . $cms['s']['siteName'] . "</a></td>";
			echo "<td>" . $cms['c']['name'] . "</td>";
			echo "<td>" . $cms['c']['description'] . "</td>";
			echo "</tr>";
		}
	?>
	</tbody>
</table>