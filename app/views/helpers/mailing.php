<?

/**
 * Wrap up the html for a single deal
 * 
 * 
 * 
 */
class MailingHelper extends AppHelper{

	public function __construct(){
		parent::__construct();

		$this->viewVars = ClassRegistry::getObject('view')->viewVars;

		if (isset($this->viewVars['utmArr'])){
			$this->utmArr=$this->viewVars['utmArr'];
			$this->url=$this->viewVars['url'];

			$this->utm_qs='?utm_medium='.$this->utmArr['utm_medium'];
			$this->utm_qs.='&utm_source='.$this->utmArr['utm_source'];
			$this->utm_qs.='&utm_campaign='.$this->utmArr['utm_campaign'];
		}

	}

	/**
	 * Wrap the standard deal display in html for a client
	 * 
	 * @return html
	 */
	public function genDealHtml($clientArr, $utm_content='', $utm_term=''){

		$h='<table align="center" width="240" height="201"  border"1" cellpadding="0" cellspacing="0">
		<tr><!--top spacer-->
		<td colspan="2">
		<img src="http://www.luxurylink.com/images/insider/new/SPACER_240x1.jpg" width="240" height="10" alt=""/></td>
		</tr>

		<tr><!--image-->
		<td colspan="2" width="240" height="126" bgcolor="#ffffff" style="padding: 10px 0px 0px 0px;">
		<a 
			xt="SPCLICK" 
			name="www_luxurylink_com_fivest_6" 
			target="_blank" 
			href="'.$this->genPropUrl($clientArr, $utm_content, $utm_term).'"
		><img 
			name="Cont_26" 
			style="display:block;" 
			width="240" 
			height="134" 
			border="0" 
			src="http://photos.luxurylink.us'.$clientArr['imagePath'].'"
		/></a>
		</td>
		</tr>
		<tr><!--savings-->
		<td 
			width="107" 
			height="34" 
			bgcolor="#d9d6b7" 
			border="2" 
			valign="middle" 
			style="background-color: #d9d6b7; border: 1px solid #e2e2e2;"
		>

			<table id="savigs" align="center" width="100"  border="0" cellpadding="0" cellspacing="0">
			<tr><!--savings-->
			<td 
				width="45" 
				height="34" 
				color="#444444" 
				valign="middle" 
				style="font-family: Georgia,Garamond, Times New Roman, serif; text-align: right; font-size: 22px; color: #444444;"
			>';
			if (isset($clientArr['offers'])){
				if (isset($clientArr['offers'][0]['flexPercentOff'])){
					$h.=$clientArr['offers'][0]['flexPercentOff'];
				}else{
					$h.=$clientArr['offers'][0]['percentOff'];
				}
			}else{
				$h.='<span style="font-size:10px;">TODO</span>';
			}
			$h.='<span style="font-size:14px; vertical-align:middle;">%</span>
			</td>
			<td 
				width="55" 
				height="34" 
				valign="middle" 
				style="font-family: Arial Black, Helvetica, sans-serif; text-align: left; font-size: 9px; text-transform: uppercase; color: #444444;"
			>&nbsp;Savings
			</td>
			</tr>
			</table>

		</td>
		<!--extras-->
		<td 
			width="130" 
			height="34" 	
			bgcolor="#ffffff" 
			valign="top" 
			style="background-color: #ffffff; border-top: 1px solid #e2e2e2; border-right: 1px solid #e2e2e2; border-bottom: 1px solid #e2e2e2;"
		>
		<table align="center" width="120"  valign="top" border="0" cellpadding="0" cellspacing="0">
		<tr><!--savings-->
		<td 
			width="58" 
			height="34" 	
			color="#8f9259" 
			valign="top" 
			style="font-family:Georgia, Garamond, Times New Roman, serif; text-align: right; font-size: 22px; color: #8f9259;"
			><span style="font-size:14px; line-height: 12px;">$</span>&nbsp;';

		$h.='</td>
		<td 
			width="70" 
			height="34" 
			valign="middle" 
			style="font-family: Arial, Helvetica, sans-serif; text-align: left; font-size: 9px; color: #8f9259;"
		><strong>EDIT Extras</strong>
		</td>
		</tr>
		</table>

		</td>
		</tr>

		<tr>
		<td colspan="2"><!--spacer-->
		<img src="http://www.luxurylink.com/images/insider/new/SPACER_240x1.jpg" width="240" height="17" alt=""/>
		</td>

		<tr><!--prop-name-->
		<td 
			colspan="2" 
			width="240" 
			bgcolor="#ffffff" 
			valign="top" 
			style="font-size:17px; line-height:17px;font-family: Arial, helvetica, Sans-Serif; color:#3ca9d5; text-align: left; background-color: #ffffff; padding:0px 0px 0px 0px; width: 180px"
		><a 
			style=" text-decoration: none; color: #3ca9d5" 
			href="'.$this->genPropUrl($clientArr).'"
			name="'.$clientArr['seoName'].'" 
			target="_blank" 
			xt="SPCLICKSTREAM"
		><strong>'.$clientArr['name'].'</strong></a>
		</td>
		</tr>

		<!--prop-location-->
		<tr>
		<td 
			colspan="2" 
			width="240" 
			height="15" 
			bgcolor="#ffffff" 
			valign="middle" 
			style="font-size:13px; line-height:18px; font-family: Verdana, Arial, helvetica, Sans-Serif; color:#666666; text-align: left; background-color: #ffffff; padding:0px 0px 0px 0px; width: 180px"
		>'.$clientArr['locationDisplay'].'
		</td>
		</tr>
		<tr>
		<td colspan="2"><!--spacer-->
		<img src="http://www.luxurylink.com/images/insider/new/SPACER_240x1.jpg" width="240" height="4" alt=""/>
		</td>
		</tr>
		</table>';

		return $h;

	}

	public function genViewDetailsCell($clientArr, $utm_term='', $utm_content=''){

		$h='<td 
			colspan="2" 
			width="240" 
			bgcolor="#ffffff" 
			valign="bottom" 
			style="font-size:11px; font-family: Arial, helvetica, Sans-Serif; color:#3ca9d5; line-height:13px; text-align: left; background-color: #ffffff; padding:0px 0px 0px 0px; width: 180px"
		><a 
			style="text-decoration: none; color: #3ca9d5" 
			href="'.$this->genPropUrl($clientArr, $utm_content, $utm_term).'" 
			name="'.$clientArr['seoName'].'" 
			target="_blank" 
			xt="SPCLICKSTREAM"><img 
				src="http://www.luxurylink.com/images/insider/new/view_btn.jpg" 
				width="100" 
				height="28" 
				border="0"/>
		</a>
		</td>';

		return $h;

	}

	public function genPropUrl($clientArr, $utm_content='', $utm_term=''){

		//$url=$clientArr['seoUrl'];
		// link to listing page as landing page
		$url="http://www.luxurylink.com/fivestar/hotel-deals/".str_replace(" ","_",$clientArr['country']);
		$utm_content=($utm_content!='')?$utm_content:$clientArr['seoName'];
		$qs=$this->utm_qs.'&utm_content='.$utm_content;
		if ($utm_term!=''){
			$qs.='&utm_term='.$utm_term;
		}else{
			$qs.='&utm_term='.rawurlencode($clientArr['name']);
		}
		$qs.="&featuredproperty=1&tmsg=2";

		return strtolower($url.$qs);

	}

	/**
	 * Wrap the "Newest Additions" deal in html 
	 * 
	 * @return html
	 */
	public function genNewestAdditionsHtml($clientArr, $utm_content='', $utm_term=''){

		$url=$this->genPropUrl($clientArr, $utm_content, $utm_term);

		$name=$clientArr['name'];
		$locationDisplay=$clientArr['locationDisplay'];
		$imgsrc='http://photos.luxurylink.us'.$clientArr['imagePath'];

		$h='<table align="center" width="240" height="219" border="0" cellpadding="0" cellspacing="0">';
		$h.='<tr><!--top spacer-->';
		$h.='<td colspan="2">';
		$h.='<img src="http://www.luxurylink.com/images/insider/new/SPACER_240x1.jpg" width="240" height="18"/>';
		$h.='</td>';
		$h.='</tr>';

		$h.='<tr><!--image-->';
		$h.='<td colspan="2" width="240" height="126" bgcolor="#ffffff">';
		$h.='<a 
				xt="SPCLICK" 
				name="www_luxurylink_com_fivest_6" 
				target="_blank" 
				href="'.$url.'"
			><img 
				style="display:block;" 
				width="240" 
				height="134" 
				border="0" 
				src="'.$imgsrc.'"
			/></a>';
		$h.='</td>';
		$h.='</tr>';

		$h.='<tr>
			<td colspan="2"><!--spacer-->
			<img 
				src="http://www.luxurylink.com/images/insider/new/SPACER_240x1.jpg" 
				width="240" 
				height="17" 
			/>
			</td>
		</tr>';

		$h.='<tr><!--prop-name-->
			<td 
				colspan="2" 
				width="240" 
				bgcolor="#ffffff" 
				valign="top" 
				style="font-size:17px; line-height:17px;font-family: Arial, helvetica, Sans-Serif; color:#3ca9d5;  text-align: left; background-color: #ffffff; padding:0px 0px 0px 0px; width: 180px">
				<a 
					style="text-decoration: none; color: #3ca9d5" 
					href="'.$url.'"
					name="'.$utm_term.'" 
					target="_blank" 
					xt="SPCLICKSTREAM"
				><strong>'.$name.'</strong></a>
		</td>
		</tr>';

		$h.='<!--prop-location-->
		<tr>
			<td 
				colspan="2" 
				width="240" 
				height="15" 
				bgcolor="#ffffff" 
				valign="middle" 
				style="font-size:13px; line-height:18px; font-family: Verdana, Arial, helvetica, Sans-Serif; color:#666666; text-align: left; background-color: #ffffff; padding:0px 0px 0px 0px; width: 180px"
			>'.$locationDisplay.'
		</td>
		</tr>';

		$h.='<tr>
		<td colspan="2"><!--spacer-->
			<img src="http://www.luxurylink.com/images/insider/new/SPACER_240x1.jpg" width="240" height="17" alt=""/>
		</td>
		</tr>

		</table>';

		return $h;

	}

	/**
	 * Format url
	 * 
	 * @return url
	 */
	public function genUrl($utm_content='', $dir='', $utm_term='', $paramsArr=''){

		$url=$this->url;
		if ($dir!=''){
			$url.=$dir;
		}
		$qs=$this->utm_qs;
		if (is_array($paramsArr) && count($paramsArr)>0){
			if ($qs==''){
				$qs='?';
			}
			foreach($paramsArr as $key=>$val){
				$qs.=urlencode($key)."=".urlencode($val)."&";
			}
		}
		$url.=$qs;
		$url.='&utm_content='.$utm_content;

		return $url;

	}


}
