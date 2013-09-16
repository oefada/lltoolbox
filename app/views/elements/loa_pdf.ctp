<?
Configure::write('debug', 0);
?>
<style type="text/css">
    ol li{margin-bottom:10px;}

    ol li {
        list-style: none;
    }

    ol li:before {
        content: counter(list, lower-alpha) ") ";
        counter-increment: list;
    }

    p{text-indent: none;}
</style>
<p>
    &nbsp;
</p>
<p>
    &nbsp;
</p>

<p><?=date('F d, Y', strtotime($loa['LoaDocument']['docDate']));?></p>

<h3><strong>Partnership Program Overview for <?=$loa['Client']['name'];?></strong>
</h3>
<p>Dear <?=$loa['LoaDocument']['contactName']?>,
</p>
<p>It is my pleasure to present this partnership invitation for your review. As we discussed, Luxury Link Travel Group (LLTG) is uniquely positioned to send
    thousands of qualified leads and direct consumer business to <?=$loa['Client']['name'];?> via our Partner Program while providing brand lift within a targeted
    environment.
</p>
<p>To achieve these goals, The Luxury Link Travel Group (LLTG) will provide the following services to <?=$loa['Client']['name'];?> in the Luxury Link partner program:
</p>
<p>
    <u><strong>Services Benefitting <?=$loa['Client']['name'];?></strong></u>
</p>
<ol>
    <li><p><strong>Customized Landing Page - </strong><?=$loa['Client']['name'];?> will receive a customized landing page including high-resolution image gallery, interactive maps, accolades listing and
            prominent links to <?=$loa['Client']['name'];?> website and booking engine to promote <strong>direct bookings.</strong> Landing pages will appear in all
            appropriate key word searches on Luxury Link.</p>

    </li>
    <li><p><strong>Call forwarding service - </strong>a unique, toll-free telephone number will be assigned to <?=$loa['Client']['name'];?>; consumer calls will be forwarded to your reservations desk for            <strong>direct booking</strong>.<strong> </strong>
        </p>
    </li>
    <li><p><strong>Unlimited reservation referrals</strong> , clicks, and calls (commission-free)
        </p>
    </li>
    <li><p><strong>Unlimited promotional package sales</strong>
        </p>
    </li>
    <li><p><strong>Customer Data – </strong>
            name, address, email address, phone number, opt-in for all LL consumers who have requested/purchased reservations, called your reservations center
            via LL toll-free numbers, or signed up for your newsletter via Luxury Link. Delivered monthly.<strong> </strong>
        </p>
    </li>
    <li><p><strong>Targeted placements - </strong>
            <?=$loa['Client']['name'];?> will receive the following placements on the Luxury Link web site:
        <ul><li><strong>Destination and Interest Landing Pages</strong> – regular rotation on appropriate Destination and Interest pages (e.g. Beach, Romance, All Inclusive, Food &amp; Wine, etc.)
            </li>
            <?if (!empty($loa['Loa']['numEmailInclusions'])){ ?>
            <li><strong>E-Mail Newsletters</strong>
                    – <?=$loa['Client']['name'];?> will be guaranteed inclusion in (<?=$loa['Loa']['numEmailInclusions']?>) two Luxury Link e-newsletters. Partner must have a mutually approved promotional package
                    live on Luxury Link web site to be included.
            </li>
            <? }?>
        </ul></p>
    </li>
    <li><strong>Social Media Suite</strong>
        <? if(in_array(trim($loa['Client']['segment']),array('A','M'))){ ?>
        <ul>
            <li>Launch Week Introduction
                <ul><li>Inclusion in “New Properties” photo gallery on Facebook and LL Lounge</li></ul>
            </li>
            <li>General Manager interview posted on your property Showcase Page
            </li>
            <li>2 property-exclusive posts on Facebook
            </li>
            <li>5 property-exclusive Twitter posts
            </li>
            <li>Inclusion of photo on appropriate LLTG Pinterest board
            </li>
            <li>Potential inclusion in LLTG social media features (e.g. destination/lifestyle- specific posts)
            </li>
            <li>Additional exposure via Family Getaway and Vacationist social media channels, if applicable
            </li>
        </ul>
        <? }else{?>
            <ul>
                <li>Launch Week Introduction -Inclusion in “New Properties” photo gallery on Facebook and LL Lounge
                </li>
                <li>General Manager interview posted on your property Showcase Page
                </li>
                <li>Inclusion of photo on appropriate LLTG Pinterest board
                </li>
                <li>2 property-exclusive Twitter posts
                </li>
                <li>Potential inclusion in LLTG social media features (e.g. destination/lifestyle- specific posts)
                </li>
                <li>Additional exposure via Family Getaway and Vacationist social media channels, if applicable
                </li>
            </ul>

        <?}?>
    </li>
    <li><p><strong>Private Sale </strong>
            – <?=$loa['Client']['name'];?> is entitled to a minimum of one (1) sale on <strong>Vacationist.com</strong>. Subject to Vacationist pricing guidelines and
            scheduling.
        </p>
    </li>
    <li><p><strong>Management &amp; Reporting</strong>
            – A dedicated Senior Account Manager will be assigned to <?=$loa['Client']['name'];?> to ensure your program is optimized throughout your term. <?=$loa['Client']['name'];?> will receive detailed monthly marketing and customer acquisition reports.
        </p>
    </li>
    <? if(!empty($loa['Loa']['moneyBackGuarantee']) ||
        !empty($loa['Loa']['checkboxes'])){ ?>
    <li><p><strong>Additional Marketing</strong>
        <ul>
            <? if(!empty($loa['Loa']['moneyBackGuarantee'])){?><li>Money Back Guarantee</li>
            <? } ?>
            <? if(!empty($loa['Loa']['checkboxes'])){
                $specialTerms = explode(',',$loa['Loa']['checkboxes']);
                foreach ($specialTerms as $term){
                    $term = ucwords($term);
                    ?>
                    <li><?=$term?></li>
                <?}
            }?>
        </ul>
        </p>
    </li>
    <? } ?>
    <? if (!empty($loa['Loa']['notes'])){?><li><p><strong>Special Inclusions</strong>
            <br /><?=$loa['Loa']['notes'];?>
        </p>
    </li><?}?>

</ol>
<p><u><strong>Fees</strong></u>
</p>
<p>The fee for the Luxury Link standard program is $12,000 per property, per year, plus a 20% transaction fee for each promotional Luxury Link package sold on
    our site. <strong>By special agreement</strong>, Luxury Link is pleased to extend <?=$loa['Client']['name'];?> the following special rates:
</p>

<table>
    <?if (!empty($loa['Loa']['membershipFee'])){ ?>
    <tr>
        <td width="50%"><strong>Membership<br/></strong></td>
        <td>$<?=number_format($loa['Loa']['membershipFee'],0,'.',',');?> <?if($loa['Loa']['loaMembershipTypeId'] == 1){?><em>(100% Payable in Barter)</em><?}?></td>
    </tr>
    <? }?>
    <?if (!empty($loa['Loa']['membershipTotalPackages'])){ ?>
        <tr>
            <td width="50%"><strong>Total Packages<br/></strong></td>
            <td><?=$loa['Loa']['membershipTotalPackages'];?> <em><strong></strong></em></td>
        </tr>
    <? }?>
    <?if (!empty($loa['Loa']['membershipTotalNights'])){ ?>
        <tr>
            <td width="50%"><strong>Total Nights<br/></strong></td>
            <td><?=$loa['Loa']['membershipTotalNights'];?> <em><strong></strong></em></td>
        </tr>
    <? }?>
    <tr>
        <td><strong>Promotional Packages Sales Commission</strong></td>
        <td><? if (!empty($loa['Loa']['auctionCommissionPerc'])){?>Auctions: <?=$loa['Loa']['auctionCommissionPerc'];?>%<? } ?>
        <? if (!empty($loa['Loa']['buynowCommissionPerc'])){?><br />Buy Now: <?=$loa['Loa']['buynowCommissionPerc'];?>%<br /><? } ?>
        </td>
    </tr>
    <tr>
        <td><strong>Direct Reservations/Referrals/Leads</strong></td>
        <td>0%</td>
    </tr>
</table>

<!--variable text BEGIN-->
<p><u>How it Works</u>
    <?=$loa['howText'];?>
</p>

<!--variable text END-->
<p>All direct reservations and leads generated by Luxury Link and transacted by <?=$loa['Client']['name'];?> shall be <u>unlimited and commission-free.</u>
</p>
<p><u><strong>Term</strong></u>
</p>
<p>The program outlined above will commence on or before <?=date('F d, Y',strtotime($loa['Loa']['startDate']));?> and will conclude on <?=date('F d, Y',strtotime($loa['Loa']['endDate']));?>.
</p>
<p><?=$loa['LoaDocument']['contactName']?>, on behalf of the entire Luxury Link Travel Group, we look forward to a successful year ahead.
</p>


<p>
    &nbsp;
</p>
<br />
<table width="100%">
    <tr><th width="200">Warm Regards,</th><th>&nbsp;</th><th>Approved:</th></tr>
    <tr><td></td><th>&nbsp;</th><td style="height:40px;"></td></tr>
    <tr><td ><?=$loa['LoaDocument']['signerName']?></td><th>&nbsp;</th><td>&nbsp;</td></tr>
    <tr><td style="border-top:1px solid #CCC;"><?=$loa['LoaDocument']['signerTitle']?></td><th>&nbsp;</th><td style="border-top:1px solid #CCC;"><?=$loa['LoaDocument']['contactName']?></td></tr>
    <tr><td>Luxury Link Travel Group</td><th>&nbsp;</th><td><?=$loa['Client']['name'];?></td></tr>
</table>

