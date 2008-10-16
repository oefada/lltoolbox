var cd = {
	codes : Array,
	init : function() {
		cd.codes = $$('.collapsible');
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
		var el = cd.getEventSrc(e).nextSibling;
		if ( Element.hasClassName(el,'closed') ) {
			new Effect.Parallel(
				[
					new Effect.SlideDown(el,{sync:true}),
					new Effect.Appear(el,{sync:true})
				],
				{
					duration:1.0,
					fps:40
				}
			);
			Element.removeClassName(cd.getEventSrc(e), 'collapsible-closed');
			Element.removeClassName(el,'closed');
		} else {
			new Effect.Parallel(
				[
					new Effect.SlideUp(el,{sync:true}),
					new Effect.Fade(el,{sync:true})
				],
				{
					duration:1.0,
					fps:40
				}
			);
			Element.addClassName(cd.getEventSrc(e), 'collapsible-closed');
			Element.addClassName(el,'closed')
		}
	}
};
Event.observe(window,'load',cd.init,false);