// To use this script add the floatingheader class to a table and then call jQuery.tableheader() somewhere on the page.
function UpdateTableHeaders() {
	jQuery("table.floatingheader").each(function() {
		var originalHeaderRow = jQuery(".tableFloatingHeaderOriginal", this);
		var floatingHeaderRow = jQuery(".tableFloatingHeader", this);
		var offset = jQuery(this).offset();
		var scrollTop = jQuery(window).scrollTop();
		if((scrollTop > offset.top) && (scrollTop < offset.top + jQuery(this).height())) {
			floatingHeaderRow.css("visibility", "visible");
			floatingHeaderRow.css("top", Math.min(scrollTop - offset.top, jQuery(this).height() - floatingHeaderRow.height()) + "px");

			// Copy cell widths from original header
			jQuery("th", floatingHeaderRow).each(function(index) {
				var cellWidth = jQuery("th", originalHeaderRow).eq(index).css('width');
				jQuery(this).css('width', cellWidth);
			});
			// Copy row width from whole table
			floatingHeaderRow.css("width", jQuery(this).css("width"));
		} else {
			floatingHeaderRow.css("visibility", "hidden");
			floatingHeaderRow.css("top", "0px");
		}
	});
}(function($) {
	$.fn.tableheader = function() {
		jQuery(document).ready(function() {
			jQuery("table.floatingheader").each(function() {
				jQuery(this).wrap("<div class=\"divTableWithFloatingHeader\" style=\"position:relative\"></div>");
				var originalHeaderRow = jQuery("tr:first", this)
				originalHeaderRow.before(originalHeaderRow.clone());
				var clonedHeaderRow = jQuery("tr:first", this)
				clonedHeaderRow.addClass("tableFloatingHeader");
				clonedHeaderRow.css("position", "absolute");
				clonedHeaderRow.css("top", "0px");
				clonedHeaderRow.css("left", jQuery(this).css("margin-left"));
				clonedHeaderRow.css("visibility", "hidden");
				originalHeaderRow.addClass("tableFloatingHeaderOriginal");
			});
			UpdateTableHeaders();
			jQuery(window).scroll(UpdateTableHeaders);
			jQuery(window).resize(UpdateTableHeaders);
		});
	};
})(jQuery);
