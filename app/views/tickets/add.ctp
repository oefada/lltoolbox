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
        //echo $form->input('userId');
        echo $this->renderElement("input_search", array('name'=>'userId', 'controller'=>'users', 'label'=>'User Id', 'style'=>'width:400px', 'multiSelect'=>'TicketUserId'));
        ?>
        <div class="input text userIdCheck" style="display:none;">

        </div>
        <?
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
<script type="text/javascript">
/***
    jQuery(document).ready(function () {
        var $ = jQuery;
        $(document).on("change", "#TicketUserId", function () {
            $(".userIdCheck").empty().show().html('<span style="color:green;margin-left:200px;"><img src="/img/ajax-loader2.gif">&nbsp;Validating UserId</span>');
           $userId = $(this).val();
            if ($userId.length < 1){
                return false;
            }
            $.ajax({
                type: "POST",
                //same domain, do as html, jsonp not needed
                url: "/tickets/isRegisteredUser/"+$userId+"?"+Math.random()*100000000,
                dataType: 'jsonp',
                success: function(data, textStatus) {
                    if (data.registered == 1){
                        window.myuserdata = data;
                        var $UserHtml = '<br />'+data.userData[0].User.firstName+'&nbsp;'+data.userData[0].User.lastName;
                        $(".userIdCheck").empty().show().html('<p style="width:50%;color:green;">User Found: '+$UserHtml+'</p>');
                        $(".submit input").prop('disabled',false);
                    }else{
                        $(".userIdCheck").empty().show().html('<p class="error" style="width:50%">Unable to Query UserId</p>');
                        $(".submit input").prop('disabled',true);
                    }
                },
                error: function() {
                   $(".userIdCheck").empty().show().html('<p class="error" style="width:50%">Unable to Query UserId</p>');
                    //disable form submit button
                   $(".submit input").prop('disabled','disabled');
                   //$(".submit input").val('disabled...');
                }
            });
        });
    });

    ***/
</script>

</div>
