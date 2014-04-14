(function ($) {
    jQuery.fn.liveSearch = function (conf) {
        var config = jQuery.extend({
            duration: 0,
            typeDelay: 500,
            loadingId: 'spinner',
            onSlideUp: function () {
            },
            uptadePosition: false
        }, conf);

        var liveSearch = jQuery('#' + config.id);
        var loadingId = jQuery('#' + config.loadingId);
        // Create live-search if it doesn't exist
        if (!liveSearch.length) {
            liveSearch = jQuery('<div id="' + config.id + '"></div>')
                .appendTo(document.body)
                .hide()
                .slideUp(0);

            // Close live-search when clicking outside it
            jQuery(document.body).click(function (event) {
                var clicked = jQuery(event.target);

                if (!(clicked.is('#' + config.id) || clicked.parents('#' + config.id).length || clicked.is('input'))) {
                    liveSearch.slideUp(config.duration, function () {
                        config.onSlideUp();
                    });
                }
            });
        }

        return this.each(function () {
            var input = jQuery(this).attr('autocomplete', 'off');
            var liveSearchPaddingBorderHoriz = parseInt(liveSearch.css('paddingLeft'), 10) + parseInt(liveSearch.css('paddingRight'), 10) + parseInt(liveSearch.css('borderLeftWidth'), 10) + parseInt(liveSearch.css('borderRightWidth'), 10);

            // Re calculates live search's position
            var repositionLiveSearch = function () {
                var tmpOffset = input.offset();

                var inputDim = {
                    left: tmpOffset.left,
                    top: tmpOffset.top,
                    width: input.outerWidth(),
                    height: input.outerHeight()
                };

                if (config.callingId == 'promoEdit') {
                    inputDim.top = 26;
                    inputDim.left = 175;
                }

                inputDim.topPos = inputDim.top + inputDim.height;
                inputDim.totalWidth = inputDim.width - liveSearchPaddingBorderHoriz;

                liveSearch.css({
                    width: (inputDim.totalWidth - 2) + 'px'
                });

                liveSearch.offset({
                    left: inputDim.left,
                    top: inputDim.topPos
                });
            };

            repositionLiveSearch();

            // Shows live-search for this input
            var showLiveSearch = function () {
                liveSearch.show();
            };

            // Hides live-search for this input
            var hideLiveSearch = function () {
                liveSearch.fadeOut(config.duration, function () {
                    config.onSlideUp();
                });
            };

            liveSearch.click(function () {
                setTimeout(hideLiveSearch, 1)
            });

            if (config.placeInput == true) {
                jQuery(".inputplace").live('click', function () {
                    $(this).attr('href', '');
                    if (config.multiSelect != '') {
                        var divs = $(this).parents('div');
                        var idArr = divs[0].id.split('-');
                        var selectedId = idArr[idArr.length - 1];
                        var selectedText = $(this).find('.inputtable').html();
                        $('#' + selectedId).val(selectedText);
                        jQuery('#' + selectedId).change();
                    } else {
                        jQuery(input).val($(this).find('.inputtable').html());
                    }
                    loadingId.hide();
                    return false;
                });
            }

            var xhr;
            var xhrct = 0;

            input
                // On focus, if the live-search is empty, perform an new search
                // If not, just slide it down. Only do this if there's something in the input
                .focus(function () {
                    if (this.value.indexOf("Search") == -1 && this.value !== '') {
                        // Perform a new search if there are no search results
                        if (liveSearch.html() == '') {
                            this.lastValue = '';
                            input.keyup();
                        } else {
                            // HACK: In case search field changes width onfocus
                            setTimeout(showLiveSearch, 1);
                        }
                    } else {
                        this.value = '';
                    }
                })
                // Auto update live-search onkeyup
                .keyup(function () {
                    // Don't update live-search if it's got the same value as last time
                    if (this.value != this.lastValue) {

                        if (this.value == '') {
                            setTimeout(hideLiveSearch, 1)
                            return false;
                        }
                        //input.addClass(config.loadingClass);
                        loadingId.show();

                        var q = this.value;

                        // Stop previous ajax-request
                        if (this.timer) {
                            clearTimeout(this.timer);
                        }

                        if (xhr && xhrct > 0) {
                            xhr.abort();
                            xhrct = 0;
                        }

                        // Start a new ajax-request in X ms
                        this.timer = setTimeout(function () {
                            xhrct++;
                            xhr = jQuery.post(config.url, {query: q}, function (data) {
                                if (data == "") {
                                    setTimeout(hideLiveSearch, 1)
                                }
                                //input.removeClass(config.loadingClass);
                                loadingId.hide();

                                // Show live-search if results and search-term aren't empty
                                if (data.length && q.length) {
                                    liveSearch.html(data);
                                    setTimeout(showLiveSearch, 1);
                                }
                            });
                        }, config.typeDelay);

                        this.lastValue = this.value;
                    }
                });
        });
    };
})(jQuery);
