<div class="variation-container">
    <h4>Variation <?php echo $variationId; ?></h4>
    <ul class="sortable" id="<?php echo $sectionId; ?>-<?php echo $variationId; ?>">
    <?php if (!empty($clients)): ?>
            <?php foreach($clients as $client) {
                    if (isset($client['MailingPackageSectionRel'])) {
                        echo $this->element('/mailing_scheduler/list_item', array('mailingPackageSectionRelId' => $client['MailingPackageSectionRel']['mailingPackageSectionRelId'], 'name' => $client['Client']['name']));
                    }
                    else {
                        echo $this->element('/mailing_scheduler/list_item', array('name' => $client['Client']['name']));
                    }
            } ?>
    <?php endif; ?>
    </ul>    
    <form onsubmit="return false;">
        <?php if (empty($sectionContent) || (!empty($sectionContent) && !empty($clients[0]['MailingPackageSectionRel']))): ?>
            <input type="text" class="client-picker" id="picker_<?php echo $sectionId ?>-<?php echo $variationId ?>" <?php if (count($clients) == $maxInsertions) { echo 'disabled="true"'; } ?>/>
            <input type="hidden" name="data[0][clientId]" value="" />
            <input type="hidden" name="data[0][mailingId]" value="<?php echo $mailingId; ?>" />
            <input type="hidden" name="data[0][mailingSectionId]" value="<?php echo $sectionId ?>" />
            <input type="hidden" name="data[0][variation]" value="<?php echo $variationId; ?>" />
            <input type="hidden" class="maxInsertions" value="<?php echo $maxInsertions; ?>" />
            <input type="button" class="add-button" value="Add Client" <?php if (count($clients) == $maxInsertions) { echo 'disabled="true"'; } ?>/>
        <?php else: ?>
            <?php for($i=0; $i < count($clients); $i++): ?>
                <input type="hidden" name="data[<?php echo $i; ?>][clientId]" value="<?php echo $clients[$i]['Client']['clientId']; ?>" />
                <input type="hidden" name="data[<?php echo $i; ?>][mailingId]" value="<?php echo $mailingId; ?>" />
                <input type="hidden" name="data[<?php echo $i; ?>][mailingSectionId]" value="<?php echo $sectionId ?>" />
                <input type="hidden" name="data[<?php echo $i; ?>][variation]" value="<?php echo $variationId; ?>" />
                <input type="hidden" name="data[<?php echo $i; ?>][packageId]" value="<?php echo $clients[$i]['Package']['packageId']; ?>" />
            <?php endfor; ?>
            <?php if (in_array($userDetails['samaccountname'], $superusers) || in_array('Geeks', $userDetails['groups'])): ?>
                    <input type="checkbox" class="freeze-list" /> <strong>Use these clients</strong>
            <?php endif; ?>
        <?php endif; ?>
    </form>
</div>