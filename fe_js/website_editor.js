var imgObj, imgW, imgH, jsonArray;

function loadConf(confFileName) {
	$.getJSON("app/edit_conf.php?r=" + Math.random(), {
		"oper": "get_conf",
		"conf_file_name": confFileName
	}, loadContentCallback);
}
// function getValueByName(jdata, wert){
	// var tmpRet = "parameter nicht gefunden";
	// $.each(jdata, function(intInd, value){
		// if (value.opt_name == wert){
			// tmpRet = value.opt_value;
			// return false;
		// }
	// });
	// return tmpRet;
// }


function loadContentCallback(data) {
	jsonArray = data;

	$.each(data, function( intIndex, obj){
		$("#"+obj.opt_name).val(obj.opt_value);
	});

	imgH = $("#logo-Height").val();
	imgW = $("#logo-Width").val();

	if($("#logo-URL").val() != "") {
		imgPreLoad();
	}
}

function previewImageLoadCallback() {
	if(imgObj.complete) {
        imgW = imgW || imgObj.width;
        imgH = imgW || imgObj.height;
	}
}

function saveContentCallback(data) {
	//alert(data);
	saveVars(data);
//	location.reload();
}

//saveVars function, for every button
function saveVars(datab4){
	$.each(jsonArray, function(ind, obj){
		if($("#"+obj.opt_name).length){
			jsonArray[ind].opt_value = $("#"+obj.opt_name).val();
		}
	});
	$.post("app/edit_conf.php", {
			"oper": "set_conf",
			"conf_file_name": thisConf,
			"jsonData": JSON.stringify(jsonArray)
		}, function(rdata) {
			alert(datab4);
			loading(false);
			loadConf(thisConf);
		});
}


function imgPreLoad(){
    var imgStr = "";
	if($("#logo-URL").val() !== ""){
		var imgsrctmp = $("#logo-URL").val();
		var imgalttmp = $("#logo-Alt").val();
		imgH = $("#logo-Height").val();
		imgW = $("#logo-Width").val();
		imgObj = new Image();
		imgObj.src = imgsrctmp;
		imgObj.onload = previewImageLoadCallback;
        var hStyle = (imgH)?"height:" + imgH + "px;":"";
        var wStyle = (imgW)?"width:" + imgW + "px;":"";
		imgStr = "<img alt='" + imgalttmp + "' style='" + hStyle + wStyle + "' src='" + imgsrctmp + "' border='0'/>";
	}
	$("#imgPrev").html(imgStr);
}

function loading(yesNo){
	if (yesNo) {
		if ($('div.tmpLoadingOverlay').length){
			$('div.tmpLoadingOverlay').fadeIn(100);
		}else{
			$('body').append('<div class="tmpLoadingOverlay"><div style="z-index:1000;position:fixed;top:0;bottom:0;left:0;width:100%;height=100%;background:#000;opacity:0.35;-moz-opacity:0.35;filter:alpha(opacity=35);visibility:visible;"><p class="p_tmp_class_loading"  style="position:fixed;top:50%;width:100%;z-index:1001;color:#fff;font-size:20px;font-weight:bold;text-align:center;">Loading...</p></div></div>');
		}
		$("body").css("overflow", "hidden");
	}else{
		$('div.tmpLoadingOverlay').fadeOut(1000);
		$("body").css("overflow", "auto");
    }
}


$(document).ready(function() {
	loadConf(thisConf);
//	on change any param of logo, reload it (preview)
	$("#bildBlock :input").on('keydown', function() {
        setTimeout(imgPreLoad, 500);
	});
	//all inputs add class 'textBox'
	$('input[type="text"]').addClass("textBox");

	//save all inputs to conf.
	$("#btnSaveall").click(function saveAll() {
		$.each(jsonArray, function(ind, obj){
			if($("#"+obj.opt_name).length){
				jsonArray[ind].opt_value = $("#"+obj.opt_name).val();
			}
		});
		loading(true);
		var btnTmp = $(this);
		var btnTmpVal = $(this).val();
		$(this).val("Moment...");
        $(this).attr("disabled", "disabled");
        $.post("app/edit_conf.php", {
            "oper": "set_conf",
            "conf_file_name": thisConf,
            "jsonData": JSON.stringify(jsonArray)
        }, function(rdata) {
            alert(rdata);
            btnTmp.val(btnTmpVal);
            btnTmp.removeAttr("disabled");
            loading(false);
            loadConf(thisConf);
        });
	});



	$("#chkAllowHtml").click(function(){
		if ($("#chkAllowHtml:checked").length > 0){
            alert("HTML auf eigene Gefahr verwenden");
		}
	});


	$("#btnUpdate").click(function() {
		var text = $("#name-des-Webauftritts").val();
		var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
		var imgUrl = $("#logo-URL").val();
		var imgAlt = $("#logo-Alt").val();
		var siteTitle = $("#titel-des-Webauftritts").val();

		// if image specified, then alt-text cannot be empty!
		if(imgUrl != "") {
			if(imgAlt == "") {
				alert("Bitte geben Sie die Beschreibung zu Ihrem Logo an!");
				return false;
			}
		}

		if(confirm("Wollen Sie wirklich speichern?")) {
			loading(true);
			if(imgUrl !== "") {
				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
			}else{
				img = "";
			}
			var templname = $("#selTempl").val();
			var pdata = {
				"content_text": text,
				"content_desc": desc,
				"content_img": img,
				"content_img_alt": imgAlt,
				"site_title_text": siteTitle,
				"content_allow_html": $("#chkAllowHtml:checked").length > 0 ? true : false
			};
			$.post("app/edit_logo.php", {
				"json_oper": "update_content",
				"json_content": JSON.stringify(pdata),
				"template_name": templname
			}, saveContentCallback);
		}
	});

//	$("#btnLoadLogo").click(function() {
//		$.getJSON("app/edit_logo.php?r=" + Math.random(), {
//			"json_oper": "get_content",
//			"template_name": $("#selTempl").val()
//		}, loadContentCallback);
//	});


	$("#btnUpdateExisted").click(function() {
		if(confirm("Wollen Sie wirklich alle Seiten aktualisieren und mit dem neuen Titel und/oder Logo versehen?")) {
			loading(true);
			var text = $("#name-des-Webauftritts").val();
			var desc = $("#kurzbeschreibung-zum-Webauftritt").val();
			var imgUrl = $("#logo-URL").val();
			var imgAlt = $("#logo-Alt").val();
			var siteTitle = $("#titel-des-Webauftritts").val();
			var img = '';
			if(imgUrl !== "") {
//				img = "<img alt=\"" + imgAlt + "\" src=\"" + imgUrl + "\" width=\"" + imgW + "\" height=\"" + imgH + "\" border=\"0\" />";
                img = $('#imgPrev').html();
			}
			var templname = $("#selTempl").val();
			var pdata = {
				"content_text": text,
				"content_desc": desc,
				"content_img": img,
				"content_img_alt": imgAlt,
				"site_title_text": siteTitle,
				"content_allow_html": $("#chkAllowHtml:checked").length > 0 ? true : false
			};
			$.post("app/edit_logo.php", {
				"json_oper": "update_content_all",
				"json_content": JSON.stringify(pdata),
				"template_name": templname
			}, saveContentCallback);
		}
	});

	$("#btnCopySiteName").click(function() {
		$("#titel-des-Webauftritts").val($("#name-des-Webauftritts").val());
	});



	// initial load
	// $.getJSON("app/edit_logo.php?r=" + Math.random(), {
		// "json_oper": "get_content",
		// "template_name": $("#selTempl").val()
	// }, loadContentCallback);

});
