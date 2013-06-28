<?php
$this->pageTitle = $clientName . $html2->c($this->data['Loa']['clientId'], 'Client Id:') . '<br />' . $html2->c(
        'manager: ' . $client['Client']['managerUsername']
    );
$this->set('clientId', $this->data['Loa']['clientId']);
?>
<h2 class="title">New LOA</h2>
<div class="loas form">
    <?php echo $form->create('Loa'); ?>
    <fieldset>
        <div class="controlset4">
            <?php echo $multisite->checkbox('Loa'); ?>
        </div>
        <?php
        echo $form->input('accountExecutive');
        echo $form->input('accountManager');
        echo $form->input('accountTypeId', array('label' => 'Account Type'));
        ?>

        <?php
        echo $form->input(
            'startDate',
            array(
                'minYear' => date('Y', strtotime('-1 year')),
                'maxYear' => date('Y', strtotime('+5 year')),
                'timeFormat' => ''
            )
        );
        echo $form->input(
            'endDate',
            array(
                'minYear' => date('Y', strtotime('-1 year')),
                'maxYear' => date('Y', strtotime('+5 year')),
                'timeFormat' => ''
            )
        );
        echo $form->input('loaMembershipTypeId', array('label' => 'Membership Type'));

        echo '<div id="_LoaMembershipFeeEstimated" style="padding:0px;">';
        echo $form->input('membershipFeeEstimated', array('label' => 'Estimated Fee'));
        echo '</div>';

        echo '<div id="_LoaMembershipTotalPackages" style="padding:0px;">';
        echo $form->input('membershipTotalPackages');
        echo '</div>';

        echo $form->input('membershipTotalNights');

        echo '<div id="_LoaMembershipFee" style="padding:0px;">' . $form->input('membershipFee') . '</div>';
        echo $form->input('luxuryLinkFee');
        echo $form->input('familyGetawayFee');
        echo $form->input('advertisingFee');

        echo $form->input('loaPaymentTermId', array(
                'label' => 'Payment Terms',
                'empty' => true
            ));

        echo $form->input('revenueSplitPercentage');

        echo $form->input(
            'notes',
            array(
                'label' => 'LOA Notes',
                'id' => 'loaNotes',
                'onKeyDown' => 'limitText(loaNotes, 300)',
                'onKeyUp' => 'limitText(loaNotes, 300)'
            )
        );

        echo $form->input('averageDailyRate');

        echo $form->input('currencyId');

        echo $form->input('loaLevelId', array('label' => 'LOA Level'));
        echo $form->input(
            'customerApprovalDate',
            array(
                'empty' => true,
                'label' => 'Package in Date',
                'timeFormat' => '',
                'minYear' => date('Y', strtotime('January 01, 2000')),
                'maxYear' => date('Y', strtotime('+5 year'))
            )
        );
        echo '<div class="controlset">' . $form->input('moneyBackGuarantee') . "</div>";
        echo '<div class="controlset">' . $form->input('upgraded', array('label' => 'Risk Free Guarantee')) . "</div>";
        echo '<div class="controlset" style="margin-left:-155px;">';
        echo $form->input(
            'checkboxes',
            array(
                'label' => false,
                'type' => 'select',
                'multiple' => 'checkbox',
                'options' => $checkboxValuesArr
            )
        );
        echo '</div>';
        echo $form->input('numEmailInclusions');
        echo $form->input('auctionCommissionPerc', array('label' => 'Auction % Commission'));
        echo $form->input('buynowCommissionPerc', array('label' => 'BuyNow % Commission'));
        echo $form->input(
            'emailNewsletterDates',
            array(
                'label' => 'Packaging Notes',
                'id' => 'emailNewsletterDates',
                'onKeyDown' => 'limitText(emailNewsletterDates, 300)',
                'onKeyUp' => 'limitText(emailNewsletterDates, 300)'
            )
        );
        echo $form->input(
            'homepageDates',
            array(
                'label' => 'Homepage Placements',
                'id' => 'homepageDates',
                'onKeyDown' => 'limitText(homepageDates, 300)',
                'onKeyUp' => 'limitText(homepageDates, 300)'
            )
        );
        echo $form->input(
            'additionalMarketing',
            array(
                'id' => 'additionalMarketing',
                'onKeyDown' => 'limitText(additionalMarketing, 300)',
                'onKeyUp' => 'limitText(additionalMarketing, 300)'
            )
        );
        echo $form->input('loaNumberPackages', array('label' => 'Commission-Free Packages'));

        echo '<div><label>Client Segment</label><span>' . $client['Client']['segment'] . '</span></div>';

        echo $form->input('socialMediaNotes', array('label' => 'Social Media Notes'));

        echo $form->input('modifiedBy', array('type' => 'hidden', 'value' => $userDetails['username']));
        echo $form->input('clientId', array('type' => 'hidden'));

        echo '<div id="_LoaRetailValueFee" style="padding:0px;display:none;">' . $form->input(
                'retailValueFee',
                array('label' => 'Retail Value Credit')
            ) . '</div>';
        ?>
    </fieldset>
    <?php echo $form->end('Submit'); ?>
</div>


<script type="text/javascript">

    jQuery(document).ready(function () {
        var $ = jQuery,
            loaPaymentTermsElement = $("#LoaLoaPaymentTermId"),
            loaRevenueSplitPercentageElement = $("#LoaRevenueSplitPercentage");
        $("#LoaAddForm").submit(function () {
            if ($("#LoaSitesLuxuryLink").attr('checked') == false && $("#LoaSitesFamily").attr('checked') == false) {
                alert("You must check off which site(s) this is for.");
                return false;
            } else {
                return true;
            }
        });

        if (loaPaymentTermsElement.find("option:selected").text() !== 'Revenue Split') {
            loaRevenueSplitPercentageElement.parent().hide();
        }
        loaPaymentTermsElement.on("change", function() {
            if ($(this).find("option:selected").text() === 'Revenue Split') {
                loaRevenueSplitPercentageElement.parent().show();
                loaRevenueSplitPercentageElement.val('50');
            } else {
                loaRevenueSplitPercentageElement.parent().hide();
                loaRevenueSplitPercentageElement.val('');
            }
        })
    });

    Event.observe('LoaLoaMembershipTypeId', 'change', toggle_fields);
    Event.observe(window, 'load', toggle_fields);
    function toggle_fields() {
        if ($('LoaLoaMembershipTypeId').getValue() == 3) {
            $('_LoaMembershipFee').hide();
            $('_LoaMembershipTotalPackages').show();
            $('_LoaMembershipFeeEstimated').show();
            $('_LoaRetailValueFee').hide();
        } else if ($('LoaLoaMembershipTypeId').getValue() == 5) {
            $('_LoaMembershipFeeEstimated').show();
            $('_LoaRetailValueFee').show();
            $('_LoaMembershipTotalPackages').hide();
            $('_LoaMembershipFee').hide();
        } else {
            $('_LoaMembershipFee').show();
            $('_LoaMembershipTotalPackages').hide();
            $('_LoaMembershipFeeEstimated').hide();
            $('_LoaRetailValueFee').hide();
        }
    }

</script>
