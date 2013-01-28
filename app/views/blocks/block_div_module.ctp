<ul>
	<li>
		Common classes:
		<ul>
			<?php
			$links = array(
				'content',
				'content-box',
				'link-box',
			);
			foreach ($links as $link) {
				echo '<li>';
				echo '<a href="#class-' . $link . '" onclick="';
				echo "jQuery('#editorDiv input[name=&quot;class&quot;]').val('$link').change();";
				echo 'return false;">';
				echo $link;
				echo '</a>';
				echo '</li>';
			}
			?>
		</ul>
	</li>
</ul>