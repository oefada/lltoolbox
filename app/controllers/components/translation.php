<?php
class TranslationComponent extends Object
{
    const DEFAULT_LOCALE = 1;

    private static $translationDataStub = array(
        1 => array(
            "TEXT_PACKAGES_UPPER" => "Packages",
            "TEXT_PACKAGE_UPPER" => "Package",
            "TEXT_PACKAGES_LOWER" => "packages",
            "TEXT_PACKAGE_LOWER" => "package",
            "TEXT_WHY_DO_WE_ASK" => "",
            "TEXT_DEFAULT_SELECT_COUNTRY_CODE" => "US",
            "TEXT_ZIP_CODE" => "ZIP / Postal Code",
            "TEXT_DEFAULT_COUPON" => "WELCOMELL",
            "TEXT_VACATION" => "vacation",
            "TEXT_VACATION_UPPER" => "Vacation",
            "TEXT_VACATION_PLURAL" => "vacations",
            "TEXT_VACATIONS_UPPER" => "Vacations",
            "TEXT_VACATION_EXPERIENCE" => "Vacation Experience",
            "TEXT_VACATION_EXPERIENCES" => "vacation experiences",
            "TEXT_VACATION_EXPERIENCES_UPPER" => "Vacation Experiences",
            "TEXT_FAVORITE" => "favorite",
            "TEXT_FAVORITE_PLURAL" => "favorites",
            "TEXT_LABEL_PACKAGES" => "packages",
            "TEXT_LABEL_PACKAGE" => "package",
            "TEXT_EMAIL_COSTUMER_SERVICE" => "Email Customer Service",
            "TEXT_SALES_TYPE" => "Sales Type",
            "TEXT_TERMS" => "Terms of Use",
            "TEXT_RAF_DESCRIPTION" => 'Introduce your friends to the extraordinary vacation experiences on Luxury Link! We will welcome them with a $50 credit for their first purchase, and thank you for the introduction with a $50 credit for each friend that books with us.',
            "TEXT_BLOCK_WHY_DO_WE_ASK" => "<strong>Why do we ask for your username?</strong> &nbsp;Your username is unique and displayed publicly when bidding on an auction. Must be between 6 and 25 characters long; letters &amp; numerals only.",
            "TEXT_LABEL_VIEW_ALL_PACKAGES" => "View All Vacation Packages",
            "TEXT_LABEL_NEW_PACKAGES" => "Newest Vacation Packages",
            "TEXT_LABEL_PACKAGES_UNDER" => "Packages under",
            "TEXT_LABEL_BEST_VACATION_DEAL" => "Best Vacation Deal",
            "TEXT_LABEL_VACATION_EXPERIENCES" => "Vacation Experiences",
            "TEXT_LABEL_VACATION_EXPERIENCES_VIEW_ALL" => "View all Vacation Experiences",
            "TEXT_LABEL_VACATION_TYPES" => "Vacation Types",
            "TEXT_LABEL_DESTINATIONS" => "Destinations",
            "TEXT_LABEL_FIND_LUXURY_VACATION" => "Find a Luxury Vacation",
            "TEXT_LABEL_NUMBER_OF_NIGHTS" => "Number of Nights",
            "TEXT_LABEL_HAVE_PROMO_CODE" => "Have a gift certificate or promo code?",
            "TEXT_LABEL_PROMO_CODE_ERROR_PREVIOUS" => "The promo code you entered is valid, but there is a previous promo code associated with this offer.",
            "TEXT_LABEL_PROMO_CODE_ERROR_INVALID" => "The promotional code you entered is either invalid, expired or did not meet the minimum purchase price. <br/>Please re-check the code and enter it again.",
            "TEXT_LABEL_PROMO_CODE" => "Promotional Code",
            "TEXT_LABEL_GIFT_CERT" => "Gift Certificate",
            "TEXT_LABEL_GC_CODE_ERROR" => "The gift certificate code you entered is valid, but there is a previous gift certificate associated with this offer.",
            "TEXT_LABEL_CONCIERGE_CALL_CTA" => "CALL NOW TO BOOK",
            "TEXT_LABEL_CONCIERGE_WELCOME" => "Need Assistance? Ask a Travel Concierge.",
            "TEXT_LABEL_CONCIERGE_HOURS_BR" => "24 Hours / 7 Days a Week",
            "TEXT_LABEL_CONCIERGE_EMAIL_CTA" => "Email A Travel Concierge Here",
            "TEXT_LABEL_FEATURED_VACATIONS" => "Featured Vacations",
            "TEXT_LABEL_VIEW_NEWEST_VACATIONS" => "View Our Newest Vacations",
            "TEXT_LABEL_EXTRAORDINARY_VACATIONS" => "Extraordinary Vacations",
            "TEXT_TRACKING_ADROLL_ADV" => "6FCG6S7KINEJVD3GRBAKNF",
            "TEXT_TRACKING_ADROLL_PIX" => "OWN6IP36CZF7HJFIBU2GF6",
            "TEXT_ABANDON_CHECKOUT_1" => "Need assistance with your booking? Call ",
            "TEXT_ABANDON_CHECKOUT_2" => " to speak to a Travel Concierge.",
            "TEXT_EXPERIENCE_LENGTH" => "Experience Length",
            "TEXT_PACKAGE_DETAILS" => "Package Details"

        ),
        2 => array(
            "TEXT_PACKAGES_UPPER" => "Experiences",
            "TEXT_PACKAGE_UPPER" => "Experience",
            "TEXT_PACKAGES_LOWER" => "experiences",
            "TEXT_PACKAGE_LOWER" => "experience",
            "TEXT_WHY_DO_WE_ASK" => "",
            "TEXT_DEFAULT_SELECT_COUNTRY_CODE" => "UK",
            "TEXT_ZIP_CODE" => "Postal Code",
            "TEXT_DEFAULT_COUPON" => "WELCOMEUK",
            "TEXT_VACATION" => "stay",
            "TEXT_VACATION_UPPER" => "Experience",
            "TEXT_VACATION_PLURAL" => "experiences",
            "TEXT_VACATIONS_UPPER" => "Experiences",
            "TEXT_VACATION_EXPERIENCE" => " Experience",
            "TEXT_VACATION_EXPERIENCES" => " experiences",
            "TEXT_VACATION_EXPERIENCES_UPPER" => " Experiences",
            "TEXT_FAVORITE" => "favourite",
            "TEXT_FAVORITE_PLURAL" => "favourites",
            "TEXT_LABEL_PACKAGE" => "experience",
            "TEXT_LABEL_PACKAGES" => "experiences",
            "TEXT_EMAIL_COSTUMER_SERVICE" => "Email Concierge",
            "TEXT_SALES_TYPE" => "No. of Nights",
            "TEXT_TERMS" => "Conditions of Use & Sale",
            "TEXT_RAF_DESCRIPTION" => "Introduce your friends to the extraordinary travel experiences on Luxury Link! We will welcome them with a 30£ credit for their first purchase, and thank you for the introduction with a £ credit for each friend that books with us.",
            "TEXT_BLOCK_WHY_DO_WE_ASK" => "<strong>Why do we ask for your username?</strong>  Your username is unique and displayed publicly when interacting on the site. Must be between 6 and 25 characters long; letters & numerals only.",
            "TEXT_LABEL_VIEW_ALL_PACKAGES" => "View All Experiences",
            "TEXT_LABEL_NEW_PACKAGES" => "Newest Experiences",
            "TEXT_LABEL_PACKAGES_UNDER" => "Experiences under",
            "TEXT_LABEL_BEST_VACATION_DEAL" => "Best Deal",
            "TEXT_LABEL_VACATION_EXPERIENCES" => " Experiences",
            "TEXT_LABEL_VACATION_EXPERIENCES_VIEW_ALL" => "View all Experiences",
            "TEXT_LABEL_VACATION_TYPES" => "Experience Types",
            "TEXT_LABEL_DESTINATIONS" => "Destinations",
            "TEXT_LABEL_FIND_LUXURY_VACATION" => 'Find a Luxury Experience',
            "TEXT_LABEL_NUMBER_OF_NIGHTS" => "Number of Nights",
            "TEXT_LABEL_HAVE_PROMO_CODE" => "Have a gift certificate or voucher code?",
            "TEXT_LABEL_PROMO_CODE_ERROR_PREVIOUS" => "The voucher code you entered is valid, but there is a previous voucher code associated with this offer.",
            "TEXT_LABEL_PROMO_CODE_ERROR_INVALID" => "The voucher code you entered is either invalid, expired or did not meet the minimum purchase price. <br/>Please re-check the code and enter it again.",
            "TEXT_LABEL_PROMO_CODE" => "Voucher Code",
            "TEXT_LABEL_GIFT_CERT" => "Gift Certificate",
            "TEXT_LABEL_GC_CODE_ERROR" => "The gift certificate code you entered is valid, but there is a previous gift certificate associated with this offer.",
            "TEXT_LABEL_CONCIERGE_HOURS_BR" => "24 Hours / 7 Days a Week",
            "TEXT_LABEL_FEATURED_VACATIONS" => "Featured Experiences",
            "TEXT_LABEL_VIEW_NEWEST_VACATIONS" => "View Our Newest Experiences",
            "TEXT_LABEL_EXTRAORDINARY_VACATIONS" => "Extraordinary Experiences",
            "TEXT_TRACKING_ADROLL_ADV" => "AS7JMMXIV5AIVH2ITRDXWA",
            "TEXT_TRACKING_ADROLL_PIX" => "3Q4EQGFNOBCHPCW6QCAZAM",
            "TEXT_EXPERIENCE_LENGTH" => "Length of Stay",
            "TEXT_PACKAGE_DETAILS" => "Experience Details"
        ),
    );

    private $translationDataSource;
    private $localeTranslationData = null;


    public function initialize()
    {
        //changeable data source
        $this->translationDataSource = $translationDataSource = self::$translationDataStub;
/*
        if ($this->localeTranslationData == null) {
            if (isset($translationDataSource[$this->getContext()->getLocale()])) {

                //grab localTranslation data only
                $this->localeTranslationData = $translationDataSource[$this->getContext()->getLocale()];
            }
        }
*/
    }

    public function getTranslationforKey($strKey)
    {
        $data = $this->localeTranslationData;

        $strKey = trim($strKey);

        if (!isset($strKey)) {
            throw new Exception('Translation Token Required');
        }

        if (!isset($data)) {
            //no local data, use Default Data
            return $this->getDefaultTranslation($strKey);

        }

        if (isset($data[$strKey])) {
            return $data[$strKey];
        }

        return $this->getDefaultTranslation($strKey);
    }

    public function getDefaultTranslation($strKey)
    {

        $data = $this->translationDataSource;

        if (!isset($strKey)) {

            throw new Exception('Translation Token Required');
        }
        if (isset($data[self::DEFAULT_LOCALE][$strKey])) {

            return $data[self::DEFAULT_LOCALE][$strKey];
        } else {
            //log?
            return $strKey;
        }
    }

    public function getTranslationforText($strText,$searchKey = null,$replaceKey = null)
    {
        if ($this->getContext()->isUS()) {
            return $strText;
        } else {
            if (($replaceKey !== null) && ($replaceKey !== null)) {
                return str_replace($searchKey,$replaceKey,$strText);
            } else {
                return $strText;
            }
        }
    }

    /*
     * Shows translations for Locale
     * @return array
     */
    public function showTranslationDataForLocale()
    {

        if (isset($this->localeTranslationData)) {

            return print_r($this->localeTranslationData, true);
        }
    }
}
