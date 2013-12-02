<div class="tickets form">
<?php echo $form->create('Ticket');?>
    <fieldset>
        <legend><?php __('Create Manual Ticket');?></legend>
        <h2>This will NOT autocharge or autosend ppv<br/ ><br /></h2>
    <?php
        echo $form->input('manualTicketInitials', array('readonly' => 'readonly'));
        echo $form->input('ticketNotes');
        echo $form->input('siteId');
        echo $form->input('offerId');
        echo $form->input('userId');
        echo $form->input('bidId');
        echo $form->input('billingPrice');
        echo $form->input('numNights');
        echo $form->input('requestArrival');
        echo $form->input('requestDeparture');
        echo $form->input('requestNumGuests');
        echo $form->input('requestNotes');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
</div>
