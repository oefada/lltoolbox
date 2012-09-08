<div>
	<div style="float: right;">
		<button id="newCs">
			New
		</button>
		<button id="openCsSearch">
			&gt;&gt;
		</button>
	</div>
	<div>
		<h2>CS Tool</h2>
	</div>
</div>

<div>
	<div style="float: right;"><span id="csToolClock">Clock</span></div>
	<div><span id="csUserName"><?php echo $username; ?></span></div>
</div>

<div>
	<input id="cs_omnibox" type="text" value="OmniBox goes here..." />
</div>

<div class="guest interaction">
	<h3>Guest</h3>
	<p>This is a test.</p>
	<ul>
		<li><a href="#" onclick="jQuery(this).parents('div.interaction').css({'background':'#ffeeee'});return false;">Red</a></li>
		<li><a href="#" onclick="jQuery(this).parents('div.interaction').css({'background':'#eeffee'});return false;">Green</a></li>
	</ul>
</div>

<div class="client interaction">
	<h3>Client</h3>
	<p>This is a test.</p>
	<ul>
		<li><a href="#" onclick="jQuery(this).parents('div.interaction').css({'background':'#ffeeee'});return false;">Red</a></li>
		<li><a href="#" onclick="jQuery(this).parents('div.interaction').css({'background':'#eeffee'});return false;">Green</a></li>
	</ul>
</div>

<div class="ajax debug interaction">
	<h3>Ajax Debug</h3>
	<div id="ajaxDebug">Ready.</div>
</div>