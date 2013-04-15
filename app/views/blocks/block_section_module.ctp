<ul>
    <li>
        Destination page sections:
        <ul>
            <?php
            $links = array(
                'destinationMainGraphic',
                'destinationMainGraphicImage',
                'destinationMainGraphicSelector',
                'destinationFeaturedVacations',
                'destinationWhatWeLove',
                'destinationWhatWeLoveSidebar',
                'destinationPhotos',
                'destinationCommunity',
                'destinationMaps',
                'destinationChatLog',
                'destinationTripPlanning',
                'destinationLearnMore',
            );
            foreach ($links as $link) {
                echo '<li>';
                echo '<a href="#class-' . $link . '" onclick="';
                echo "jQuery('#editorDiv input[name=&quot;keyName&quot;]').val('$link').change();";
                echo 'return false;">';
                echo $link;
                echo '</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </li>
</ul>
