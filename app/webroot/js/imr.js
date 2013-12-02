$().ready(function() {
    if ($('table#imr') != undefined) {   
        $('td.offers').children('span.bids').each(function(i, offer) {
                if (parseInt($(offer).text()) > 0) {
                    $(offer).css({color: '#336699',
                                  cursor: 'pointer',
                                  'text-decoration': 'underline'});
                    var tooltipText = 'Offer Retail Value: ' + $(offer).nextAll('span.retail').eq(0).text() + '<br />';
                    tooltipText += 'Winning Bid: ' + $(offer).nextAll('span.winningBid').eq(0).text() + '<br />';
                    tooltipText += 'Auction End Date: ' + $(offer).nextAll('span.endDate').eq(0).text();
                    $(offer).wTooltip({
                        content: tooltipText
                    });
                 }
        });
        $.tablesorter.defaults.widgets = ['zebra'];
        $('#imr').tablesorter({
                headers: {
                    8: {sorter: 'digit'},
                    10: { sorter: false }
                }
            });
    }
    $('input.datepicker').each(function(i, cal) {
                        $(cal).datepicker({showOn: 'both',
                                           buttonImage: '/img/cal.png',
                                           buttonImageOnly: true,
                                           showAnim: 'blind',
                                           dateFormat: 'M d yy'}
                                           );
                        }
                );
});
