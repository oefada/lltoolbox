<?php
class MultisiteHelper extends AppHelper {
    var $sites = array(1 => array('dbName' => 'luxurylink',
                                  'displayName' => 'Luxury Link'),
                       2 => array('dbName' => 'family',
                                  'displayName' => 'Family')
                       );
    
    function checkbox($modelName, $label='sites') {
        $out = '<div class="input select">';
        $out .= '<label>'.ucwords($label).'</label>';
        foreach($this->sites as $site) {
            if (!empty($this->data)) {
                $checked = (in_array($site['dbName'], $this->data[$modelName]['sites'])) ? ' checked' : '';
            }
            else {
                $checked = '';
            }
            $out .= '<div class="checkbox">';
            $out .= '<input id="'.$modelName.'Sites'.$site['displayName'].'" type="checkbox" value="'.$site['dbName'].'" name="data['.$modelName.'][sites][]" '.$checked.' />';
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
}
?>