<?php
require_once('auth.php');


$thiseditor = null;

$AreaManager = new AreasManager();
$alleBereiche = $AreaManager->getAllAreaSettings();

foreach ($alleBereiche as $value) {
    if(isset($_GET[$value['name']])){
        $thiseditor = $value;
        break;
    }
}

if (!is_array($thiseditor)){
    echo ("Fehler: Request unbekannt");
    return;
}


// help
function has_help_file() {
	global $ne_config_info, $thiseditor;
	$help_file = $ne_config_info['help_path'] .$thiseditor['help_page_name']. $ne_config_info['help_filesuffix'] ;
	return file_exists($help_file);
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($thiseditor["title"]); ?> bearbeiten - <?php echo($ne_config_info['app_titleplain']); ?></title>

<?php
    echo NavTools::includeHtml(
            "default",
            "tinymce/tinymce.min.js",
            "nav_tools.js" //todo put to default?
            );
?>


<script type="text/javascript">
var tinymceReady = false;

var helpText = "";

tinyMCE.init({
	forced_root_block : '',
	mode: "textareas",
	language: "de",
	plugins: "image link code table preview mitarbeiter feedimport ssiInclude image_choose",
	menubar: false,
	toolbar1: "undo redo | cut copy paste | link image table | outdent indent | code | preview | mitarbeiter | feedimport",
	toolbar2: "fontselect fontsizeselect | styleselect | alignleft aligncenter alignright alignjustify | bold italic underline strikethrough | bullist numlist",
	//theme: "advanced",
	//language: "de",
	//skin: "o2k7",
	relative_urls: false,
	convert_urls: false,
	//plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	theme_advanced_styles: "infologo",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_statusbar_location: "bottom",
	oninit: function() {
		tinymceReady = true;
	}
});
</script>

<script type="text/javascript">
function loadContentCallback(cdata) {
//	$("#txtContent").val(data);
	var rdata = cdata.replace(/<comment_ssi>/g, "<!-" + "-#");
	rdata = rdata.replace(/<comment>/g, "<!-" + "-");
	rdata = rdata.replace(/<\/comment>/g, "-" + "->");
	tinyMCE.get("txtContent").setContent(rdata);
}

var tinymceReadyCheck = null;
var this_editor_name = '<?php echo $thiseditor['name'];?>';
//var NavTools = new NavTools({something: 5});

function loadContent() {
	if(tinymceReady) {

        NavTools.call_php('app/classes/AreasEditor.php', 'get_content',
                        {tinymce: true, bereich: this_editor_name},
                        loadContentCallback);

		if(tinymceReadyCheck !== null) {
			clearTimeout(tinymceReadyCheck);
		}
	} else {
		setTimeout("loadContent()", 500);
	}
}

function saveContentCallback(data) {
	alert(data);
	location.reload();
}

/* ---------- Here comes jQuery: ---------- */
$(document).ready(function() {
	loadContent();

	$("#btnUpdate").click(function() {
		if(confirm("Sind Sie sicher?")) {
			var cnt = tinyMCE.get("txtContent").getContent();
			cnt = cnt.replace(new RegExp("<!-" + "-#", "g"), "<comment_ssi>");
			cnt = cnt.replace(/<!--/g, "<comment>");
			cnt = cnt.replace(/-->/g, "</comment>");

            NavTools.call_php('app/classes/AreasEditor.php', 'update_content',
                        {tinymce: true, bereich: this_editor_name, new_content: cnt},
                        saveContentCallback);
		}
	});

	// help
	$(".help-container .fetch").click(function() {
		var $this = $(this),
			content = $this.siblings(".hover-popover").find("content").html(),
			showContent = function(content) {
				$this.siblings(".hover-popover").show().find(".content").html(content);
			};

		console.log(content);

		if(content === undefined || content == "") {
			$.get("app/get_help.php?r=" + Math.random(), {
				"page_name": "<?php echo ($thiseditor['help_page_name']); ?>"
			}, showContent);
		} else {
			showContent(content);
		}
	});

	$(".hover-popover .dismiss").click(function() {
		$(this).closest(".hover-popover").hide();
    });
});
</script>
</head>

<body id="bd_Inhal">
	<?php require('common_nav_menu.php'); ?>

    <div class="container" id="wrapper">

        <div id="contentPanel1">

            <form action="" method="post" name="frmEdit" id="frmEdit">
                <fieldset>
                	<div class="page-header">
	                    <h3 class="page-header"><?php echo ($thiseditor["title"]); ?> bearbeiten</h3>
	                    <div class="pull-right">
							 <?php
				            	// help
				            	if (has_help_file()) {
				            ?>
				            	<div class="popover-container">
									<a class="fetch btn btn btn-primary btn-light" href="javascript:void(0);"><i class="icon-white">?</i> Hilfe</a>
									<div class="hover-popover">
										<div class="header clearfix">
											<h4>Hilfe</h4>
											<div class="pull-right">
												<a class="dismiss btn btn-black-white" href="javascript:void(0);">Ok</a>

											</div>
										</div>

										<div class="content"></div>
									</div>
								</div>
							<?php
				            	}
				            ?>
				            <a href="javascript:void(0);" class="btn btn-success btn-light" id="btnUpdate" name="btnUpdate" ><i class="icon-white icon-ok"></i> Speichern</a>
				        </div>
                    </div>

                    <textarea id="txtContent" class="padding-top" name="txtContent" cols="160" rows="25" class="textBox"></textarea>
                </fieldset>
            </form>
        </div>
    </div>

    <?php require('common_footer.php'); ?>
</body>

</html>
