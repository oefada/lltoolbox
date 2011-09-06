<?php
//Check whether to render a search bar or not, depending on whether this controller has a search method
$controller = false;
if(isset($this->viewVars['searchController'])) {
	$controllerName = Inflector::pluralize($this->viewVars['searchController']);
} else {
	$controllerName = $this->params['controller'];
}
$controllerName = Inflector::camelize($controllerName);	//make sure the controller name is always in the right format
$controllerUrl = Inflector::underscore($controllerName);
$fullControllerName = $controllerName.'Controller';

if(!isset($this->viewVars['searchController']) &&class_exists($fullControllerName)) {					//just in case the controller doesn't exist
	$controller = new $fullControllerName;
}

if (@$this->viewVars['searchController'] || method_exists($controller , 'search') || isset($this->searchController)):
 ?>
<div id='search-bar' class="clearfix">
<div id='search-bar-inner' class="clearfix">
<?php $defSearchValue = "Search {$controllerName}"; ?>
<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get" action="/<?= $controllerUrl ?>/search">
	<div class="clearfix">
		<?php
		/*
		<div class="search-input-with-livesearch" on>
			
			<input id="query" autocomplete='off' maxlength="2147483647" name="query" type="text" value="<?=$defSearchValue?>" onfocus="if($F(this) == '<?=$defSearchValue?>') { $(this).value = '';} else { $('livesearch').show(); }" onblur="Element.hide.delay(0.2, 'livesearch'); if($F(this) == '') { $(this).value = '<?=$defSearchValue?>' }" />
		</div>
		 */
		?>
		<div class="search-input-with-livesearch" on>
		<label>
			<input autocomplete='off' maxlength="2147483647" name="query" type="text" value="<?=$defSearchValue?>" />
			<div id="search-input-with-livesearch" class="auto_complete"><!-- Results will load here --></div>
		</label>
		<input type="submit" value="Search" />
		</div>
	</div>
</form>
<script>
(function($) { 
	jQuery.fn.liveSearch = function (conf) {
	    var config = jQuery.extend({
	        id:                'search-input-with-livesearch', 
	        duration:        0, 
	        typeDelay:        10,
	        loadingId:    'spinner', 
	        onSlideUp:        function () {}, 
	        uptadePosition:    false
	    }, conf);
	
	    var liveSearch    = jQuery('#' + config.id);
		var loadingId  = jQuery('#' + config.loadingId);
	    // Create live-search if it doesn't exist
	    if (!liveSearch.length) {
	        liveSearch = jQuery('<div id="' + config.id + '"></div>')
	                        .appendTo(document.body)
	                        .hide()
	                        .slideUp(0);
	
	        // Close live-search when clicking outside it
	        jQuery(document.body).click(function(event) {
	            var clicked = jQuery(event.target);
	
	            if (!(clicked.is('#' + config.id) || clicked.parents('#' + config.id).length || clicked.is('input'))) {
	                liveSearch.slideUp(config.duration, function () {
	                    config.onSlideUp();
	                });
	            }
	        });
	    }

	    return this.each(function () {
	        var input                            = jQuery(this).attr('autocomplete', 'off');
	        var liveSearchPaddingBorderHoriz    = parseInt(liveSearch.css('paddingLeft'), 10) + parseInt(liveSearch.css('paddingRight'), 10) + parseInt(liveSearch.css('borderLeftWidth'), 10) + parseInt(liveSearch.css('borderRightWidth'), 10);
	
	        // Re calculates live search's position
	        var repositionLiveSearch = function () {
	            var tmpOffset    = input.offset();
	            var inputDim    = {
	                left:        tmpOffset.left, 
	                top:        tmpOffset.top, 
	                width:        input.outerWidth(), 
	                height:        input.outerHeight()
	            };
	
	            inputDim.topPos        = inputDim.top + inputDim.height;
	            inputDim.totalWidth    = inputDim.width - liveSearchPaddingBorderHoriz;
	
	            liveSearch.css({
	                position:    'absolute', 
	                left:        inputDim.left + 'px', 
	                top:        inputDim.topPos + 'px',
	                width:        inputDim.totalWidth + 'px'
	            });
	        };
	
	        // Shows live-search for this input
	        var showLiveSearch = function () {
	            // Always reposition the live-search every time it is shown
	            // in case user has resized browser-window or zoomed in or whatever
	            //repositionLiveSearch();
	
	            // We need to bind a resize-event every time live search is shown
	            // so it resizes based on the correct input element
	            //jQuery(window).unbind('resize', repositionLiveSearch);
	            //jQuery(window).bind('resize', repositionLiveSearch);
	
	            liveSearch.show();
	        };
	
	        // Hides live-search for this input
	        var hideLiveSearch = function () {
	            liveSearch.fadeOut(config.duration, function () {
	                config.onSlideUp();
	            });
	        };
			
			liveSearch.click(function() {
				setTimeout(hideLiveSearch,1)
				loadingId.show();
			});
			
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
	                    //input.addClass(config.loadingClass);
						loadingId.show();
						
	                    var q = this.value;
	
	                    // Stop previous ajax-request
	                    if (this.timer) {
	                        clearTimeout(this.timer);
	                    }
	                    
	                    if (xhr && xhrct > 1) {
	                    	xhr.abort();
	                    	xhrct = 0;
	                    }
	
	                    // Start a new ajax-request in X ms
	                    this.timer = setTimeout(function () {
	                    	xhrct++;
	                        xhr = jQuery.post(config.url, {query: q}, function (data) {
	                        	if (data == "") {
	                        		setTimeout(hideLiveSearch,1)
	                        	}
	                            //input.removeClass(config.loadingClass);
	                            loadingId.hide();
	
	                            // Show live-search if results and search-term aren't empty
	                            if (data.length && q.length) {
	                                liveSearch.html(data);
	                                showLiveSearch();
	                            }
	                        });
	                    }, config.typeDelay);
	
	                    this.lastValue = this.value;
	                }
	            });
	    });
	};
})(jQuery);
</script>
<script>jQuery('.search-input-with-livesearch input[name="query"]').liveSearch({url: "/ajax_search?searchtype=<?= $controllerUrl ?>"});</script>
</div>
</div>
<?php endif; //end method exists check ?>
