var cd = {
	codes : Array,
	init : function() {
		cd.codes = $$('.handle');
		
		cd.contentDivs = $$('.collapsibleContent');
		
		//grab and close all collapsible divs in fieldsets
		cd.fieldsetHandles = $$('fieldset .handle');
		cd.fieldsetCollapsibleContent = $$('fieldset .collapsibleContent');
		
		for(var i = 0; i < cd.fieldsetCollapsibleContent.length; i++) {
			//only collapse if no error message is contained within
			if (!$(cd.fieldsetCollapsibleContent[i]).hasClassName('disableAutoCollapse') &&
				!$(cd.fieldsetCollapsibleContent[i]).down('.error-message')) {
				cd.fieldsetCollapsibleContent[i].hide();
				Element.addClassName(cd.fieldsetCollapsibleContent[i],'closed');
			}
		}
		
		for(var i = 0; i < cd.fieldsetHandles.length; i++) {
			//only collapse if no error message is contained within
			if($(cd.fieldsetHandles[i]).next('.collapsibleContent') &&
				!$(cd.fieldsetHandles[i]).next('.collapsibleContent').hasClassName('disableAutoCollapse') &&
				!$(cd.fieldsetHandles[i]).next('.collapsibleContent').down('.error-message')) {
				Element.removeClassName(cd.fieldsetHandles[i].up(), 'collapsible');
				Element.addClassName(cd.fieldsetHandles[i].up(), 'collapsible-closed');
			}
		}
		
		cd.attach();
	},
	attach : function() {
		var i;
		for ( i=0;i<cd.codes.length;i++ ) {
			Event.observe(cd.codes[i],'click',cd.collapse,false);
			Element.cleanWhitespace(cd.codes[i].parentNode);
		}
	},
	getEventSrc : function (e) {
		if (!e) e = window.event;
		if (e.originalTarget)
			return e.originalTarget;
		else if (e.srcElement)
		return e.srcElement;
	},
	collapse : function(e) {
		var el = cd.getEventSrc(e).next('div.collapsibleContent');

		if (el && Element.hasClassName(el,'closed') ) {
			new Effect.Parallel(
				[
					new Effect.BlindDown(el,{sync:true}),
					//new Effect.Appear(el,{sync:true})
				],
				{
					duration:0.5,
					fps:40
				}
			);
			Element.removeClassName(cd.getEventSrc(e).up(), 'collapsible-closed');
			Element.addClassName(cd.getEventSrc(e).up(), 'collapsible');
			Element.removeClassName(el,'closed');
		} else if(el) {
			new Effect.Parallel(
				[
					new Effect.BlindUp(el,{sync:true}),
					//new Effect.Fade(el,{sync:true})
				],
				{
					duration:0.5,
					fps:40
				}
			);
			Element.removeClassName(cd.getEventSrc(e).up(), 'collapsible');
			Element.addClassName(cd.getEventSrc(e).up(), 'collapsible-closed');
			Element.addClassName(el,'closed')
		}
	}
};
Event.observe(window,'load',cd.init,false);