var cd = {
	codes : Array,
	init : function() {
		cd.codes = $$('.handle');
		
		cd.contentDivs = $$('.collapsibleContent');
		
		//grab and close all collapsible divs in fieldsets
		cd.fieldsetHandles = $$('.handle');
		cd.fieldsetCollapsibleContent = $$('.collapsibleContent');
		
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
					new Effect.Appear(el,{sync:true})
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
					new Effect.Fade(el,{sync:true})
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

function fancyShow(idOfObject) {
	new Effect.Parallel(
		[
			new Effect.BlindDown($(idOfObject),{sync:true}),
			new Effect.Appear($(idOfObject),{sync:true})
		],
		{
			duration:0.5
		}
	);
}

function fancyHide(idOfObject) {
	new Effect.Parallel(
		[
			new Effect.BlindUp($(idOfObject),{sync:true}),
			new Effect.Fade($(idOfObject),{sync:true})
		],
		{
			duration:0.5
		}
	);
}

function tree(el) {
 var fullyOpen = false;
 if (el.className.indexOf('fully-open') != -1) {
  fullyOpen = true;
 }
 el.className = 'treed';
 var lis = el.getElementsByTagName('li');
 for (var i = 0; i < lis.length; i++) {
  var uls = lis[i].getElementsByTagName('ul');
  if (uls.length > 0) {
   if (lis[i].className == 'open' || fullyOpen) {
    uncollapse(lis[i]);
   } else {
    collapse(lis[i]);
   }
   lis[i].onmousedown = function(e) {
    this.className = (this.className.substring(0, 4) == 'last' ? (this.className == 'lastopen' ? 'lastcollapsed' : 'lastopen') : (this.className == 'open' ? 'collapsed' : 'open'));
    clicker(e);
   }
  } else {
   lis[i].className = 'file';
  }
  if (!nextElement(lis[i])) {
   lis[i].className = 'last' + lis[i].className;
  }
 }
 var as = el.getElementsByTagName('a');
 for (var i = 0; i < as.length; i++) {
  as[i].onmousedown = function(e) {
   clicker(e);
  }
 }
}
function collapse(li) {
 li.className = 'collapsed';
}
function uncollapse(li) {
 li.className = 'open';
}
function nextElement (node) {
 try {
  do {
   node = node.nextSibling;
  } while (node.nodeType != 1);
  return node;
 } catch (e) {
  return false;
 }
}
function clicker(e) {
 if (!e) var e = window.event;
 e.cancelBubble = true;
 if (e.stopPropagation) e.stopPropagation();
}
function initTree() {
 if (document.getElementById && document.getElementsByTagName) {
  var uls = document.getElementsByTagName('ul');
  for (var i = 0; i < uls.length; i++) {
   if (uls[i].className.indexOf('tree') != -1) {
    tree(uls[i]);
   }
  }
 }
}
Event.observe(window,'load',initTree,false);