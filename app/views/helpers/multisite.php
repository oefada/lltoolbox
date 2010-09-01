<?php
class MultisiteHelper extends AppHelper {
    var $sites = array(1 => array('dbName' => 'luxurylink',
                                  'displayName' => 'Luxury Link',
                                  'styleId' => 'LuxuryLink'),
                       2 => array('dbName' => 'family',
                                  'displayName' => 'Family',
                                  'styleId' => 'Family')
                       );
    
    function checkbox($modelName, $label='sites', $loaSites=null) {
        $out = '<div class="input select">';
        if ($label) {
            $out .= '<label>'.ucwords($label).'</label>';
        }
        foreach($this->sites as $site) {
            if ($loaSites) {
                if (!in_array($site['dbName'], $loaSites)) {
                    continue;
                }
            }
            if (!empty($this->data)) {
                $checked = (in_array($site['dbName'], $this->data[$modelName]['sites'])) ? ' checked' : '';
            }
            else {
                $checked = '';
            }
            $out .= '<div class="checkbox">';
            $out .= '<input id="'.$modelName.'Sites'.$site['styleId'].'" type="checkbox" value="'.$site['dbName'].'" name="data['.$modelName.'][sites][]" '.$checked.' />';
            $out .= '<label>'.$site['displayName'].'</label>';
            $out .= '</div>';
        }
        $out .= '</div>';
        return $out;
    }
    
    function indexDisplay($modelName, $modelSites) {
        $out = '';
        foreach($this->sites as $site) {
            if (in_array($site['dbName'], $modelSites)) {
                $out .= $site['displayName'].'<br />';
            }
        }
        return $out;
    }
    
    function displayName($siteId) {
        return $this->sites[$siteId]['displayName'];
    }
    
    function dbName($siteId) {
        return $this->sites[$siteId]['dbName'];
    }
}
?>