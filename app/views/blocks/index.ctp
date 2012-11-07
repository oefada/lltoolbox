<?php
$this->pageTitle = 'Blocks Editor';
$this->set('hideSidebar', true);
?>

<div id="demo1" class="demo" style="float: left; width: 300px;">
	<ul>
		<li id="phtml_1">
			<a href="#">Ski &amp; Snow</a>
			<ul>
				<li id="phtml_2">
					<a href="#">PhotoModule</a>
					<ul>
						<li id="phtml_22">
							<a href="#">Image</a>
						</li>
						<li id="phtml_23">
							<a href="#">Image</a>
						</li>
						<li id="phtml_24">
							<a href="#">Image</a>
						</li>
						<li id="phtml_25">
							<a href="#">Image</a>
						</li>
						<li id="phtml_26">
							<a href="#">Image</a>
						</li>
					</ul>
				</li>
				<li id="phtml_3">
					<a href="#">Main</a>
					<ul>
						<li id="phtml_31">
							<a href="#">Content</a>
							<ul>
								<li id="phtml_311">
									<a href="#">TabsModule</a>
									<ul>
										<li id="phtml_3111">
											<a href="#">Colorado</a>
										</li>
										<li id="phtml_3112">
											<a href="#">California</a>
										</li>
										<li id="phtml_3113">
											<a href="#">International</a>
										</li>
										<li id="phtml_3114">
											<a href="#">Canada</a>
										</li>
										<li id="phtml_3115">
											<a href="#">Other</a>
										</li>
									</ul>
								</li>
								<li id="phtml_311">
									<a href="#">SEO Text</a>
								</li>
								<li id="phtml_311">
									<a href="#">Newsletter Signup</a>
								</li>
							</ul>
						</li>
						<li id="phtml_32">
							<a href="#">Sidebar</a>
							<ul>
								<li id="phtml_321">
									<a href="#">Featured Auction</a>
								</li>
								<li id="phtml_322">
									<a href="#">Advertisement</a>
								</li>
								<li id="phtml_323">
									<a href="#">Learn More</a>
								</li>
								<li id="phtml_324">
									<a href="#">Advertisement</a>
								</li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
</div>

<div id="outputDiv">ASDF</div>

<div style="clear: both;"></div>

<br/>

<script type="text/javascript">
jQuery(function () {
	var $ = jQuery;
	$("#demo1")
		.jstree({
			"plugins" : ["themes","html_data","ui","crrm","hotkeys"],
			"core" : { "initially_open" : [ "phtml_1" ] }
		})
		.bind("loaded.jstree", function (event, data) {
		})
		.bind("select_node.jstree", function (event, data) {
			console.log(Math.random(),event,data,data.rslt.obj.attr("id"));
			var outputText = typeof data.rslt.obj.attr("showme");
			if (typeof outputText == 'string' && outputText.length > 0) {
				$('#outputDiv').html(outputText);
			}
		})
		.delegate("a", "click", function (event, data) { event.preventDefault(); });
	setTimeout(function () { $.jstree._reference("#phtml_1").open_node("#phtml_1"); }, 1000);
	setTimeout(function () { $.jstree._reference("#phtml_2").open_node("#phtml_2"); }, 2000);
	setTimeout(function () { $.jstree._reference("#phtml_3").open_node("#phtml_3"); }, 3000);
	setTimeout(function () { $.jstree._reference("#phtml_31").open_node("#phtml_31"); }, 4000);
	setTimeout(function () { $.jstree._reference("#phtml_311").open_node("#phtml_311"); }, 5000);
	setTimeout(function () { $.jstree._reference("#phtml_32").open_node("#phtml_32"); }, 6000);
});
</script>
