<?php
uses('view/helpers/Html');
class Html2Helper extends HtmlHelper
{
	/* Formats a number to be a counter, surrounded by parenthesis
	 * @input $num the number to use for the counter
	 * @returns a string with the proper spans and tags
	 */
	function c($num = 0, $prepend = '', $append = '') {
		if(is_array($num))
			$num = count($num);

		$out = '<span class="inline-counter"><span class="p">(</span>';
		if($prepend)
			$out .= $prepend.' ';
			
		$out .= $num;
		
		if($append)
			$out .= ' '.$append;
			
		$out .= '<span class="p">)</span></span>';
		
		return $out;
	}
}
?>