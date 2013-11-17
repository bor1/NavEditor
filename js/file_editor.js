//
//global  var fileInfoArray from jqueryFileTree.js

function getFileInfoCallback(resp) {
	var fi = JSON.parse(resp);
	if(fi.thumb_name != "") {
//		var thumbHtml = "<img alt='' src='" + fi.thumb_name + "' style='border:0;' />";
//		$("#thumbImage").html(thumbHtml);
		$("#txtFileThumbUrl").val(fi.thumb_name);
	} else {
		$("#thumbImage").html("");
		$("#txtFileThumbUrl").val("");
	}
	$("#fileNameTd").html(fi.file_name);
	$("#fileSizeTd").html(fi.file_size + " Byte");
	$("#fileLastModTd").html(fi.modified_time);
	$("#txtFileUrl").val(fi.url);

	if(fi.editable) {
		var admin = <?php echo(($is_admin)? 1 : 0); ?>; //todo $is_admin -> test rights >= "admin" (1000)
		var hideEditorMode = <?php echo(($ne_config_info['hide_sourceeditor'])? 1 : 0); ?>;
		var extension = getExtension(fi.file_name);
		var forb_folders = ['css/','grafiken/','img/','ssi/','js/','vkdaten/','univis/','vkapp/'];
		if (admin || hideEditorMode == -1){
			$("#btnQuickEdit").removeAttr("disabled");
			$("#btnEditorEdit").removeAttr("disabled");
		}else{
			if (extension == "shtml" || extension == "html"){
				$("#btnQuickEdit").attr("disabled", "true");
				$("#btnEditorEdit").removeAttr("disabled");
			}else if(hideEditorMode == 1){
				$("#btnQuickEdit").attr("disabled", "true");
				$("#btnEditorEdit").attr("disabled", "true");
			}else if(hideEditorMode == 0){
				$("#btnQuickEdit").removeAttr("disabled");
				$("#btnEditorEdit").attr("disabled", "true");
				for (var fold_tmp in forb_folders){
					if (fi.url.indexOf(forb_folders[fold_tmp]) != -1 && fi.url.indexOf(forb_folders[fold_tmp]) < 2){
						$("#btnQuickEdit").attr("disabled", "true");
						break;
					}
				}

			}
		}
	}else{
		$("#btnQuickEdit").attr("disabled", "true");
		$("#btnEditorEdit").attr("disabled", "true");
	}
}

function getExtension(path){
	var str = path + '';
        var dotP = str.lastIndexOf('.') + 1;
	return str.substr(dotP);
}

function nameWOextension(path){
	var str = path + '';
        var dotP = str.lastIndexOf('.');
	return str.substr(0, dotP);
}

function verzeichnis(path){
	var str = path + '';
        var slash = str.lastIndexOf('/');
	return str.substr(0, slash+1);
}


function dateiname(path){
	var str = path + '';
        var slash = str.lastIndexOf('/');
	return str.substr(slash+1);
}

function deleteCurFileVars(){
	curFilePath = "";
	fpath = "";
	thisIsAFile = false;
}

//TODO setTimeout -> BAD. irgendwie von jqueryFileTree.js expandCallback und collapseCallback rausholen
function tree_refresh(pfad, mode){
	if (pfad != undefined && pfad != "/"){
		var element = $("a[rel='"+ pfad +"']");
		if (mode == "rename"){
			if(element.parent().attr('class') == "directory expanded"){
				element.click();
				var funk = "$('a[rel=\""+ pfad +"\"]').click()";
				setTimeout(funk,500);
			}else{
				fpath = mainPath + $('.selectedTreeElement').attr('rel');
			}
		}else{
			if(element.parent().attr('class') == "directory expanded"){
				element.click();
			}
			var funk = "$('a[rel=\""+ pfad +"\"]').click()";
			setTimeout(funk,400);
		}
	}else{
		$('.jqueryFileTree').unbind();
		$('.jqueryFileTree').remove();
		addFolderTree();
		$('#fileupload .files tbody').html("");
	}
}

//nicht editierbare dateien mit einer farbe markieren
//color_notallow - > farbe nicht erlaubte zugriff
//color_someallow -> etwas erlaubt unter dem ordner
//permissions -> array von erlaubten pfaden
function treeMarkPermissions(permissions, color_notallow, color_someallow){
//    alert(permissions);
    var perm_type;
    color_notallow = (color_notallow == null)?"<?php echo $ne_config_info['jquery_file_tree']['colors']['color_notallow'];?>":color_notallow;
    color_someallow = (color_someallow == null)?"<?php echo $ne_config_info['jquery_file_tree']['colors']['color_someallow'];?>":color_someallow;
    $(".jqueryFileTree").find("a").each(function(){
        perm_type = allowPermission($(this).attr("rel"), permissions);
        if (perm_type == 0){
            $(this).css("color", color_notallow);
        }else if(perm_type == 2){
            $(this).css("color", color_someallow);
        }
    });
}

//pruefen ob zugriff auf datei erlaubt ist, abhaengig von permissions array
//return 0 -> kein zugriff, 1->full zugriff, 2->unterordner/files erlaubt
function allowPermission(path, permissions){
    var retVal = 0;

    //pruefen ob path in permissions bzw obere element drin ist
    $.each(permissions, function(){
        if(path.substr(0,this.length).toLowerCase() == this.toLowerCase()){
                retVal = 1;
                return false;
        }
    });

    //falls nichts gefunden
    if (retVal == 0) {
       //umgekehrt, pruefen ob irgendwas unter dem path erlaubt ist, dann return 2 (dateien unter dem ordner)
        $.each(permissions, function(){
            //falls path von ordner..
            if(path.substr(-1,1) == "/"){
                if(this.substr(0,path.length).toLowerCase() == path.toLowerCase()){
                    retVal = 2;
                    return false;
                }
            }
        });
    }
    return retVal;
};


function treeSelect(relation){
	thisIsAFile = true;
	if(!relation){relation = "/";}
	if(relation.substr(relation.length-1, 1) == "/"){
			thisIsAFile = false;
	}
        curRelation = relation;
	if(thisIsAFile){curFilePath = relation;}
	currentPath = mainPath;
	clearFileInfo();
	fpath = mainPath + relation;
	if(thisIsAFile){loadFileInfo(fpath);}
	currentPath = verzeichnis(fpath);

        $('#txtQuickEdit').attr('disabled', 'true');

	$('#fileupload').fileupload({
		url: 'app/upload.php'+'?folder='+encodeURIComponent(verzeichnis(relation))
	});

	$('#fileupload .files tbody').html("");

	$('.selectedTreeElement').removeClass('selectedTreeElement');
	$("a[rel='"+ relation +"']").addClass('selectedTreeElement');

	$('.action_icons').remove();
	$('.selectedTreeElement').after('<div class="action_icons"></div>');
	if(!thisIsAFile){$('.action_icons').append('<img id="newfolder_icon" title="Unterverzeichnis erstellen" src="<?php echo $ne_config_info['jquery_file_tree']['icons']['newfolder_icon'];?>"/>');}
	$('.action_icons').append('<img id="rename_icon"  title="Umbenennen" src="<?php echo $ne_config_info['jquery_file_tree']['icons']['rename_icon'];?>"/>');
	$('.action_icons').append('<img id="delete_icon"  title="L&ouml;schen" src="<?php echo $ne_config_info['jquery_file_tree']['icons']['delete_icon'];?>"/>');
        if(!thisIsAFile){$('.action_icons').append('<img id="create_new_icon"  title="Eine Datei erstellen" src="<?php echo $ne_config_info['jquery_file_tree']['icons']['create_new_icon'];?>"/>');}
	$('.action_icons img').addClass('action_icon_img');


    $('.action_icons img').each(function(){
        $(this).hover(function(){
            $(this).css('background-color', '#A8C7E6');
        },
        function(){
            $(this).css('background-color', 'transparent');
        });
    });

    //bilder vorschau aktualisieren
    if(thisIsAFile) {
        folderImgPreviewObj.loadOnePic(curRelation);
    }else{
        folderImgPreviewObj.loadByPath(verzeichnis(curRelation));
    }


}



function loadFolderTree(whattodo, var1) {
	folderTreeHtml = "";

	var pfad = var1;
	var rel = relFromFullPath(pfad);
	if (whattodo == "delete"){
		$('.selectedTreeElement').parent().remove();
		$('.selectedTreeElement').removeClass('selectedTreeElement'); //no need?
		if(thisIsAFile){
			$("a[rel='"+ verzeichnis(rel) +"thumb_"+ dateiname(rel)+"']").parent().remove();
		}else{
			treeSelect(relFromFullPath(pfad.substr(0, pfad.length-1)));
		}
		clearFileInfo();
		deleteCurFileVars();

	}else if(whattodo == 'rename' && var1 != ''){
		var newrel = "";
		if (rel.substr(rel.length-1, 1) == "/"){ //einfacher check mit thisIsAFile?
			//ist ein Ordner
			var ordnername = dateiname(rel.substr(0, rel.length - 1));
			$('.selectedTreeElement').text(ordnername);
			newrel = verzeichnis(rel.substr(0, rel.length - 1))+ordnername+'/';
			$('.selectedTreeElement').attr('rel', newrel);
			tree_refresh(newrel, "rename");
		}else{
			//ist eine datei
			$('.selectedTreeElement').text(dateiname(rel));
			newrel = verzeichnis(rel)+dateiname(rel);
			oldrel = $('.selectedTreeElement').attr('rel');
			$('.selectedTreeElement').attr('rel', newrel);
			$("a[rel='"+ verzeichnis(oldrel) +"thumb_"+ dateiname(oldrel)+"']")
				.text('thumb_'+dateiname(rel))
				.attr('rel', verzeichnis(rel)+'thumb_'+dateiname(rel));
			treeSelect(rel);
		}
	}else if(whattodo == 'createSubFolder' && var1 != ''){
		tree_refresh(pfad);
	}else{
		tree_refresh(relFromFullPath(currentPath));
	}
}

function clearFileInfo() {
	$("#thumbImage").html("");
	$("#fileNameTd").html("");
	$("#fileSizeTd").html("");
	$("#fileLastModTd").html("");
	$("#txtFileUrl").val("");
	$("#txtFileThumbUrl").val("");
	$("#btnQuickEdit").attr("disabled", "true");
	$("#btnEditorEdit").attr("disabled", "true");
	$("#txtQuickEdit").val("");
	$("#btnQuickEditSubmit").attr("disabled", "true");
}

function clearVars(){
	currentPath = "";
	fpath = "";
	curFilePath = "";

}

function setPanelScroll() {
	var winHeight = $(window).height();
	var panelHeight = winHeight - 208; //mb TODO
	$("#folderTreePanel").css("max-height", panelHeight + "px");
	$("#fileListPanel").css("max-height", panelHeight + "px");
}

function loadFileInfo(fpath){
	var relfpath = relFromFullPath(fpath);
	if(fileInfoArray[relfpath] == undefined || fileInfoArray[relfpath] == ''){
		$.post("app/file_manager.php", {
					"service": "get_file_info",
					"file_path": fpath
				}, function(data){
					fileInfoArray[relfpath] = JSON.parse(data);
					getFileInfoCallback(data)});
	}else{
		getFileInfoCallback(JSON.stringify(fileInfoArray[relfpath]));
	}
}

function addFolderTree(){

	$('#folderTreePanel').fileTree({ root: '/',
            loadCallBack: function(){treeMarkPermissions(gUserPermissionsArray)},
            checkPermFunc: function(relation){return allowPermission(relation, gUserPermissionsArray) == 0 ? false : true;}

            },
            function(file, folder) {
		var rel = "";
		if (file != null){
			thisIsAFile = true;
			rel = file;
		}else if(folder != null){
			thisIsAFile = false;
			rel = folder;
		}

		treeSelect(rel);


	});

}

function relFromFullPath(fullPath){
	if(fullPath != undefined || fullPath != null){
		var curPath = fullPath;
		return curPath.substring(mainPath.length, curPath.length)
	}
}

function createFolder(path){
	var folderName = filterSymbols(prompt("Verzeichnisname?"));
	if(folderName != "" && folderName != null) {
		$.post("app/file_manager.php", {
			"service": "create_subfolder",
			"current_path": path,
			"new_subfolder_name": folderName
		}, function(resp) {
			if(resp == "0") {
				alert("Fehler bei der Erstellung des Verzeichnises; Bitte versuchen Sie es noch einmal!");
			} else {
				loadFolderTree('createSubFolder', relFromFullPath(path));
			}
		});
	}
}

function createNewFile(path, file_name, file_ext){
    if(file_ext != "" && file_ext != null && path != null) {
        $.post("app/file_manager.php", {
			"service": "create_new_file",
			"current_path": path,
			"new_file_name": file_name,
                        "extension": file_ext
		}, function(resp) {
			if(resp == "0") {
				alert("Fehler bei der Erstellung der Datei; Bitte versuchen Sie es noch einmal!");
			}else if(resp == "1"){
                            tree_refresh(relFromFullPath(path));
                        } else {
                            alert(resp);
			}
		});
    }
}

function filterSymbols(string){
    var filteredString = string;
    var find = $.parseJSON('<?php echo(json_encode($ne_config_info['symbols_being_replaced'])); ?>');
    var replace = $.parseJSON('<?php echo(json_encode($ne_config_info['symbols_replacement'])); ?>');
    var regex;
    for (var i = 0; i < find.length; i++) {
        regex = new RegExp(find[i], "g");
        filteredString = filteredString.replace(regex, replace[i]);
    }
    //regex = new RegExp('<?php echo($ne_config_info['regex_removed_symbols']); ?>', "g");
    filteredString = filteredString.replace(<?php echo($ne_config_info['regex_removed_symbols']); ?>g, "");
    return filteredString;
}

$(document).ready(function() {

    //load fileUpload widget..
 //    $('#fileupload').fileupload({
 //    // limitMultiFileUploads: 1,
 //    // sequentialUploads: true
	// })
	// .bind('fileuploadstop', function (e, data) {
	// 		alert('DONE');
	// 		loadFolderTree();
	// 		clearFileInfo();
	// });

    //load folder tree..
	addFolderTree();

    //load image preview..
    folderImgPreviewObj = new folderImgPreview({
        loadMessage: "Loading...",
        ImgPrevContainer: $("#folderImagesPreview"),

        loadedCallback: function(path){
            //callback function test
            var headcnt = this.headContainer;
            var children = headcnt.children();
            children.fadeOut(100, function(){
                headcnt.find("h4").remove();
                headcnt.prepend('<h4 style="text-align: center;">Pfad: "'+path+'"</h4>');
                setTimeout(function(){
                    headcnt.find("h4").fadeOut(100, function(){
                    headcnt.find("h4").remove();
                    children.fadeIn(300);
                });}
                , 200);
            });
        },
        clickPicCallback: function(){
//            alert($(this).find('IMG').attr('src'));
            treeSelect($(this).find('IMG').attr('src'));
        }

    });

    $("#newfolder_icon_main").bind('click', function(){
        //neue ordner nur falls full zugriff
        if(allowPermission("/", gUserPermissionsArray) != 1){
            alert(unescape("Kein zugriff"));
            return;
        }
		createFolder(mainPath+"/");
	})

    $("#create_new_icon_main").bind('click', function(){
        if(allowPermission("/", gUserPermissionsArray) != 1){
            alert(unescape("Kein zugriff"));
            return;
        }
        dlg_path = "main";
        $( "#dialog-form" ).dialog( "open" );
	});

	$("#newfolder_icon").live('click', function() {

		if(currentPath == "" || fpath.substr(fpath.length-1, 1) != "/") {
			alert("Bitte auf Zielverzeichnis klicken!");
			return;
		}
                //neue ordner nur falls full zugriff
                if(allowPermission(curRelation, gUserPermissionsArray) != 1){
                    alert(unescape("Kein zugriff"));
                    return;
                }

		createFolder(currentPath);


	});



	$("#rename_icon").live('click', function() {
		if(currentPath == "") {
			alert(unescape("Bitte eine Datei oder Verzeichnis w%E4hlen!"));
			return;
		}
        //umbenennen nur falls full zugriff
        if(allowPermission(curRelation, gUserPermissionsArray) != 1){
            alert(unescape("Kein zugriff"));
            return;
        }
		var curName = $('.selectedTreeElement').text();
		var path_rename = "";
		if(fpath.substr(fpath.length-1, 1) == "/"){
			var newName = prompt("Neuer Name des Verzeichnises:", curName);
			var ext = "/";
			path_rename = fpath.substr(0, fpath.length-1);
		}else{
			var newName = prompt("Neuer Name der Datei (ohne Erweiterung!)", nameWOextension(curName));
			var ext = '.'+getExtension(fpath);
			path_rename = fpath;
		}
        //symbole filtern
        newName = filterSymbols(newName);
		if(newName != "" && newName != null) {
			$.post("app/file_manager.php", {
				"service": "rename",
				"current_path": path_rename,
				"new_name": newName
			}, function(resp) {
				if(resp == "0") {
					alert("Fehler beim Umbenennen, versuchen Sie es noch einmal!");
				} else {
					loadFolderTree('rename', verzeichnis(path_rename)+newName+ext);

				}
			});
		}
	});

	$("#delete_icon").live('click', function() {
		if(currentPath == "") {
			alert(unescape("Bitte eine Datei oder Verzeichnis w%E4hlen!"));
			return;
		}

        //loeschen nur falls full zugriff
        if(allowPermission(curRelation, gUserPermissionsArray) != 1){
            alert(unescape("Kein zugriff"));
            return;
        }

		if(fpath.substr(fpath.length-1, 1) == "/"){
			if(confirm(unescape("CAUTION! Sind Sie wirklich sicher, das GANZE verzeichnis zu l%F6schen?"))) {
				$.post("app/file_manager.php", {
					"service": "delete_folder",
					"folder": currentPath
				}, function(resp) {
					alert(resp);
					loadFolderTree("delete", currentPath);
				});
			}
		}else{
			if(!window.confirm(unescape("Sind Sie sicher, diese Datei zu l%F6schen?"))) {
				return;
			}
			$.post("app/file_manager.php", {
				"service": "delete_file",
				"file_path": fpath
			}, function() {
				clearFileInfo();
				loadFolderTree("delete", fpath);
			});
		}
	});

    //dialog vorbereiten
    $("#dialog-form #buttons").buttonset();
    $( "#dialog-form" ).dialog({
        show: 'drop',
        hide: 'drop',
        autoOpen: false,
        height: 300,
        width: 350,
        modal: true,
        buttons: {
            "Ok": function() {
                                (dlg_path != "main") ? dlg_path = currentPath : dlg_path = mainPath + "/";
                                file_name = filterSymbols($("#dialog-form #dateinameinput").val());
                                file_ext = $("#dialog-form input[name=buttons]:checked").attr('id');
                                createNewFile(dlg_path, file_name, file_ext);
                                $( this ).dialog( "close" );
            },
            Cancel: function() {
                                $( this ).dialog( "close" );
            }
        },
        close: function() {
                        $(this).find("#dateinameinput").val('');
        },
        open: function() {
                        $(this).find("input[name=buttons],:button").blur(); //firefox dont blur button ?
        }
    });


    $("#dialog-form input[name=buttons]").bind('click', function() {
        if($("#dialog-form input[name=buttons]:checked").attr('id') == 'htaccess'){
            $('#dialog-form #datei_name_input_field').fadeOut(300);
        }else{
            $('#dialog-form #datei_name_input_field').fadeIn(300);
        }
    });

    $("#create_new_icon").live('click', function() {
        if(currentPath == "") {
            alert(unescape("Bitte eine Datei oder Verzeichnis w%E4hlen!"));
            return;
        }
        //neue datei erstellen nur falls full zugriff
        if(allowPermission(curRelation, gUserPermissionsArray) != 1){
            alert(unescape("Kein zugriff"));
            return;
        }

        $( "#dialog-form" ).dialog( "open" );

	});

	$("#txtFileUrl").mouseover(function() {
		$(this).select();
	});

	$("#txtFileThumbUrl").mouseover(function() {
		$(this).select();
	});

	$("#btnQuickEdit").click(function() {
		if(currentPath == "") {
			return;
		}
		var check = confirm(unescape("Achtung: Die Berabeitung im Quelltext erm%F6glicht Modifikationen an der Seitenstruktur.\nFehler k%F6nnen die Seite zerst%F6ren. Bitte benutzen Sie diese Option nur \nbei ausreichenden HTML-Kenntnissen auf eigene Gefahr."));
		if (check == true){
			$.post("app/file_manager.php", {
				"service": "load_file_content",
				"file_path": $("#txtFileUrl").val()
			}, function(resp) {
				var rdata = resp.replace(/<comment_ssi>/g, "<!-" + "-#");
				rdata = rdata.replace(/<comment>/g, "<!-" + "-");
				rdata = rdata.replace(/<\/comment>/g, "-" + "->");
				$("#txtQuickEdit").val(rdata);
				$("#btnQuickEditSubmit").removeAttr("disabled");
                                $("#txtQuickEdit").removeAttr("disabled");
			})
		}
	});

	$("#btnEditorEdit").click(function() {
		if(currentPath == "") {
			return;
		}
		//alert("nav_editor.php?path="+escape(curFilePath));
		window.location.href = "nav_editor.php?path="+escape(curFilePath);
	});


	$("#btnQuickEditSubmit").click(function() {
		if(currentPath == "") {
			return;
		}
		if(!confirm("Sind Sie sicher?")) {
			return;
		}
		var cnt = $("#txtQuickEdit").val();
		cnt = cnt.replace(new RegExp("<!-" + "-#", "g"), "<comment_ssi>");
		cnt = cnt.replace(/<!--/g, "<comment>");
		cnt = cnt.replace(/-->/g, "</comment>");
		$.post("app/file_manager.php", {
			"service": "save_file_content",
			"file_path": $("#txtFileUrl").val(),
			"new_content": cnt
		}, function(resp) {
			alert(resp);
		});
	});

	$(window).resize(function() {
		setPanelScroll();
	});


    $("#folderImagesPreview_accordion").accordion({
        active: false,
        collapsible: true,
        navigation: true,
        autoHeight: false
//        icons: {
//            'header': 'ui-icon-plus',
//            'headerSelected': 'ui-icon-minus'
//        }
    });

	setPanelScroll();
	clearFileInfo();


});