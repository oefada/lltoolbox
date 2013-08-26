<?php
class LoaDocument extends AppModel
{
    public $name = 'LoaDocument';
    public $useTable = 'loaDocument';
    public $primaryKey = 'loaDocumentId';

    public $order = array("LoaDocument.loaDocumentId DESC");
    public $actsAs = array('Containable', 'Logable');
    public $multisite = true;

    public $belongsTo = array(
     //   'Client' => array('foreignKey' => 'clientId'),
//        'Currency' => array('foreignKey' => 'currencyId'),
//        'LoaLevel' => array('foreignKey' => 'loaLevelId'),
//        'LoaMembershipType' => array('foreignKey' => 'loaMembershipTypeId'),
//        'AccountType' => array('foreignKey' => 'accountTypeId'),
//        'LoaPaymentTerm' => array('foreignKey' => 'loaPaymentTermId')
    );

    public $validate = array(

        'signerName' => array(
            'rule' => VALID_NOT_EMPTY,
            'required' => true,
            'message' =>'A signer name is required'
        ),

        /*'docDate' => array(
                'required' => true,
                'message' => 'Please fill in the date field',
            )*/
        );

    public function loaDocumentHowItWorks($membershipTypeId,$paymentTypeId,$hotelName){
        if (empty($paymentTypeId)){
            return false;
        }
         $arrMembershipTypes = array(
             3,//Total packages
             4, //Barter/Cashe
             5, //Barter
             7,//Total Nights
         );
         if(in_array($membershipTypeId, $arrMembershipTypes)){
             switch($paymentTypeId){
                 case(1)://Rev Split
                    $text= "In lieu of a cash fee, Luxury Link will accept a mutually agreed upon package from $hotelName to be sold on the Luxury Link website. Luxury Link will keep x% of the proceeds from the sale of these packages until the membership fee has been satisfied. Proceeds from subsequent sales of this package and any other promotional packages placed on the Luxury Link site shall be remitted directly to the property less the LL transaction fee noted above.";
                 break;
                 case(2)://Keep/Remit
                    $text ="In lieu of a cash fee, Luxury Link will accept a mutually agreed upon package from $hotelName to be sold on the Luxury Link website. Luxury Link will keep proceeds from the sale of every other package sold until the membership fee has been satisfied. Proceeds from subsequent sales of this package and any other promotional packages place on the Luxury Link site shall be remitted directly to the property less the LL transaction fee noted above.";
                 break;
                 case(6)://50/50
                     $text ="In lieu of a cash fee, Luxury Link will accept a mutually agreed upon package from $hotelName to be sold on the Luxury Link website. Luxury Link will keep 50% of the proceeds from the sale of these packages until the membership fee has been satisfied. Proceeds from subsequent sales of this package and any other promotional packages placed on the Luxury Link site shall be remitted directly to the property less the LL transaction fee noted above.";
                 break;
                 case(7)://Standard
                     $text ="In lieu of a cash fee, Luxury Link will accept a mutually agreed upon package from $hotelName to be sold on the Luxury Link website. Luxury Link will keep proceeds from the sale of these packages until the membership fee has been satisfied. Proceeds from subsequent sales of this package and any other promotional packages placed on the Luxury Link site shall be remitted directly to the property less the LL transaction fee noted above.";
                 break;
                 default:
                 break;
             }
         }
    }

    public function includeText($strCheckbox)
    {
        if(empty($strCheckbox)){
            return false;
            exit;
        }
        $strCheckbox = trim($strCheckbox);
        switch ($strCheckbox){
            case('New York Times'):
                $strText = '<b>'.$strCheckbox.'</b> - Inclusion in New York Times Great Getaways email promotion';
            break;
            case('Departures (American Express)'):
                $strText = '<b>'.$strCheckbox.'</b> - Inclusion in week-long Leader board or Box Ad on <a href="http://www.departures.com">Departures.com</a>';
            break;
            case('Exclusive Email'):
                $strText = '<b>'.$strCheckbox.'</b> – Exclusive Email drop to Luxury Link database subscribers';
            break;
            default:
                $strText = null;
            break;
        }
        return $strText;
    }
}