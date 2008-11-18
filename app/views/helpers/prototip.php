<?php class PrototipHelper extends AppHelper {
    var $helpers = array('Html', 'Javascript');
    
    var $allowed_options = array('ajax', 'border', 'borderColor', 'closeButton', 'delay', 'hideAfter', 'hideOn', 'hideOthers', 'hook', 'images', 'offset', 'radius', 'showOn', 'stem', 'style', 'target', 'title', 'viewport', 'width');

    var $tips = array();

    function tooltip($el, $content, $options = array()) {
		$content['style'] = 'toolboxblue';
		$content['stem'] = 'topLeft';
        if (isset($options['render']) && $options['render'] == true) {
            return $this->Javascript->codeBlock($this->output($this->_createTip($el, $content, $options)));
        } else {
            $this->tips[] = $this->_createTip($el, $content, $options);
        }
    }

    function renderTooltips() {
        $tips_string = '';
        foreach($this->tips as $tip) {
            $tips_string .= $tip."\n";
        }
        return $this->Javascript->codeBlock($tips_string);
    }

    function _createTip($el, $content, $options = array()) {
        $valid_el = array('\'', '$');
        if (!in_array(substr($el, 0, 1), $valid_el)) $el = "'$el'";
		if (isset($content['ajax'])) {
            return 'new Tip('.$el.', '.json_encode($content).');';
		}
		if (substr($content, 0, 1) != '\'' &&
            substr($content, 0, 4) != 'new ') $content = "'$content'";
        if ($options) {
            return 'new Tip('.$el.', '.$content.', '.$this->_parseOptions($options).');';
        } else {
            return 'new Tip('.$el.', '.$content.');';
        }
    }

    function _parseOptions($options = array()) {
        $opts = '{ ';
        $arr_opts = array();
        foreach($options as $key => $value) {
            if (in_array($key, $this->allowed_options)) {
                $value = $this->_parseValues($value);
                $arr_opts[] = "$key: $value";
            }
        }
        $opts .= join(",", $arr_opts);
        $opts .= ' }';
        return $opts;
    }
    
    function _parseValues($values = null) {
        if (!is_array($values)) {
            if (!is_int($values)) $values = "'$values'";
            return $values;
        }
        $value = '{ ';
        $val_opts = array();
        foreach($values as $key=>$val) {
            if (is_array($val)) {
                $val = $this->_parseValues($val);
            }
            if (!is_int($val)) $val = "'$val'";
            $val_opts[] = "$key: $val";
        }
        $value .= join(',', $val_opts);
        $value .= ' }';
        return $value;
    }
}?>