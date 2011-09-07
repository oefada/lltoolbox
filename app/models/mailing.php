<?php
class Mailing extends AppModel {

	var $name = 'Mailing';
	var $useTable = 'mailing';
	var $primaryKey = 'mailingId';
    
    var $validate = array('mailingTypeId' => array('rule1' => array('rule' => 'numeric'),
                                                   'rule2' => array('rule' => 'notEmpty')),
                          'mailingDate' => array('rule1' => array('rule' => 'validateDayOfWeek',
                                                                  'message' => 'The date you have selected is invalid for this mailing type'),
                                                 'rule2' => array('rule' => 'validateNoDuplicates',
                                                                  'message' => 'A mailing already exists for this date and mailing type.'))
                          );
    
    var $belongsTo = array('MailingType' => array('className' => 'MailingType', 'foreignKey' => 'mailingTypeId'));
    var $hasMany = array('MailingPackageSectionRel' => array('className' => 'MailingPackageSectionRel', 'foreignKey' => 'mailingId'),
                         'MailingAdvertising' => array('className' => 'MailingAdvertising', 'foreignKey' => 'mailingId')
    );

		function generator($clientId_arr){

			foreach($clientId_arr as $key=>$id)if ($id!='')$arr[$key]=$id;
			$q="SELECT * FROM client, offerLuxuryLink WHERE client.clientId IN (".implode(",",$arr).")";
			$q.=" AND client.clientId=offerLuxuryLink.clientId ";
			$q.="AND isClosed=0 ";
			$q.="GROUP BY client.clientId";
			$rows=$this->query($q);
//print "<pre>"; 
//print_r($clientId_arr); 
//print_r($arr);	

			// maintain the order the ids were submitted in
			$new_rows=array();
			foreach($arr as $key=>$id){
				foreach($rows as $index=>$row){
					if ($row['client']['clientId']==$id){
						$new_rows[$key]=$row;
					}
				}
			}

			$q="SELECT imagePath,clientId FROM imageClient INNER JOIN image USING(imageId) ";
			$q.="WHERE clientId IN (".implode(", ",$clientId_arr).") ";
			$q.="AND imageTypeId=2 ";
			$q.="AND inactive = 0 ";
			$q.="ORDER BY sortOrder";
			$image_rows=$this->query($q);

			foreach($image_rows as $key=>$row){
				foreach($new_rows as $key=>$new_row){
					if ($row['imageClient']['clientId']==$new_row['client']['clientId']){
						$new_rows[$key]['client']['imagePath']=str_replace("gal-lrg","gal-xl",$row['image']['imagePath']);
					}
				}
			}

//print "<pre>"; print_r($image_rows);exit;


			$q="SELECT longDesc,clientId FROM clientSiteExtended WHERE clientId IN (".implode(",",$arr).")";
			$q.=" AND siteId=1";
			$longDesc_arr=$this->query($q);
			foreach($longDesc_arr as $key=>$row){
				$clientId=$row['clientSiteExtended']['clientId'];
				foreach($new_rows as $index=>$new_row){
					if ($new_row['client']['clientId']==$clientId){
						preg_match("~<p>(.*?)</p>~is",$row['clientSiteExtended']['longDesc'],$match_arr);
						if (isset($match_arr[1])){
							$longDesc=$match_arr[1];
						}else{
							$longDesc=$row['clientSiteExtended']['longDesc'];
						}
						$longDesc=preg_replace("~<[^>]*>~is","",$longDesc);
						
						if (strlen($longDesc)>155){
							$tmp=explode(" ",$longDesc);
							$str='';
							foreach($tmp as $word){
								$str.=" ".$word;
								if (strlen($str)>=155){
									break;
								}
							}

							$last_letter=substr($str,-1);
							if (!preg_match("~[a-z0-9]~is",$last_letter)){
								$str=substr($str,0,-1);
							}
							$str.="...";
							$longDesc=$str;
						}
						
						$new_rows[$index]['client']['longDesc']=$longDesc;
					}
				}
			}

//print "<pre>"; print_r($new_rows); exit;

			return $new_rows;

		}
    
    function validateDayOfWeek($date) {
        $mailingDate = strtotime($date['mailingDate']);
        if ($mailingDate < time()) {
            return false;
        }
        $weekday = getdate($mailingDate);
        $mailingType = $this->MailingType->findByMailingTypeId($this->data['Mailing']['mailingTypeId']);
        if ($weekday['wday'] != $mailingType['MailingType']['mailingDay']) {
            return false;
        }
        else {
            return true;
        }
    }
    
    function validateNoDuplicates($date) {
        if ($this->find('first', array('conditions' => array('Mailing.mailingDate' => $date, 'Mailing.mailingTypeId' => $this->data['Mailing']['mailingTypeId'])))) {
            return false;
        }
        else {
            return true;
        }
    }

}
?>
