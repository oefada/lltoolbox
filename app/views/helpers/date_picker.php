<?php
/**
 * Date Picker Helper
 *
 * @author Abdullah Zainul Abidin
 * @website http://php.abdullahsolutions.com/2008/02/output-helper-javascript-into-headers.html
 */
class DatePickerHelper extends FormHelper {

    var $format = '%Y-%m-%d';
    var $helpers = array('Javascript','Html');

    /**
     *Setup the format if exist in Configure class
     */
    function _setup(){
        $format = Configure::read('DatePicker.format');
        if($format != null){
            $this->format = $format;
        }
        else{
            $this->format = '%Y-%m-%d';
        }
    }

    function beforeRender(){
        $view = ClassRegistry::getObject('view');
        if (is_object($view)) {
            $view->addScript($this->Javascript->link('jscalendar/calendar.js'));
            $view->addScript($this->Javascript->link('jscalendar/lang/calendar-en.js'));
            $view->addScript($this->Javascript->link('common.js'));
            $view->addScript($this->Html->css('../js/jscalendar/calendar-blue'));
        }
    }

    /**
     * The Main Function - picker
     *
     * @param string $field Name of the database field. Possible usage with Model.
     * @param array $options Optional Array. Options are the same as in the usual text input field.
     */   
    function picker($fieldName, $options = array()) {
        $this->_setup();
        $htmlAttributes['id'] = $this->domId($fieldName);
        $divOptions['class'] = 'date';
        $options['type'] = 'text';
        $options['div']['class'] = 'date';
        $time='';
        if(isset($options['showstime'])){
            if($options['showstime']===true) {
                $time=',"24"';
                $this->format.=" %k:%M";
            }
            unset($options['showstime']);
        }

        $options['after'] = $this->Html->link($this->Html->image('../js/jscalendar/img.gif'), '#', array('onClick'=>"return showCalendar('".$htmlAttributes['id']."', '".$this->format."'$time); return false;"), null, false);
        $output = $this->input($fieldName, $options);

        return $output;
    }

    function flat($fieldName, $options = array()){
        $this->_setup();
        $htmlAttributes = $this->domId($options);       
        $divOptions['class'] = 'date';
        $options['type'] = 'hidden';
        $options['div']['class'] = 'date';
        $hoder = '<div id="'.$htmlAttributes['id'].'_cal'.'"></div><script type="text/javascript">showFlatCalendar("'.$htmlAttributes['id'].'", "'.$htmlAttributes['id'].'_cal'.'", "'.$this->format.'", function(cal, date){document.getElementById(\''.$htmlAttributes['id'].''.'\').value = date});</script>';
        $output = $this->input($fieldName, $options).$hoder;

        return $output;
    }
}
?>