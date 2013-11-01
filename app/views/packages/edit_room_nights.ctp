<?php $this->layout = 'overlay_form'; ?>

<script type="text/javascript">
    var packageId = <?php echo $package['Package']['packageId']; ?>;
    var clientId = <?php echo $package['Loa']['clientId']; ?>;
    var numNights = <?php echo $package['Package']['numNights']; ?>;
    
    function getNumNights() {
        return numNights;
    }
</script>

<?php echo $html->css('jquery.autocomplete'); 
echo $javascript->link('jquery/jquery-autocomplete/jquery.autocomplete'); ?>
<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js?v=<?=$jsVersion;?>" type="text/javascript"></script>

<div id="errorsContainer" style="display:none;">
    Please fix the following errors:<br />
    <ol>
        <div id="errors">&nbsp;</div>
    </ol>
</div>

<form id="roomNightsForm" method="post">
    <input type="button" id="addRoomLoaItem" value="Add/Change Room Type" /><span class="room-night-header">For <?php echo $package['Package']['numNights']; ?>-Night Package</span>
    <?php if (!empty($ratePeriods)): ?>
        <?php foreach ($ratePeriods as $i => $ratePeriod): ?>
                <?php echo $this->element('package/rate_period', array('ratePeriod' => $ratePeriod, 'i' => $i, 'package' => $package)); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($_GET['isNewItem']) || empty($ratePeriods)): ?>
      <div class="rate-period-button"><input type="button" id="newRatePeriod" value="New Rate Period" /></div>
    <?php endif; ?>
    <?php $taxesIncluded = ($package['Package']['isTaxIncluded'] || empty($ratePeriods)) ? true : false; ?>
    <div class="taxes-included">Taxes Included in this Package?  <input type="radio" value="1" name="data[Package][taxesIncluded]" <?php echo $taxesIncluded ? 'checked' : ''; ?>/> Yes  <input type="radio" value="0" name="data[Package][taxesIncluded]" <?php echo $taxesIncluded ? '' : 'checked'; ?>/> No </div>
    <input type="button" value="Save Changes" onclick="submitForm('roomNightsForm');" />
</form>
