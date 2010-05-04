$().ready(function() {
        $('div.edit-link').click(function() {
                                    $('iframe#dynamicForm').attr('src', '/pages/package_proto/'+$(this).attr('name'));
                                    $('div#formContainer').dialog({modal:true,
                                                                   autoOpen:false,
                                                                   height:700,
                                                                   width:1000,
                                                                   title:'Edit'
                                                                  });
                                    $('div#formContainer').dialog('open');
                        }
                    );
    
    
        if (getSiteName() == 'Family Getaway' || getSiteName() == 'Both') {
            $('div#familyAgeRanges').show();
        }
        
        //show/hide age ranges
        $('select#site').change(function() {
                $('div#familyAgeRanges').toggle();
            }); //end select#site
        
        //enable the status dropdown
        $('span#overrideStatus').click(function() {
                $('select#status').attr('disabled', false);
                $(this).html('');
            }); //end span#overrideStatus
        
        //autopopulate the minGuests and maxAdults fields for LL
        $('input#maxGuests').blur(function() {
                if (getSiteName() == 'Luxury Link') {
                    $.each(['input#minGuests', 'input#maxAdults'], function(numGuests) {
                            return function(i, item) {
                                $(item).val(numGuests);
                            } //end function(item)
                        } ($(this).val())
                    );  // end fields.each()
                }
            }); //end input#maxGuests
        
        //show/hide the custom disclaimer textbox
        $.each(['span#overrideDisclaimer', 'span#useDefault'], function(i, item) {
                $(item).bind('click', function() {
                    $.each(['span#defaultDisclaimer', 'span#customDisclaimer', 'span#overrideDisclaimer', 'span#useDefault'], function(i, childItem) {
                        $(childItem).toggle();
                    }); // end each.childItems
                }); //end item.bind
            }); //end each.disclaimers
        
        //add a room type to a rate period
        $('input#addRoomNight').click(function() {
                addRoomTypeBox();
            }); //end input#addRoomNight
        
        //add a rate period to a package
        $('input#newRatePeriod').click(function() {
                addRatePeriodBox();
            }); //end input#newRatePeriod
        
        //recalculate starting price fields from percent off
        $('input.percentRetail').blur(function() {
                var thisElem = $(this).parentsUntil('table');
                recalcDiscount($(this).attr('value'), thisElem);
            });
        
        $('input#addBlackoutDates').click(function() {
                if ($('div#newBlackout').is(':hidden')) {
                    $('div#newBlackout').show();
                }
                else {
                    var newBlackout = $(this).parent();
                    $.each($(newBlackout), function(i, item) {
                            $(item).attr('value', '');
                            $(item).attr('checked', false);
                        });
                    $(this).before(newBlackout);
                    init_datepickers();
                }
            });
        
        $('span#cancelBlackoutEdit').click(function() {
                $.each($(this).parent().find('input'), function(i, item) {
                        $(item).attr('value', '');
                        $(item).attr('checked', false);
                    });
                $(this).parent('div#newBlackout').hide();
            });
        
        //datepicker
        init_datepickers();
    }
); //end $().ready

var datepickerFields = '<tr><td><input type="text" size="10" class="datepicker" name="startDate" value="" /></td><td><input type="text" size="10" class="datepicker" name="endDate" value="" /></td><td><span class="x-remove" onclick="removeDateRange(this);">[x]</span></td></tr>';

function recalcDiscount(percentOff, ratePeriod) {
    var retailPrice = parseFloat($(ratePeriod).find('span.retailPrice').html().replace(/,/g, ''));
    var usdRetailPrice = parseFloat($(ratePeriod).find('span.usdRetailPrice').html().replace(/,/g, ''));
    $(ratePeriod).find('span.startingPrice').html(Math.round((retailPrice - (retailPrice * (percentOff/100)))*100)/100);
    $(ratePeriod).find('span.usdStartingPrice').html(Math.round((usdRetailPrice - (usdRetailPrice * (percentOff/100)))*100)/100);
}

function getSiteName() {
    return $('#site :selected').text();
}

function removeRoomType(roomTypeTable) {
     roomTypeTable.remove();
}

function addRoomTypeBox() {
    $.each($('table.room-night'), function(i, tableElem) {
        var roomNightIndex = i + 1;
        var newTable = $(document.createElement('table')).attr('class', 'roomTypeDetails').append($('table#roomType').html());
        var removeTr = $(document.createElement('tr')).attr('class', 'room-type').append($(document.createElement('td')).attr('colspan', '2').attr('align', 'right').html('<span id="removeRoomType'+i+'" class="link" onclick="removeRoomType($(this).parents(\'table.roomTypeDetails\'))">Remove</span>'));
        $(newTable).append($(removeTr));
        $('table#roomNightTaxes_'+roomNightIndex).before(newTable); 
    }); //end .each room-type tables
}

function addRatePeriodBox() {
    var newTable = $(document.createElement('table')).attr('class', 'room-night').append($('table.room-night').last().html());
    var newRatePeriod = $(datepickerFields);
    $(newTable).find('table.room-nights-col2').find('tr').first().replaceWith(newRatePeriod.html());
    var datepickerSiblings = $(newTable).find('table.room-nights-col2').find('tr');
    if (datepickerSiblings.size() > 0) {
        $.each(datepickerSiblings, function(i, sib) {
                $(sib).remove();
            });
    }
    $('div.rate-period-button').before(newTable);
    $.each($(newTable).find('input.datepicker'), function(i, datepicker) {
                init_datepickers(datepicker);
        });   
}

function removeDateRange(removeElem) {
    var dateRange = $(removeElem).parent().parent('tr');
    var totalDateRanges = $(dateRange).siblings('tr').size();
    var confirmMsg = (totalDateRanges == 1) ? 'Are you sure you would like to delete this entire rate period?' : 'Are you sure you want to delete this date range?';
    var deleteElem = (totalDateRanges == 1) ? $(dateRange).parentsUntil('table.room-night').parent() : dateRange;
    if (confirm(confirmMsg)) {
        deleteElem.remove();
    }
}

function addDateRange(ratePeriodElem) {
    var newRatePeriod = $(datepickerFields);
    $(ratePeriodElem).parent('td').parent('tr').before(newRatePeriod);
    $.each($(newRatePeriod).find('input.datepicker'), function(i, datepicker) {
                init_datepickers(datepicker);
        });
}

function init_datepickers(datepickerInput) {
    var dpElem = (datepickerInput == undefined) ? $('input.datepicker') : $(datepickerInput);
    dpElem.datepicker({showOn: 'both',
                        buttonImage: '/img/cal.png',
                        buttonImageOnly: true,
                        showAnim: 'blind',
                        dateFormat: 'M d yy'
                      }
                ); //end datepicker
}

function closeForm() {
    $('div#formContainer').dialog('close');
    $('div#formContainer').dialog('destroy');
}