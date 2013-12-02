<ul>
<?php foreach ($results as $result): ?>
	<li><a href="/popular_travel_ideas/edit/<?=$result['PopularTravelIdea']['popularTravelIdeaId']?>"><?=$text->highlight($result['Style']['style_name'], $query)?> - <?=$text->highlight($result['PopularTravelIdea']['popularTravelIdeaName'], $query)?> (styles:<?=$text->highlight($result['PopularTravelIdea']['linkToMultipleStyles'],$query)?> | keywords:<?=$text->highlight($result['PopularTravelIdea']['keywords'], $query)?>)</a></li>
<?php endforeach ?>
</ul>