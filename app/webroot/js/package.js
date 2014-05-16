$().ready(function() {

	$('div.edit-link').click(function() {

		if ($(this).attr('href') == undefined) {
			var url = '/clients/'+clientId+'/packages/'+$(this).attr('name')+'/'+packageId;
			if ($(this).attr('qs') != undefined) {
				url += '/?' + $(this).attr('qs');
			}
		} else {
			var url = $(this).attr('href')+'/?overlayForm=1';
		}

		$('iframe#dynamicForm').attr('src', url);
		$('div#formContainer').dialog({modal:true,
			autoOpen:false,
			height:800,
			width:1100,
			title:'Edit'
		});

		$('div#formContainer').dialog('open');

	});
        //show/hide age ranges if family package
        $('#PackageType1').change(function(){

            if ($('#PackageType1').is(':checked')) {
                $('div#familyAgeRanges').show();
            }else{
                $('div#familyAgeRanges').hide();
                //clear age ranges when not family
                $('#ageRangeLow').val('');
                $('#ageRangeHigh').val('');

            }

        });

        $(document).ready(function () {

            if ($('#PackageType1').is(':checked')) {
                $('div#familyAgeRanges').show();
            }
        });

        $('select#sites').change(function() {
               // $('div#familyAgeRanges').toggle();
								// flex packs enabled for fg
                //$('#showFlex').toggle();
                if ($(this).val() == 2 && $('.flexOptions').not(':hidden')) {
                    //$('.flexOptions').hide();

                }
                else if ($(this).val() == 1 && $('#isFlexPackage').is(':checked') && $('.flexOptions').is(':hidden')) {
                    $('.flexOptions').show();
                }
            });
    

        $.each(['input#isFlexPackage', 'input#notFlexPackage'], function(i, item) {
            $(item).bind('click', function() {
                $('tr.flexOptions').toggle();
            });
        });

        //enable the status dropdown
        $('span#overrideStatus').click(function() {
                $('select#status').attr('disabled', false);
                $(this).html('');
            }); //end span#overrideStatus
        
        //autopopulate the minGuests and maxAdults fields for LL
        $('input#maxGuests').blur(function() {
                if ($('input#PackageSitesLuxuryLink').is(':checked')) {
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
        
        $('span#overrideDisclaimer').click(function() {
                $.each(['input#disclaimerDesc', 'input#disclaimerDate'], function(i, inputItem) {
                        $(inputItem).val('');
                    });
            });
        
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
        
        //room type autocomplete
        //$('input#roomTypeName').autocomplete('/packages/getRoomTypes/'+packageId)
        //                       .result(function(event, item) {
        //                            if (item[1] != undefined) {
        //                                var roomTypeId = item[1];
        //                                $('input.roomTypeId').attr('value', roomTypeId);
        //                                $('span.roomTypeReadOnly').html(item[0]);
        //                            }
        //    });

        $('span#create-room-type').click(function() {
                    $.each($('tr.add-room-type').children().find('input'), function(i, item) {
                            $(item).attr('disabled', false);
                        });
                    $('tr.add-room-type').show();
                }
            );
        
        $('input#addRoomLoaItem').click(function() {
                    document.location.href = '/clients/'+clientId+'/packages/edit_room_loa_items/'+packageId;
                }            
            );
        
        $('input.create-inclusion').click(function() {
                var index = $(this).attr('id');
                var numNewInclusions = $('tr.new-inclusion').length;
                $.ajax({type: 'get',
                        url: '/clients/'+clientId+'/packages/render_create_inclusion_form/'+packageId+'?i='+index+'&j='+numNewInclusions,
                        success: function(data) {
                                if ($('tr#create-inclusions-header').is(':hidden')) {
                                    $('tr#create-inclusions-header').show();
                                }
                                $('input.create-inclusion').parent('td').parent('tr').before(data);
                                init_removeInclusions();
                            }
                        });
            });
        
        $('select#new-inclusions-filter').change(function() {
                var selected = $(this).attr('value');
                var inclusionRows = $('table#available-inclusions-options').find('tr');
                if (selected == '0') {
                    $.each(inclusionRows, function(i, row) {
                            if (i % 2 > 0) {
                                $(row).removeClass('odd').addClass('odd');
                            }
                            else {
                                $(row).removeClass('odd');
                            }
                    });
                    inclusionRows.show();
                }
                else {
                    var j = 0;
                    $.each(inclusionRows, function(i, row) {
                            var showClass = 'item-type-'+selected;
                            if ($(row).hasClass(showClass)) {
                                if (j % 2 > 0) {
                                    $(row).removeClass('odd').addClass('odd');
                                }
                                else {
                                    if ($(row).hasClass('odd')) {
                                        $(row).removeClass('odd');
                                    }
                                }
                                $(row).show();
                                j++;
                            }
                            else {
                                $(row).hide();
                            }
                        });
                }
            });
        
        $('input.weekdaysInput[type="checkbox"]').click(function() {
                var weekday = $(this).attr('weekday');
                var clickedElem = this;
                $.each($('input.'+weekday), function(i, item) {
                       if ($(item).attr('name') != $(clickedElem).attr('name')) {
                            if ($(clickedElem).is(':checked') && $(item).is(':checked')) {
                                $(item).removeAttr('checked');
                            }
                            else if ($(clickedElem).is(':checked') && $(item).not(':checked')) {
                                $(item).attr('checked', 'checked');
                            }
                       }
                    });
            });
        
        $.each([$('input#input-numNights-rate1'), $('input#input-numNights-rate2'), $('input.price'), $('input#fee-0'), $('input#fee-1'), $('input#fee-2')], function(i, item) {
                $(item).bind('change', function() {
                        calculateAccommodations(numNights);
                    });
            }
               
        );
        
        $.each([$('input#fee0Label'), $('input#fee1Label'), $('input#fee2Label')], function(i, item) {
                $(item).bind('change', function() {
                        alert($(this).attr('value'));
                        $('td.fee-name-'+i).text($(this).attr('value'));
                    });
            }
        );
        
        //datepicker
        init_datepickers();
        init_rateOptions();
        init_removeInclusions();
        init_numNights();
        init_calcAccommodations();
    }
); //end $().ready

function calculateAccommodations(totalNights) {
    var ratePeriods = $('table.room-night');
    var count = ratePeriods.length;
    var feesElem = $('table#roomNightTaxes_1');
    var fees = new Array();
    for (var i=0; i<3; i++) {
        fees[i] = new Array();
        fees[i]['feeName'] = (feesElem.find('input#fee'+i+'Label').attr('value') == undefined) ? $('td.fee-name-'+i).first().text() : feesElem.find('input#fee'+i+'Label').attr('value');
        fees[i]['feeTypeId'] = (feesElem.find('input#feeTypeId-'+i).attr('value') == undefined) ? feesElem.find('span#feeTypeId-'+i).text() : feesElem.find('input#feeTypeId-'+i).attr('value');
        fees[i]['feeValue'] = (feesElem.find('input#fee-'+i).attr('value') == undefined) ? feesElem.find('span#fee-'+i).text() : feesElem.find('input#fee-'+i).attr('value');
    }
    for (var i=0; i<count; i++) {
        var ratePeriod = ratePeriods[i];
        var total = 0;
        var rateFees = 0;
        var rates = $(ratePeriod).find('tr.rate');
        var rateCount = rates.length;
        for (var j=0; j<=rateCount; j++) {
            var rate = rates[j];
            var price = ($(rate).find('input.price').attr('value') == undefined) ? $(rate).find('span.price').text() : $(rate).find('input.price').attr('value');
            var numNights = ($(rate).find('input.numNights').attr('value') == undefined) ? $(rate).find('span.numNights').text() : $(rate).find('input.numNights').attr('value');
            if ((price != undefined || price != '') && numNights != undefined) {
                total += (price * numNights);
            }
        }
        if (total > 0) {
            for (var k=0; k<3; k++) {
                var fee = fees[k];
                if (fee['feeValue'] != undefined && fee['feeTypeId'] != undefined) {
                    switch (fee['feeTypeId']) {
                        case '1':
                            rateFees += total * (fee['feeValue']/100);
                            break;
                        case '2':
                            rateFees += totalNights * fee['feeValue'];
                            break;
                        default:
                            continue;
                    }
                }
                $(ratePeriod).find('span#fee-'+k).text(fee['feeValue']);
                $(ratePeriod).find('td.fee-name-'+k).text(fee['feeName']);
            }
            total += rateFees;
        }
        $(ratePeriod).find('span.total-accommodations').text(add_commas(total.toFixed(2)));
    }
}


function add_commas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}


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

function addRatePeriodBox() {
    var index = $('table.room-night').length;
    $.ajax({type: 'get',
            url: '/clients/'+clientId+'/packages/render_rate_period/'+packageId+'?i='+index+'&isNewItem=true',
            success: function(data) {
                    $('div.rate-period-button').before(data);
                    populateDefaultValuesForRatePeriod();
            }
        });
    
}

function populateDefaultValuesForRatePeriod() {
    var newElem = $('div.rate-period-button').siblings('table.room-night').last();
    init_rateOptions(newElem);
    init_numNights();
    init_calcAccommodations();
    $.each([$('input#fee0Label'), $('input#fee1Label'), $('input#fee2Label')], function(i, item) {
                $(item).bind('change', function() {
                        $('td.fee-name-'+i).text($(this).attr('value'));
                    });
            }
        );
    var firstElem = $('table.room-night')[0];
    if ($(firstElem).find('tr.rate2').is(':visible')) {
        var rate2 = $(newElem).find('tr.rate2')
        $(rate2).show();
        $(rate2).find('input.weekdaysInput').removeAttr('disabled');
        var firstElemCheckboxes = $(firstElem).find('input.weekdaysInput[type=checkbox]');
        var newElemCheckboxes = $(newElem).find('input.weekdaysInput[type=checkbox]');
        var firstElemCount = firstElemCheckboxes.length;
        for (var i=0; i<firstElemCount; i++) {
            if ($(firstElemCheckboxes[i]).is(':checked')) {
                $(newElemCheckboxes[i]).attr('checked', 'checked');
            }
            else {
                $(newElemCheckboxes[i]).removeAttr('checked');
                $(newElemCheckboxes[i]).next('span.weekday-label').hide();
            }
            $(newElemCheckboxes[i]).hide();
        }
        var firstElemNumNights = $(firstElem).find('input.numNights');
        var countNumNights = firstElemNumNights.length;
        for (var i=0; i<countNumNights; i++) {
            var spanElem = $(newElem).find('span.numNights')[i];
            var inputElem = $(newElem).find('input.numNights')[i];
            $(spanElem).text($(firstElemNumNights[i]).attr('value'));
            $(inputElem).attr('value', ($(firstElemNumNights[i]).attr('value')));
        }
        $(newElem).find('span#rateLabel0').text('Daily Rate 1');
        $(newElem).find('span.numNights-rate2').show();
        $(newElem).find('input#input-numNights-rate2').hide();
        $(newElem).find('span.rateOption').hide();
    }
    for (var i=0; i<3; i++) {
        $(newElem).find('span#fee-'+i).text($(firstElem).find('input#fee-'+i).attr('value'));
    }
}

function removeDateRange(removeElem) {
    var dateRange = $(removeElem).parent().parent('tr');
    var totalDateRanges = $(dateRange).siblings('tr').size();
    var confirmMsg = (totalDateRanges == 1) ? 'Are you sure you would like to delete this entire rate period?' : 'Are you sure you want to delete this date range?';
    var deleteMe = confirm(confirmMsg);
    if (deleteMe) {
        var deleteElem = (totalDateRanges == 1) ? $(dateRange).parentsUntil('table.room-night').parent() : dateRange;
        var formElem = document.createElement('form');
        deleteElem.appendTo($(formElem));
        $.ajax({type: 'post',
                url: '/clients/'+clientId+'/packages/delete_date_range/'+packageId,
                data: $(formElem).serialize(),
                success: function() {
                        deleteElem.remove();
                    }
                }
            );
    }
}

function removeFee(removeElem, index, totalNights) {
    var fee = $(removeElem).parent().parent('tr');
    var deleteMe = confirm('Are you sure you want to delete this fee?');
    if (deleteMe) {
        $(fee).children('td').children('input[type=text]').attr('value', '');
        calculateAccommodations(totalNights);
        var ratePeriods = $('table.room-night');
        var rateCount = ratePeriods.length;
        for(var i=1; i<rateCount; i++) {
            var removeFee = $(ratePeriods[i]).find('tr.fee-'+index);
            $(removeFee).hide();
        }
    }
}

function addDateRange(ratePeriodId, i, index, targetElem) {
    $(targetElem).removeAttr('onclick');
    var dpHtml = $.ajax({type: 'GET',
                       url: '/clients/'+clientId+'/packages/render_datepicker/'+packageId+'?i='+i+'&index='+index+'&ratePeriodId='+ratePeriodId,
                       async: false
                }).responseText;
    $(targetElem).parent().parent().before(dpHtml);
    $.each($(targetElem).parentsUntil('tr.validity').find('input.datepicker'), function(i, datepicker) {
                        init_datepickers(datepicker);
        });
    var nextIndex = index + 1;
    $(targetElem).unbind('click');
    $(targetElem).click(function() { addDateRange(ratePeriodId, i, nextIndex, $(targetElem)) } );
}

function init_datepickers(datepickerInput) {
    var dpElem = (datepickerInput == undefined) ? $('input.datepicker') : $(datepickerInput);
    dpElem.datepicker({showOn: 'both',
                        buttonImage: '/img/cal.png',
                        buttonImageOnly: true,
                        showAnim: 'blind',
                        dateFormat: 'M d yy',
                        onSelect: function(selectedDate) {
                            var nextDatepicker = $(this).nextAll('input.datepicker').first();
                            var option = ($(this).hasClass('startDate') || $(this).hasClass('startdate')) ? 'minDate' : 'maxDate';
                            var instance = $(this).data('datepicker');
                            var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                            $.each($(nextDatepicker), function(i, item) { $(item).datepicker('option', option, date) });
                        }
                      }
                ); //end datepicker
}

function init_rateOptions(ratePeriod) {
    var rpElem = (ratePeriod == undefined) ? $('span.rateOption') : $(ratePeriod).find('span.rateOption');
    $(rpElem).click(function() {
                    if ($(this).text() == '(Switch to Daily Rates)') {
                        $(this).text('(Switch to Rate per Night)');
                        $('tr.weekdaysInput').show();
                        $('table.rate1').children().children().find('input.weekdaysInput[type="checkbox"]').show();
                        $('input.weekdaysInput').attr('disabled', false);
                        $('span.numNights-rate1').hide();
                        $('input#input-numNights-rate1').show();
                        $('span#rateLabel0').html('Daily Rate 1');
                    }
                    else if ($(this).text() == '(Switch to Rate per Night)') {
                        $(this).text('(Switch to Daily Rates)');
                        $('tr.weekdaysInput').hide();
                        $('tr.rate-2').find('input.weekdaysInput').attr('disabled', 'disabled');
                        $('table.rate1').children().children().find('input.weekdaysInput[type="checkbox"]').attr('checked', 'checked').hide();
                        $('table.rate1').children().children().find('input.numNights').attr('value', numNights);
                        $('table.dailyRates').children().children().find('input.weekdaysInput[type="checkbox"]').removeAttr('checked');
                        $('table.dailyRates').children().children().find('input.weekdaysInput[type="text"]').attr('value', '');
                        $('input#input-numNights-rate1').hide();
                        $('span.numNights-rate1').show();
                        $('span.numNights-rate1').text(getNumNights());
                        $('span.numNights-rate2').text('');
                        $('span#rateLabel0').html('Rate per Night');
                    }
                    $('.dailyRates').toggle();
                }
        );
}

function init_numNights() {
    $(['input#input-numNights-rate1', 'input#input-numNights-rate2']).each(function(i, item) {
                    $(item).bind('blur', function() {
                        var numNights = $(item).attr('value');
                        var rateId = $(item).attr('id').split('-')[2];
                        $.each($('span.numNights-'+rateId), function(j, elem) {
                               $(elem).html(numNights);
                           });
                        $.each($('input.numNights-'+rateId), function(j, elem) {
                               $(elem).attr('value', numNights);
                            });
                        });
                    
        });
}

// acarney 2010-12-14
// deprecated: we're allowing users to select the per-night option for any item type
function init_itemTypeDropdown(newInclusion) {
    $('select.new-loa-item-type').change(function() {
                if ($(this).attr('value') == 5) {
                    $(this).parent('td').siblings('td').children('span.new-food-item').show();
                    $(this).parent('td').siblings('td').children('span.total-price-input').hide();
                    $(this).parent('td').siblings('td').children('span.total-price-readonly').show();
                    $(this).parent('td').siblings('td').children('span.total-price-input').children('input').attr('disabled', true);
                    $(this).parent('td').siblings('td').children('span.new-food-item').children('div').children('input').removeAttr('disabled');
                }
                else {
                    if ($(this).parent('td').siblings('td').children('span.new-food-item').is(':visible')) {
                        $(this).parent('td').siblings('td').children('span.new-food-item').hide();
                        $(this).parent('td').siblings('td').children('span.total-price-readonly').hide();
                        $(this).parent('td').siblings('td').children('span.total-price-input').show();
                        $(this).parent('td').siblings('td').children('span.total-price-input').children('input').removeAttr('disabled');
                        $(this).parent('td').siblings('td').children('span.new-food-item').children('div').children('input').attr('disabled', true);
                    }
                }
            });
}

function init_removeInclusions() {
    $('td.remove-inclusion').click(function() {
            var packageLoaItemRelId = $(this).attr('id');
            var deleteMe = confirm('Are you sure that you want to remove this item from this package?');
            if (deleteMe) {
                if (packageLoaItemRelId.length > 0) {
                    $.ajax({type: 'get',
                            url: '/clients/'+clientId+'/packages/delete_inclusion_from_package/'+packageId+'?packageLoaItemRelId='+packageLoaItemRelId,
                            success: function(data) {
									/*	no need to refresh available inclusions
                                        $.ajax({type: 'get',
                                                url: '/clients/'+clientId+'/packages/render_available_inclusions/'+packageId,
                                                success: function(inclusions) {
                                                    $('td#available-inclusions').html(inclusions);
                                                }
                                         });
									*/
                                    }
                            });
                    var inclusionsRows = $(this).parent('tr').nextUntil('tr#create-inclusion-row');
                    $(this).parent('tr').remove();
                }
                else {
                    $(this).parent('tr').remove();
                }
                $.each($(inclusionsRows), function (i, item) {
                        if ($(item).attr('class') == 'odd') {
                            $(item).attr('class', '');
                        }
                        else {
                            $(item).attr('class', 'odd');
                        }
                    });
            }
        });
}

function init_calcAccommodations() {
    $.each([$('input#input-numNights-rate1'), $('input#input-numNights-rate2'), $('input.price'), $('input#fee-0'), $('input#fee-1'), $('input#fee-2')], function(i, item) {
                $(item).bind('change', function() {
                        calculateAccommodations(numNights);
                    });
            }
               
        );
}

function perNightCheckbox(inclusion, itemNumNights) {
    if ($(inclusion).is(':checked')) {
        var price = calculateInclusionPrice(inclusion, itemNumNights, true, itemNumNights);
    }
    else {
        var price = calculateInclusionPrice(inclusion, 1, false, itemNumNights);
    }
    $(inclusion).parents('td.per-night').next('td').children().children('span.total-price').html(price);
}

function newInclusionPrice(priceElem) {
    var isChecked = $(priceElem).parent('div').siblings('div').children('input.inclusion-per-night').is(':checked');
    var multiplier = (isChecked) ? numNights : 1;
    var price = $(priceElem).attr('value');
    var totalPrice = calculateInclusionPrice(priceElem, multiplier, isChecked, numNights, price);
    $(priceElem).parents('td.per-night').next('td').children().children('span.total-price').html(totalPrice);
}

function calculateInclusionPrice(inclusionElem, multiplier, isChecked, itemNumNights, newPrice) {
    var price = (newPrice == undefined) ? $(inclusionElem).parents('td.per-night').next('td').children().children('span.total-price').html() : newPrice;
    $(inclusionElem).parent('div').siblings('div').children('span.per-night-multiplier').html(' x '+multiplier);
    var totalPrice = (isChecked) ? price * multiplier : (newPrice == undefined) ? price / itemNumNights : newPrice;
    return totalPrice;
}

function editThis() {
	var prompt_da_user = confirm('Are you sure you want to make changes? This will prevent the auto-updating of validity disclaimer for all price points for this package.');
	if (prompt_da_user) {
		$('#validity-disclaimer').removeAttr('readonly');
		$('#edit-this-validity-disclaimer').val(1);
	} else {
		return false;
	}
}

function updateRetail(autoFillPercentRetail, autoFillSuggestedFlexPrice, numNights, currencyCode, isMultiClientPackage, isFlexPackage, pricePointName) {
    highestRetail = 0;
    defaultPercent = 0;
    var highestFlex = 0;
	var checkedIds = '';
    var nameRatePeriod = '';

    if ( !$('#name-rate-period').val() ) {
         nameRatePeriod = pricePointName;
    }

    $('.check-rate-period:checked').each(function() {
        if (isMultiClientPackage) {
            highestRetail += retails[$(this).val()];
            defaultPercent = guaranteedPercents[$(this).val()];
        }
        else if (retails[$(this).val()] > highestRetail) {
            highestRetail = retails[$(this).val()];
            defaultPercent = guaranteedPercents[$(this).val()];
        }
        if (isFlexPackage) {
            if (flexRoomPricePerNight[$(this).val()] > highestFlex) {
                highestFlex = flexRoomPricePerNight[$(this).val()];
            }
        }
		checkedIds += ',' + $(this).val();
        nameRatePeriod = ratePeriodDates[$(this).val()] + ';';
    });
    $('#name-rate-period').val($('#name-rate-period').val() + nameRatePeriod);;
    $('#retail').html(highestRetail);
    if (isFlexPackage) {
        var pricePerNight = 0;
        var inclusionTotal = 0;
        $('#flexDefaultRetailPrice').html(highestFlex);
        if ($('#flexSuggestedRetail').val() == 0) { 
            $('span.total-price').each(function(i, item) {
                    inclusionTotal += parseInt($(item).text());
                });            
            $('#flexSuggestedRetail').val(highestFlex + inclusionTotal);
        }
        $('span#suggestedFlexCalc').html($('#flexSuggestedRetail').val());
        // $('span#suggestedFlexCalcDNG').html($('#flexSuggestedRetail').val());
        if ($('#buynow-percent').val() > 0) {
            var calcPercent = $('#buynow-percent').val();
        }
        else {
            var calcPercent = defaultPercent;
        }
        $('span#suggestedFlexPrice').html(Math.round($('#flexSuggestedRetail').val() * (calcPercent / 100)));
        
        //if ($('#auction-percent').val() > 0) {
        //    var calcPercentDNG = $('#auction-percent').val();
        //}
        //else {
        //    var calcPercentDNG = defaultPercent;
        //}
        //$('span#suggestedFlexPriceDNG').html(Math.round($('#flexSuggestedRetail').val() * (calcPercentDNG / 100)));        
        
    }
    if (($('input#pricePointId').val() == undefined) && autoFillPercentRetail) {
        $('#auction-percent').val(defaultPercent);
    }
    $('#guaranteed-percent').val(defaultPercent);
    $('#auction-retail').val(Math.round($('#auction-percent').val() * highestRetail / 100));
    $('#auction-us-retail').val(Math.round($('#auction-percent').val() * highestRetail / 100 * conversionRate));
    $('#buynow-retail').val(Math.round($('#buynow-percent').val() * highestRetail / 100));
    $('#buynow-us-retail').val(Math.round($('#buynow-percent').val() * highestRetail / 100 * conversionRate));    
    $('#retail-value').val(highestRetail);
    if (currencyCode != 'USD') {
        $('#retail-usd').html('= ' + (highestRetail * conversionRate) + ' USD');
    }	
	if (checkedIds && !($('#edit-this-validity-disclaimer').val() == 1)) {
		updateValidityDisclaimer(checkedIds);
	}
	//updatePerNightPrice(true);
}

function updateValidityDisclaimer(ids) {
	if (!ids) {
		return false;
	}
	$.ajax({
		url: '/clients/' + clientId + '/packages/ajaxGetPricePointValidityDisclaimer/' + packageId + '/?ids=' + ids,
		success: function(data) {
			$('#validity-disclaimer').html(data);
		}
	});
}

function updatePerNightPrice(autoFillFlexPerNightPrice) {
    $('span#suggestedFlexCalc').html($('#flexSuggestedRetail').val());
    // $('span#suggestedFlexCalcDNG').html($('#flexSuggestedRetail').val());
    
    var buynowPrice = Math.round($('#flexSuggestedRetail').val() * ($('#buynow-percent').val() / 100));
    $('span#suggestedFlexPrice').html(buynowPrice);
	$('input#flexPricePerNight').val(buynowPrice);

    //var auctionPrice = Math.round($('#flexSuggestedRetail').val() * ($('#auction-percent').val() / 100));
    //$('span#suggestedFlexPriceDNG').html(auctionPrice);
	//$('input#flexPricePerNightDNG').val(auctionPrice);

}

function closeForm(anchor) {
    $('div#formContainer').dialog('close');
    $('div#formContainer').dialog('destroy');
    var baseLocation = window.location.href.split('#')[0];
    window.location.href = baseLocation+'#'+anchor;
    window.location.reload(true);
}

function submitForm(thisId) {

	// add/edit a package packages/edit_package
	// check that numGuests is appropriate for the number of rooms

	if ($('#numRooms').val()==0){
		alert("You must set a 'Num Rooms' value greater than 0");
		return false;
	}else if ($('#maxGuests').val()>4 && $("#numRooms").val()==1){
		if (false==confirm("You have selected more than 4 guests but only 1 room. To proceed, click OK or click cancel to fix things.")){
			return false;
		}
	}

 $("input[type='button']").attr("disabled", true).val("Please wait...");


  $.post(window.location.href,
		$('#'+thisId).serialize(),
			function(data) {

				if (data == 'ok') {
					parent.closeForm(thisId);
				} else {

					$("input[type='button']").attr("disabled", false).val("Save Changes");

					var errors = $.parseJSON(data);
					var errorStr = '';
					$.each(errors, function(i, error) {
						errorStr += '<li>' + error + '</li>';
					});
					$('div#errors').html(errorStr);
					$('div#errorsContainer').show();
					$('div#errors_repeat').html(errorStr);
					$('div#errorsContainer_repeat').show();
				}
		});
}

