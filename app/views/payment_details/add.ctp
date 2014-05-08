<?php  $this->pageTitle = 'Payment Processor ' . $html2->c($ticket['Ticket']['ticketId'], 'Ticket Id:');?>
<?php $this->set('hideSidebar', true); ?>
<?php $session->flash();
$session->flash('error');
?>
<div class="paymentDetails form">
<h2>Process payment for:</h2>
<br/>
<table cellspacing="0" cellpadding="0" border="1">
    <tr>
        <td width="200"><strong>Ticket Id</strong></td>
        <td><?php echo $ticket['Ticket']['ticketId'];?></td>
    </tr>
    <tr>
        <td width="200"><strong>Site</strong></td>
        <td><strong><?php echo $siteIds[$ticket['Ticket']['siteId']];?></strong></td>
    </tr>
    <tr>
        <td><strong>User</strong></td>
        <td><?php echo $ticket['Ticket']['userFirstName'] . ' ' . $ticket['Ticket']['userLastName'];?></td>
    </tr>
    <tr>
        <td><strong>User Id</strong></td>
        <td><?php echo $ticket['Ticket']['userId'];?></td>
    </tr>
    <tr>
        <td><strong>Package</strong></td>
        <td><?php echo $ticket['Package']['packageName'];?></td>
    </tr>
    <tr>
        <td><strong>Ticket Amount</strong></td>
        <td><?php echo $number->currency($billingPrice, $currencyName);?> + <?php echo $number->currency($processingFee,
            $currencyName);?> Auction Fee
        </td>
    </tr>
    <?php if (!empty($ticket['UserPromo']['Promo'])) :?>
    <tr>
        <td><strong>Promo Code Applied:</strong></td>
        <td>
            <h3 style="margin:0px;padding:0px;padding-bottom:5px;">
                <?=$ticket['UserPromo']['Promo']['promoName'];?> - [<?=$ticket['UserPromo']['Promo']['promoCode'];?>] -
                Amount Off: <?php echo $number->currency($ticket['UserPromo']['Promo']['totalAmountOff'],
                $currencyName);?>
            </h3>
        </td>
    </tr>
    <?php endif; ?>
</table>
<br/>

<form onSubmit="return false;" id="paymentForm">
    <?php echo $form->input('ticketId', array('type' => 'hidden', 'value' => $ticket['Ticket']['ticketId']));?>
    <?php echo $form->input('userId', array('type' => 'hidden', 'value' => $ticket['Ticket']['userId']));?>
    <?php echo $form->input('offerId', array('type' => 'hidden', 'value' => $ticket['Ticket']['offerId']));?>
    <?php echo $form->input('siteId', array('type' => 'hidden', 'value' => $ticket['Ticket']['siteId']));?>
    <?php echo $form->input('tldId', array('type' => 'hidden', 'value' => $ticket['Ticket']['tldId']));?>
    <div class="paymentsDebug" style="display: none"></div>
    <div class="paymentColumns">
        <div class="col1">
            <h2>Payment Settings:</h2>
            <table cellspacing="0" cellpadding="0" border="1" id="paymentSettings">
                <?php if (isset($ticket['UserPromo']['Cof']['balance'])): ?>
                <tr id="applyCof">
                    <td><strong>Apply Credit on File:</strong></td>
                    <td><input id="applyCofCheck" type="checkbox" name="data['CreditTracking']['applyCoF']" value="1">
                        <span style="color: #ff0000;">(Customer has credit on file of $<?= number_format($ticket['UserPromo']['Cof']['balance'],2) ?>
                            )</span></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="width: 200px;"><strong>Payment Type</strong></td>
                    <td>
                        <select name="data[PaymentDetail][paymentTypeId]" id="PaymentDetailPaymentTypeId">
                            <?php foreach ($paymentTypeIds as $ppId => $ppValue): ?>
                            <?php if ($ppId == 3 && !isset($ticket['UserPromo']['Cof']['balance']) && $ticket['UserPromo']['Cof']['balance'] == 0) continue; ?>
                            <?php if ($ppId == 2 && isset($ticket['UserPromo']['GiftCert']['balance']) && $ticket['UserPromo']['GiftCert']['balance'] <= 0) continue; ?>
                            <option value="<?php echo $ppId;?>"><?php echo $ppValue;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><strong>Payment Processor</strong></td>
                    <td>
                        <select name="data[PaymentDetail][paymentProcessorId]" id="PaymentDetailPaymentProcessorId">
                            <?php foreach ($paymentProcessorIds as $ppId => $ppValue): ?>
                            <option value="<?php echo $ppId;?>"><?php echo $ppValue;?></option>
                            <?php endforeach;?>
                        </select>
                    </td>
                </tr>
                <tr id="showPaymentSetting" style="display: none;">
                    <td colspan="2">
                        <strong>User Payment Setting (choose one):</strong><br/><br/>

                        <fieldset class="collapsible" id="existingCards">
                            <?php include("existing_cards.ctp") ?>
                        </fieldset>
                        <br/>

                        <fieldset class="collapsible">
                            <legend class="handle" style="font-size:12px;" id="addNewCard">Add New Card</legend>
                            <div class="collapsibleContent">

                                <table style="background:whitesmoke;margin:0px;padding:0px;" cellspacing="0"
                                       cellpadding="0" border="0">
                                    <tr>
                                        <td width="150"><br/><strong>Use New Card</strong><br/><br/></td>
                                        <td><br/><input type="checkbox" name="data[UserPaymentSetting][useNewCard]"
                                                        id="UserPaymentSettingUseNewCard"/><br/><br/></td>
                                    </tr>
                                    <tr>
                                        <td width="150">Name on Card</td>
                                        <td><input type="text" name="data[UserPaymentSetting][nameOnCard]"
                                                   id="UserPaymentSettingNameOnCard" size="30"/></td>
                                    </tr>
                                    <tr>
                                        <td>Address 1</td>
                                        <td><input type="text" name="data[UserPaymentSetting][address1]"
                                                   id="UserPaymentSettingAddress1" size="50"/></td>
                                    </tr>
                                    <tr>
                                        <td>Address 2</td>
                                        <td><input type="text" name="data[UserPaymentSetting][address2]"
                                                   id="UserPaymentSettingAddress2" size="50"/></td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td><input type="text" name="data[UserPaymentSetting][city]"
                                                   id="UserPaymentSettingCity" size="20"/></td>
                                    </tr>
                                    <tr>
                                        <td>State</td>
                                        <td><input type="text" name="data[UserPaymentSetting][state]"
                                                   id="UserPaymentSettingState" size="20"/></td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td>
                                            <select name="data[UserPaymentSetting][country]"
                                                    id="UserPaymentSettingCountry">
                                                <?php
												foreach ($countries as $ckey => $country) {
                                                echo "
                                                <option value=\"$ckey\">$country</option>
                                                \n";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Postal/Zip Code</td>
                                        <td><input type="text" name="data[UserPaymentSetting][postalCode]"
                                                   id="UserPaymentSettingPostalCode" size="10"/></td>
                                    </tr>
                                    <tr>
                                        <td>Card Number</td>
                                        <td><input type="text" name="data[UserPaymentSetting][ccNumber]"
                                                   id="UserPaymentSettingCcNumber" size="50"/></td>
                                    </tr>
                                    <tr>
                                        <td>Expiration Month</td>
                                        <td>
                                            <select name="data[UserPaymentSetting][expMonth]"
                                                    id="UserPaymentSettingExpMonth">
                                                <?php
												foreach ($selectExpMonth as $mkey => $eMonth) {
                                                echo "
                                                <option value=\"$eMonth\">$eMonth</option>
                                                \n";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Expiration Year</td>
                                        <td>
                                            <select name="data[UserPaymentSetting][expYear]"
                                                    id="UserPaymentSettingExpYear">
                                                <?php
												foreach ($selectExpYear as $ykey => $eYear) {
                                                echo "
                                                <option value=\"$eYear\">$eYear</option>
                                                \n";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>
                                            <br/>
                                            <input type="checkbox" name="data[UserPaymentSetting][save]"
                                                   id="UserPaymentSettingSave"/>&nbsp;
                                            Save this record for this user
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </fieldset>
                        <?php echo $form->error('userPaymentSettingId') ?>
                    </td>
                </tr>
                <tr id="showWire" style="display:none;">
                    <td colspan="2">
                        <strong>Reference ID:</strong><br/><br/>
                        <input type="text" name="data[PaymentDetail][ppTransactionId]"/>
                    </td>
                </tr>
                <tr id="showPromo" style="display:none;">
                    <td colspan="2">
                        <strong>Promo Code:</strong><br/><br/>
                        <input type="text" name="data[PaymentDetail][ppTransactionId]" id="promoCode"/> <a href="#"
                                                                                                           id="lookupPromo">Lookup
                        Promo</a>

                        <div id="giftBalance" style="display: none">
                            <strong>Valid Code<br>
                                Balance:</strong> $<span id="giftBalanceBalance"></span>.00
                        </div>
                        <div id="promoOff" style="display: none">
                            <strong>Valid Code<br>
                                Amount Off:</strong> <span id="amountOff"></span>
                        </div>
                        <div id="promoInvalid" style="display: none"><strong><span class="textDarkRed">Invalid Promo Code</span></strong>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top:10px;padding-bottom:10px;"><strong>Initials</strong></td>
                    <?php if ($initials_user) : ?>
                    <td style="padding-top:10px;padding-bottom:10px;"><input type="text"
                                                                             name="data[PaymentDetail][initials]"
                                                                             id="PaymentDetailInitials" maxlength="15"
                                                                             size="15" readonly="readonly"
                                                                             value="<?=$initials_user;?>"/></td>
                    <?php else : ?>
                    <td style="padding-top:10px;padding-bottom:10px;"><input type="text"
                                                                             name="data[PaymentDetail][initials]"
                                                                             id="PaymentDetailInitials" maxlength="15"
                                                                             size="15"/><?php echo $form->
                        error('initials') ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <tr style="background-color: #CCEEBB;">
                    <td style="padding-top:10px;padding-bottom:10px;"><strong>Payment Amount</strong></td>
                    <td style="padding-top:10px;padding-bottom:10px;"><?php echo $currencySymbol ?><input type="text"
                                                                                                          name="data[PaymentDetail][paymentAmount]"
                                                                                                          id="PaymentDetailPaymentAmount"
                                                                                                          value="<?= $ticket['UserPromo']['final_price_actual'] ?>"/><?php echo $form->
                        error('paymentAmount') ?>
                        <?php if (!empty($ticket['UserPromo']['Promo']) && $ticket['UserPromo']['Promo']['applied']): ?>
                        (Includes Promo Code Discount)<?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="paymentAdd" value="Add New Payment"> (You will be
                        redirected when balance has reached <?php echo $currencySymbol ?>0.00)
                    </td>
                </tr>
            </table>
        </div>
        <div class="col2">
            <h2>Payments Applied:</h2>

            <div id="paymentsApplied">
                <?php include "payments_applied.ctp"; ?>
            </div>
        </div>
    </div>
    <div style="margin-top: 10px;margin-bottom:10px;">
        <h2>Payments Summary:</h2>
    </div>
    <table id="paymentsSummary">
        <tr style="background-color: #FF8888;" id="paymentsSummaryPayments">
            <td style="width: 300px; padding-top:10px;padding-bottom:10px;"><strong>Total Promos</strong></td>
            <td style="padding-top:10px;padding-bottom:10px;">
                (<?php if (isset($ticket['UserPromo']['Promo']['totalAmountOff'])) { echo $number->
                currency($ticket['UserPromo']['Promo']['totalAmountOff'], $currencyName); } else { echo $currencySymbol
                ."0.00"; } ?>)
            </td>
        </tr>
        <tr style="background-color: #FF8888;" id="paymentsSummaryPayments">
            <td style="width: 300px; padding-top:10px;padding-bottom:10px;"><strong>Total Payments</strong></td>
            <td style="padding-top:10px;padding-bottom:10px;">(<?php echo $currencySymbol ?><span
                    id="totalPayments"><?= $ticket['UserPromo']['payments'] ?></span>.00)
            </td>
        </tr>
        <tr style="background-color: #CCEEBB;">
            <td style="padding-top:10px;padding-bottom:10px;"><strong>Balance Remaining</strong></td>
            <td style="padding-top:10px;padding-bottom:10px;"><?php echo $currencySymbol ?><span
                    id="balanceRemaining"><?= $ticket['UserPromo']['final_price_actual'] ?></span>.00
            </td>
        </tr>
    </table>
    <?php echo $form->end();?>
</div>
<?php
//if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
<script>
var credit_on_file = <?= (empty($ticket['UserPromo']['Cof']['balance']) ? 0 : $ticket['UserPromo']['Cof']['balance']) ?>;
var payment_amt = "<?= $ticket['UserPromo']['final_price_actual'] ?>";
var ticketId = <?= $ticket['Ticket']['ticketId'] ?>;
var payment_get = "";
var xhr;
var expression = true;
var thisUrl = "/tickets/" + ticketId + "/payment_details/add";
var async = true;

jQuery(document).ready(function ($) {
    var ppId = $("#PaymentDetailPaymentProcessorId");
    var ptId = $("#PaymentDetailPaymentTypeId");
    var ps = $("#paymentSettings");

    setCollapse();

    $(".collapsible legend").live('click', function () {
        var cC = $(this).parent().children('.collapsibleContent');
        if (cC.hasClass('closed')) {
            // Closed
            cC.animate({
                opacity: 1,
                height: 'toggle'
            }, 500);
        } else if (cC.hasClass('open')) {
            // Open
            cC.animate({
                opacity: 0.25,
                height: 'toggle'
            }, 500);
        }
    });

    $("#applyCofCheck").click(function () {
        if ($(this).is(":checked")) {

            ptId.val(3).change();

            async = false;
            getPaymentAmt("Cof");
            async = true;

            $("#paymentAdd").click();
            clearCof();
        }
    });

    function clearCof() {
        $("#applyCof").hide();
        ptId.find('option[value=3]').remove();
    }

    $("#paymentAdd").click(function () {
        //$(this).attr('disabled','disabled');

        if (parseInt($("#PaymentDetailPaymentAmount").val()) == 0) {
            alert("You cannot apply a $0.00 payment.");
            return false;
        } else if (payment_amt == "0") {
            var answer = confirm("This ticket has been paid in full. Are you sure you want to apply a payment?");

            if (!answer) {
                return false;
            }
        }

        if (ptId.val() == 2) {
            if ($("#promoCode").val() == '') {
                alert("Please complete the Promo Code field.");
                return false;
            }
        }

        if (ptId.val() == 4) {
            $("#paymentForm")[0].onsubmit = function () {
            };
            $("#paymentForm")[0].action = thisUrl;
            $("#paymentForm")[0].method = "POST";
            $("#paymentForm").submit();
            return false;
        }

        showSpinner();

        // Process payment via Ajax

        var data = { };
        $.post(thisUrl, $("#paymentForm").serialize(), function (data) {
            //$(".paymentsDebug").show().html(data);
            if (data != "CHARGE_SUCCESS") {
                if (data == "NO_ACCT") {
                    alert("You did not select a credit card to charge.");
                } else if (data == "NO_AVS") {
                    alert("ZIP Code provided didn't match credit card. Please verify information.")
                } else {
                    alert("Payment declined or an error occurred. Please verify information.");
                }
            } else {
                alert("Payment charged successfully");
            }

            refreshPayments();

            if (ptId.val() == 3) {
                clearCof();
            }

            getPaymentAmt(payment_get, 1);
        });
    });

    $("#lookupPromo").click(function () {
        $("#giftBalance").hide();
        $("#promoOff").hide();
        $("#promoInvalid").hide();

        var promoCode = $("#promoCode").val();
        var userId = $("#userId").val();
        var paymentAmount = $("#PaymentDetailPaymentAmount").val();
        var offerId = $("#offerId").val();
        var siteId = $("#siteId").val();
        var tldId = $("#tldId").val();

        var data = {
            promoCode: promoCode,
            userId: userId,
            paymentAmount: paymentAmount,
            offerId: offerId,
            siteId: siteId,
            tldId: tldId,
            ptId: ptId.val()
        };

        $.ajax({
            type: "POST",
            url: "/promo_codes/ajax_is_valid",
            data: data,
            success: function(data) {
                data = jQuery.parseJSON(data);

                if (data.status == 200 && data.validPromoCode == true) {
                    $.get("/promo_codes/ajax_valid_promo/" + $("#promoCode").val(), function (data) {
                        var obj = $.parseJSON(data);
                        if (obj == null) {
                            $("#promoInvalid").show();
                        } else if (obj.giftCertBalance.balance != null) {
                            // Gift certificate
                            if (ptId.val() != 2) {
                                alert("The promo you have entered is for a gift certificate, not a promo. Please re-enter.");
                                return false;
                            }

                            payment_amt = obj.giftCertBalance.balance;
                            $("#giftBalance #giftBalanceBalance").html(payment_amt);
                            $("#giftBalance").show();

                            var total_bal = parseInt($("#balanceRemaining").html());

                            if (payment_amt > total_bal) {
                                payment_amt = total_bal;
                            }

                            setPaymentAmt({payment_amt: payment_amt});
                        } else if (obj.promoCodeRel.promoCodeRelId != null) {
                            // Normal promo code
                            if (ptId.val() == 2) {
                                alert("The promo you have entered is for a promo, not a gift certificate. Please re-enter.");
                                return false;
                            }

                            if (obj.promo.amountOff != null && obj.promo.amountOff > 0) {
                                var amountOff = "$" + obj.promo.amountOff;
                                payment_amt = obj.promo.amountOff;
                            } else {
                                payment_amt = Math.round(payment_amt * (obj.promo.percentOff / 100));
                                var amountOff = obj.promo.percentOff + "% / $" + payment_amt;
                            }

                            setPaymentAmt({payment_amt: payment_amt});
                            $("#promoOff #amountOff").html(amountOff);
                            $("#promoOff").show();
                        }
                    });
                } else {
                    $("#promoInvalid").show();
                }
            }
        });

        return false;
    });

    ptId.change(function () {
        var val = $(this).val();
        if (val != 1) {
            $('#showPaymentSetting').hide();
            if (val == 2 || val == 4) {
                // Gift cert
                $('#showPromo').show();
                $('#showWire').hide();
                $('#showPaymentSetting').hide();

                if (val == 2) {
                    payment_get = "GiftCert";
                } else {
                    ppId.val(7);
                }
            } else if (val == 3) {
                // Credit on File
                $('#showWire').hide();
                $('#showPromo').hide();
                $('#showPaymentSetting').hide();
                payment_get = "Cof";
            }

            ppId.attr('disabled', 'disabled');
        } else {
            payment_get = "";
            ppId.val(1);
            $('#showPaymentSetting').show();
            $('#showWire').hide();
            $('#showPromo').hide();
            ppId.removeAttr('disabled');
        }

        showSpinner();
        async = false;
        getPaymentAmt(payment_get);
        async = true;
        hideSpinner();
    });

    ppId.change(function () {
        switch ($(this).val()) {
            case '5':
                $('#showWire').show();
                $('#showPromo').hide();
                $('#showPaymentSetting').hide();
                break;
            default:
                $('#showPromo').hide();
                $('#showWire').hide();
                $('#showPaymentSetting').show();
                break;
        }
    });

    function showSpinner() {
        $("#paymentAdd").attr("disabled", "disabled");
        ps.attr('disabled', 'disabled').fadeTo(500, 0.5);

        var psp = ps.position();

        x = psp.left + ((ps.width() / 2) - $("#spinner").width());
        y = psp.top + ((ps.height() / 2) + $("#spinner").height());

        $("#spinner").css({
            position: 'absolute',
            left: x,
            top: y,
        }).show();
    }

    function hideSpinner() {
        $("#paymentAdd").removeAttr("disabled");
        ps.removeAttr('disabled').fadeTo(500, 1);
        $("#spinner").hide();
    }

    function getPaymentAmt(payment_type, payment_added) {
        if (payment_type != "") {
            payment_type = "=" + payment_type;
        }

        if (xhr) {
            xhr.abort();
        }

        xhr = $.ajax({
            url: thisUrl + "?get_payment" + payment_type,
            type: "GET",
            async: async,
            complete: function (data) {
                var obj = $.parseJSON(data.responseText);

                if (payment_added == 1) {
                    if (ptId.val() == 1) {
                        async = false;
                        refreshCards();
                        async = true;
                    }

                    if (parseInt(obj.balance) <= 0) {
                        window.location.href = "/tickets/view/" + ticketId;
                    }

                    ptId.val(1);
                    ppId.val(1).change();

                    hideSpinner();
                }

                setPaymentAmt(obj);
            }
        });
    }

    function setPaymentAmt(obj) {
        payment_amt = obj.payment_amt;

        $("#balanceRemaining").html(obj.balance);
        $("#PaymentDetailPaymentAmount").val(obj.payment_amt);
        $("#totalPayments").html(obj.total_payments);
    }

    function refreshPayments() {
        $.get(thisUrl + "?payments_applied", function (data) {
            $("#paymentsApplied").hide().html(data).fadeIn('slow');
        });
    }

    function refreshCards() {
        $.ajax({
            url: thisUrl + "?existing_cards",
            method: "GET",
            async: async,
            complete: function (data) {
                $("#existingCards").hide().html(data.responseText).fadeIn('slow');
                setCollapse();
                $('#paymentForm')[0].reset();
            }
        });
    }

    function setCollapse() {
        $(".collapsible").each(function () {
            $(this).find('.collapsibleContent').hide().addClass('closed').removeClass('open');
        });
    }

    ppId.change();
    ptId.change();
});

</script>
