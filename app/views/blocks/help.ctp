&#160;<?php
if (isset($this->params['named']['module'])) {
	echo '<!-- ';
	echo 'No help available for: ';
	echo htmlentities($this->params['named']['module']);
	echo ' -->';
}
