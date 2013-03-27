<div style="float: right;">
    Drag this to your bookmarks toolbar: <a
        href="javascript:void(LL.ls('/inc/js/nile_guide_toolbox.js?rand='+new%20Date().getTime()));"
        style="background: #eeeeee; border-style: solid; border-color: blue; border-width: 3px; border-radius: 8px; color: blue; padding: 5px; font-weight: bold; text-decoration: none; cursor: move;">Nile
        Guide Toolbox</a>
</div>
<h2>Nile Guide</h2>
<br/>
<script type="text/javascript">
    jQuery('a[href^="javascript:LL.ls"]').click(function (e) {
        e.preventDefault();
        alert('Drag this link to your Bookmarks toolbar');
    });
</script>
<div>
    <div>
        <form method="GET"><?php

            echo $form->input(
                'title',
                array(
                    'label' => 'Attraction Name: ',
                    'value' => (isset($this->params['url']['data']['title']) ? $this->params['url']['data']['title'] : '')
                )
            );
            echo $form->submit(
                'Search Attractions'
            );
            ?></form>
    </div>
    <?php if (isset($attractions)): ?>
        <div>
            <?php foreach ($attractions as $attraction): ?>
                <div>
                    <?php echo $html->link(
                        $attraction['NileGuideAttraction']['title'],
                        array('action' => 'attraction', $attraction['NileGuideAttraction']['ngId'])
                    ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
