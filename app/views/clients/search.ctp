<?php if (isset($results) && count($results) > 0): ?>
<h4>Search Results:</h4>
<ol style="padding: 0; margin: 10px 0 0 0; list-style: none">
<?php foreach($results as $row): ?>
	<li style="margin-bottom: 3px;"><?=$html->link($text->highlight($text->excerpt($row['Client']['name'], $query, 10), $query), array('action' => 'view', $row['Client']['clientId']), null, false , false ); ?><br />
		<?php if ($row['Client']['companyName']): ?>
			<span class="italicize lightBlackTextSmall"><span class="grayTextSmall"> (</span><?=$text->truncate($row['Client']['companyName'])?><span class="grayTextSmall">)</span></span>
		<?php endif ?>
	</li>
<?php endforeach;?>
</ol>
<?php endif ?>