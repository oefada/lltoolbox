<?php
$this->viewVars['hideSidebar'] = true;
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('live_mysql');

$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$option = isset($_POST['option']) ? $_POST['option'] : false;

if ($_GET['option'] == 'delete') {
	$option = 'delete';
}

$help_mstr = array();
$section_mstr = array();

$result = mysql_query('select * from helpFaq where inactive <> 1 order by sectionId, topicId');

while ($row = mysql_fetch_array($result)) {
	if (is_numeric($row['topicId'])) {
		if ($row['topicId'] == 0) {
			$section_mstr[$row['sectionId']] = trim($row['topicTitle']);
		}

		$help_mstr[$row['sectionId']][$row['topicId']]['topicTitle'] = trim($row['topicTitle']);
		$help_mstr[$row['sectionId']][$row['topicId']]['topicLink'] = $row['topicLink'];
		$help_mstr[$row['sectionId']][$row['topicId']]['topicKeywords'] = trim($row['topicKeywords']);
		$help_mstr[$row['sectionId']][$row['topicId']]['text'] = trim($row['text']);
		$help_mstr[$row['sectionId']][$row['topicId']]['helpFaqId'] = $row['helpFaqId'];
	}
}

if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
	$tmp = explode('.', $_REQUEST['id']);
	$sid = $tmp[0];
	$tid = $tmp[1];
	$section_overview_name = $help_mstr[$sid][0]['topicTitle'];								
	$section_topic_title = $help_mstr[$sid][$tid]['topicTitle'];
	$section_topic_keywords = $help_mstr[$sid][$tid]['topicKeywords'];
	$section_topic_link = $help_mstr[$sid][$tid]['topicLink'];
	$section_text = $help_mstr[$sid][$tid]['text'];
	$section_helpFaqId = $help_mstr[$sid][$tid]['helpFaqId'];
}

switch($option) {
	case "new_section":
		$action = "addSection";
		$p_new_section_name = trim($_POST['p_new_section_name']);
		if (!$p_new_section_name) {
			$message = "New section name cannot be blank.";
			break;
		}

		foreach ($section_mstr as $k=>$v) {
			if (strtolower(trim($v)) == strtolower($p_new_section_name)) {
				$message = "This section name is already taken! Please choose another name.";
				break 2;
			}
		}
		
		$next_section_id = count($section_mstr);
		$next_section_topic_link = "$next_section_id.0";

		$sql = "insert into helpFaq(sectionId, topicId, text, topicKeywords, topicTitle, topicLink) ";
		$sql.= "VALUES('$next_section_id', '0', '', '', '$p_new_section_name', '$next_section_topic_link')";
		$result = mysql_query($sql);
		
		if ($result) {
			$action = 'complete';
			$message_complete = "New Section Added Succesfully.";
		} else {
			$message = "New Section NOT ADDED - database problem.";
		}

		break;
	case "delete":
		if ($id && $section_helpFaqId) {
			$sql = "update helpFaq set help_inactive = '1' where helpFaqId = '$section_helpFaqId'";
			$result = mysql_query($sql);
	
			if ($result) {
				$action = 'complete';
				$message_complete = "Topic has been inactivated.";
			} else {
				$message = "Operation not performed.  Database error.";
			}
		}
		break;
	case "new_entry":
		$p_section_id = $_POST['p_section_id'];
		$p_topic_title = trim($_POST['p_topic_title']);
		$p_text = trim($_POST['p_text']);
		$p_topic_keywords = trim($_POST['p_topic_keywords']);

		$search_char = array("&ldquo;", "&rdquo;");
		$p_text = str_replace($search_char, "&quot;", $p_text);
		
		$search_char = array("&lsquo;", "&rsquo;");
		$p_text = str_replace($search_char, '&#39;', $p_text);
		
		$search_char = array("&ndash;", "&mdash;");
		$p_text = str_replace($search_char, '-', $p_text);

		$p_text = str_replace("''", "&#39;", $p_text);
		$p_text = str_replace("'", "&#39;", $p_text);
		
		if (!$p_section_id || !is_numeric($p_section_id) || !$p_topic_title) {
			$message = 'Section, Topic Title, Keywords, or the Text cannot be blank';
			$action = 'add';
			break;
		}

		$next_topic_id = count($help_mstr[$_POST['p_section_id']]);
		$next_topic_link = "$p_section_id.$next_topic_id";

		$sql = "insert into helpFaq(sectionId, topicId, text, topicKeywords, topicTitle, topicLink) ";
		$sql.= "VALUES('$p_section_id', '$next_topic_id', '$p_text', '$p_topic_keywords', '$p_topic_title', '$next_topic_link')";
		$result = mysql_query($sql);
		
		if ($result) {
			$action = 'complete';
			$message_complete = "New Topic Added Succesfully.";
		} else {
			$message = "New Topic NOT ADDED - database problem.";
		}
		
		break;

	case "update":
		$section_topic_title = trim($_POST['p_topic_title']);
		$section_topic_keywords = trim($_POST['p_topic_keywords']);
		$section_text = trim($_POST['p_text']);
	
		$search_char = array("&ldquo;", "&rdquo;");
		$section_text = str_replace($search_char, "&quot;", $section_text);
		
		$search_char = array("&lsquo;", "&rsquo;");
		$section_text = str_replace($search_char, '&#39;', $section_text);
		
		$search_char = array("&ndash;", "&mdash;");
		$section_text = str_replace($search_char, '-', $section_text);

		$section_text = str_replace("''", "&#39;", $section_text);
		$section_text = str_replace("'", "&#39;", $section_text);
		
		if (!$id) {
			$action = 'view';
			break;
		}
		if (!$section_topic_title) {
			$action = 'edit';
			$message = 'Topic Title, Keywords, or the Text cannot be blank';
			break;
		}

		$sql = "update helpFaq set text = '$section_text', topicKeywords = '$section_topic_keywords', ";
		$sql.= "topicTitle = '$section_topic_title' where helpFaqId = '$section_helpFaqId'";
		$result = mysql_query($sql);
		if ($result) {
			$action = 'complete';
			$message_complete = "Updated succesfully.";
		} else {
			$message = "Update Failed.";
		}
		break;

	default:
		break;
}

$section_hdr = "style=\"background-color: #EEEEEE; font-weight:bold;\"";
?>

<script language="javascript" type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
function deleteTopic(id) {
	if (!id) return false;
	var confirmed = confirm("Are you 100% sure about deleting this topic?");
	if (confirmed) {
		window.location = 'help_faq?option=delete&id=' + id;
	} else {
		return false;
	}
}
</script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	// General options
	mode : "textareas",
	theme : "advanced",
	plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	// Theme options
	theme_advanced_buttons1 : "save,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : false,

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "lists/image_list.js",
	media_external_list_url : "lists/media_list.js"
});
</script>
<style text="text/css">
	#notesHelp { color: #CC5555; }
	#notesHelp ul li  {margin:0px; padding:0px;}
</style>

<div id="container">
	<div style="padding-bottom:5px; border-bottom:1px solid #a3a3a3; margin-bottom:30px;">
		<h3 class="hdr">Help and FAQ Tool</h3>
		Use this tool to edit content for the HELP and FAQ page.<br /><br />
		<div style="margin-bottom:5px;">
			<a <? echo $action == 'view' ? "class=\"textBold\"" : '' ?> href="help_faq?action=view">View All Topics</a>
			<a <? echo $action == 'addSection' ? "class=\"textBold\"" : '' ?> style="margin-left:15px;" href="help_faq?action=addSection">Add New Section</a>
			<a <? echo $action == 'add' ? "class=\"textBold\"" : '' ?> style="margin-left:15px;" href="help_faq?action=add">Add New Topic</a>
			<?php
			if ($action == 'edit') { echo "<span style='margin-left:15px; color:#0077EE; font-weight:bold;'>Editing Topic</span>";   }
			?>
		</div>
	</div>

	<?php if ($message) { echo "<div class='grayBox'>$message</div>"; } ?>
	<?php if ($action == 'add' || $action == 'edit') { ?>
	<div id="notesHelp" style="margin-bottom:10px;">
		<h4 style="color:#CC4422; font-weight:bold;">Notes:</h4>
		<div>To create an external link use <strong>javascript:newWindow('url');</strong></div>
		<ul>
			<li>To goto the community page --> javascript:newWindow('http://community.luxurylink.com');</li>
			<li>To goto the reg page --> javascript:newWindow('https://www.luxurylink.com/my/my_reg');</li>
			<li>To goto the about page --> javascript:newWindow('../about/abt_main');</li>
		</ul>
		<div>To create an internal link use <strong>javascript:go('section');</strong></div>
		<ul>
			<li>To goto Section 4.1 -> javascript:go('4.1');</li>
		</ul>
		<div>To create an anchor link:</div>
		<ol>
			<li>Highlight the word / phrase that is going to be an anchor tag.</li>
			<li>Click on the ANCHOR icon and give your anchor a name.</li>
			<li>Highlight the word / phrase that is going to be the link to your new anchor tag.</li>
			<li>Click on the LINK icon and select the anchor name in Anchors dropdown list.</li>
		</ol>
	</div>
	<?php 
	}
	switch($action) {
		case "complete":
			echo "<div class='grayBox'>$message_complete</div";
			break;
		case "edit":
			?>
			<div class="clean">
			<form action="help_faq" method="post">
			<table style="margin-bottom:25px;" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Section Name: </td>
					<td id="section_existing" align="left" valign="top"><?=$section_overview_name?></td>
				</tr>
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Topic ID: </td>
					<td id="section_existing" align="left" valign="top"><?=$section_topic_link?></td>
				</tr>
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Topic Title: </td>
					<td align="left" valign="top"><input style="width:400px;" type="text" name="p_topic_title" value="<?=$section_topic_title?>" /></td>
				</tr>
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Keywords: </td>
					<td align="left" valign="top"><input style="width:400px;" type="text" name="p_topic_keywords" value="<?=$section_topic_keywords?>" /></td>
				</tr>
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Delete this TOPIC: </td>
					<td align="left" valign="top"><a href="javascript:void(0);" onclick="deleteTopic('<?=$id?>');">Delete</a></td>
				</tr>
			</table>
			<textarea name="p_text" style="width:100%; height:400px;"><?=$section_text?></textarea>
			<input type="hidden" name="id" value="<?=$id?>" />
			<input type="hidden" name="option" value="update" />
			</form>
			</div>
			<?php
			break;
		case "addSection":
			?>
			<div class="clean">
			<form action="help_faq" method="post">
			<table style="margin-bottom:25px;" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="textBold" width="130" align="left" valign="top">New Section: </td>
					<td align="left" valign="top"><input style="width:400px;" type="text" name="p_new_section_name" value="<?=trim($_POST['p_new_section_name'])?>" /></td>
				</tr>
			</table>
			<input type="hidden" name="option" value="new_section" />
			<input type="submit" name="go" value="Add New Section" />
			</form>
			</div>
			<?php
			break;
		case "add":
			?>
			<div class="clean">
			<form action="help_faq" method="post">
			<table style="margin-bottom:25px;" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Section: </td>
					<td id="section_existing" align="left" valign="top">
						<select name="p_section_id">
							<option value=''>Select Section ...</option>
						<?php foreach($section_mstr as $k=>$v) {
							$selected = ($k == $_POST['p_section_id']) ? "selected='selected'" : ''; 
							echo "<option value='$k' $selected>$k.0 $v</option>\n";
						}?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Title: </td>
					<td align="left" valign="top"><input style="width:400px;" type="text" name="p_topic_title" value="<?=trim($_POST['p_topic_title'])?>" /></td>
				</tr>
				<tr>
					<td class="textBold" width="130" align="left" valign="top">Keywords: </td>
					<td align="left" valign="top"><input style="width:400px;" type="text" name="p_topic_keywords" value="<?=trim($_POST['p_topic_keywords'])?>" /></td>
				</tr>
			</table>
			<textarea name="p_text" style="width:100%; height:400px;"><?=trim($_POST['p_text'])?></textarea>
			<input type="hidden" name="option" value="new_entry" />
			</form>
			</div>
			<?php
			break;
		case "view":
		default:
			echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
			echo "<tr>\n";
			echo "	<th valign='top' align='left' width='20'>ID</th>\n";
			echo "	<th valign='top' align='left'>Section Title</th>\n";
			echo "	<th valign='top' align='center' width='20'>Content</th>\n";
			echo "	<th valign='top' align='center' width='20'>Keywords</th>\n";
			echo "</tr>\n";
			foreach($help_mstr as $k => $v) {
				foreach($v as $a => $b) {
				?>

			<tr id="<?=$b['topicLink']?>" <?php if($a==0) { echo $section_hdr;}?>>
				<td align="left"><?=$b['topicLink']?></td>
				<td align="left" class="altA"><a href="help_faq?action=edit&id=<?=$b['topicLink']?>"><?=$b['topicTitle']?></a></td>
				<td align="center"><? if ($b['text']) echo 'x';?></td>
				<td align="center"><? if ($b['topicKeywords']) echo 'x';?></td>
			</tr>

				<?php
				}
			}		
			echo "</table>";
			break;
	}
	?>

	<div style="margin-top:30px;">&nbsp;</div>
</div>
