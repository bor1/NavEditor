<?php
    require_once('auth.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Seite und Navigation - <?php echo($ne_config_info['app_titleplain']); ?></title>

    <?php
    echo NavTools::includeHtml(
            'default',
            'json2.js',
            'tinymce/tinymce.min.js',
            'handlebars.js',
            'naveditor2.js');
    ?>
    <script type="text/javascript">

        function globaleFunktion() {
            console.log("Hello World!");
        }

        (function () {

            $(document).ready(function() {

                var menue_source   = $("#menue-template").html(),
                     menue_template = Handlebars.compile(menue_source);

                $(".popover-container > a").click(function() {
                    var $this = $(this);

                    $this.siblings(".hover-popover").show();

                });

                $(".hover-popover .dismiss").click(function() {
                    $(this).closest(".hover-popover").hide();
                });

                // Prompt
                $(".btn-prompt").click(function(evt) {
                    var $this = $(this),
                        prompt = $this.data("prompt"),
                        callback = $this.data("callback"),
                        $title = $this.find(".title"),
                        title_temp = $title.html(),
                        $controls = $('<span class="controls" />'),
                        buttons = $this.data("prompt-buttons").split("|"),
                        icons = $this.data("prompt-icons").split("|"),
                        width_init = $this.width(),
                        width_text_change = 0,
                        width_final = 0,
                        clean_up = function() {
                            $this.removeClass("open");
                            $controls.remove();
                            $title.html(title_temp);
                            $this.animate({
                                width: width_init
                            }, {
                                easing: "linear",
                                duration: 150,

                                complete: function() {
                                    $this.css("width", "");
                                }
                            });
                            $(".full-screen-popover").hide();
                            $this.css("z-index", "10");
                        };


                    if(prompt && prompt != undefined && !$this.hasClass("open")) {
                        // Button besitzt ein prompt.

                        $.each(buttons, function(i, button){
                            button = $('<a href="javascript:void(0);" data-id="'+i+'"> '+button+'</a>');
                            if( icons[i] && icons[i] != "" ) {
                                button.prepend( $('<i class="icon-white icon-'+icons[i]+'"></i>') );
                            }

                            button.click(function() {
                                window[callback](i);
                                clean_up();
                                return false;
                            });

                            $controls.append( button );

                        });

                        $this.css("z-index", "100001");

                        $title.html(prompt);
                        width_text_change = $this.width();


                        $this.addClass("open");
                        $this.append($controls);

                        width_final = $this.width();

                        $title.html(title_temp);
                        $this.removeClass("open");


                        $this.css("width", width_init);
                        $controls.hide();

                        $this.animate({
                            width: width_final - 22
                        }, {
                            easing: "linear",
                            duration: 200,

                            step: function(now, fx) {
                                if(now > width_text_change) {
                                    $title.html(prompt);
                                }
                            },

                            complete: function() {
                                $controls.show();
                                $this.addClass("open");
                                $this.css("width", width_final);
                                $(".full-screen-popover").show();
                            }
                        });
                    }

                });


                $.get("app/load_tree_data.php?r=" + Math.random(), {},
                function(content) {
                    var data = JSON.parse(content);

                    navTreeOper.refreshNavTree(data);

                    $.each(data, function(id, item) {
                        var title = '';

                        switch(id) {
                            case 'A':
                                // Hauptmenue
                                title = "Hauptmenü";
                                break;
                            case 'Z':
                                // Optionales Zielgruppenmenue
                                title = "Optionales Zielgruppenmenü";
                                break;
                            case 'S':
                                // Technisches Menue
                                title = "Technisches Menü";
                                break;

                            default:
                                title = "Unbekannt";
                                break;
                        }

                        item = navTreeOper._cloneObject(item);
                        navTreeOper.addPathInfo(item);

                        $("#menue-sidebar").append(menue_template({ title: title, data: item }));
                    });

                    $("#menue-sidebar .menue-folder ul").hide();

                    $("#menue-sidebar .menue-folder a").click(function() {
                        $(this).parent().find("ul").show();
                    });


                    $("#menue-sidebar a").click(function() {
                        var $this = $(this),
                            title = $this.html(),
                            key = $this.data("key"),
                            data = { path: $this.data("path") };

                        $.post("app/update_contents.php?r=" + Math.random(), {
                            "json_oper": "get_content",
                            "json_data": JSON.stringify(data)
                        }, function(content) {

                            content = JSON.parse(content);

                            if(content) {

                                $("#menue-sidebar .active").removeClass("active");

                                $this.parent().addClass("active");

                                $("#file-title").html(title);


                                if(!content.is_locked) {

//                                    console.log(content.content_html);

                                    content.content_html = content.content_html.replace(/<comment_ssi>/g, "<!-" + "-#");
                                    content.content_html = content.content_html.replace(/<comment>/g, "<!-" + "-");
                                    content.content_html = content.content_html.replace(/<\/comment>/g, "-" + "->");

                                    tinymce.activeEditor.setContent(content.content_html);
                                }

                            }

                        });

                        $.post("app/update_contents.php?r=" + Math.random(), {
                            "json_oper": "get_backup_list",
                            "json_data": JSON.stringify(data)
                        }, function(content) {

                            content = JSON.parse(content);

                            if(content) {

//                                console.log(content);

                            }

                        });
                    });


                });



                // $(".btn-prompt").each(function() {
                //     var $this = $(this);

                //     $this.css("width", $this.width());
                //     console.log(this);
                // });


                tinyMCE.init({
                    forced_root_block : '',
                    mode: "textareas",
                    language: "de",
                    theme: "modern",
                    skin: "light",
                    plugins: "image link code table preview mitarbeiter feedimport ssiInclude image_choose osm",
                    menubar: false,
                    toolbar1: "undo redo | cut copy paste | link image table | mitarbeiter | feedimport | osm | code | preview",
                    toolbar2: "fontselect fontsizeselect | styleselect | alignleft aligncenter alignright alignjustify | outdent indent | bold italic underline strikethrough | bullist numlist",
                    //theme: "advanced",
                    //language: "de",
                    //skin: "o2k7",
                    relative_urls: false,
                    convert_urls: false,
                    //plugins: "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                    theme_advanced_styles: "infologo",
                    theme_advanced_toolbar_location: "top",
                    theme_advanced_toolbar_align: "left",
                    theme_advanced_statusbar_location: "bottom"
                });


            });
        })();

    </script>

    <script id="menue-template" type="text/x-handlebars-template">


        <h5>{{ title }}</h5>

        <ul class="menue-list">
            {{#data}}
                {{#if child}}
                    <li class="menue-folder">
                        <a href="javascript: void(0);" data-path="{{path}}" data-key="{{key}}">{{title}}</a>
                        <ul>
                            {{#child}}
                                 {{#if child}}
                                    <li class="menue-folder">
                                        <a href="javascript: void(0);">{{title}}</a>
                                        <ul>
                                            {{#child}}
                                                 <li class="menue-file"><a href="javascript: void(0);" data-path="{{path}}" data-key="{{key}}">{{title}}</a></li>
                                             {{/child}}
                                         </ul>
                                    </li>
                                {{else}}
                                    <li class="menue-file"><a href="javascript: void(0);" data-path="{{path}}" data-key="{{key}}">{{title}}</a></li>
                                {{/if}}
                             {{/child}}
                         </ul>
                    </li>
                {{else}}
                    <li class="menue-file"><a href="javascript: void(0);" data-path="{{path}}"  data-key="{{key}}">{{title}}</a></li>
                {{/if}}
            {{/data}}
        </ul>


    </script>

</head>

<body id="bd_Logo">

    <?php require('common_nav_menu.php'); ?>

    <div class="full-screen-popover">
        test

    </div>

    <div class="container" id="wrapper">


        <div class="page-header">
            <h2 id="page-title" class="page-header">Seite und Navigation <small>Bearbeiten Sie hier Ihre Internetpräsenz</small></h2>
        </div>
        <!--=======================================================================================-->
        <!--================================== Conten ab hier =====================================-->
        <!--=======================================================================================-->

        <div class="row">

            <!--==== Menue ====-->
            <div class="col-md-3">
                <div id="menue-sidebar">

                </div>

                <hr style="border-top: solid 1px #ccc; border-bottom: solid 1px transparent;">

                <span class="btn btn-prompt btn-light btn-success" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Nein|Abbrechen" data-prompt-icons="ok|remove|" data-callback="globaleFunktion">
                    <i class="glyphicon glyphicon-ok"></i>
                    <span class="title"> Speichern</span>
                </span>
            </div>
            <!--==== Menue Ende ====-->

            <!--==== Hauptteil ====-->
            <div class="col-md-9 page" >

                 <div class="page-header">
                    <h4 id="file-title" class="page-header"><small class="path"></small>Startseite</h4>

                    <div class="pull-right">

                        <span class="btn btn-prompt btn-light btn-success" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Nein|Abbrechen" data-prompt-icons="ok|remove|" data-callback="globaleFunktion">
                            <i class="glyphicon glyphicon-ok"></i>
                            <span class="title"> Speichern</span>
                        </span>

                        <span class="btn btn-prompt btn-light btn-danger" data-prompt="Sind Sie sicher?" data-prompt-buttons="Ja|Nein" data-prompt-icons="ok|remove"  data-callback="globaleFunktion">
                            <i class="glyphicon glyphicon-remove"></i>
                            <span class="title"> Seite l&ouml;schen</span>
                        </span>

                    </div>
                </div>

                <div class="tabbable"> <!-- Only required for left/right tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom">
                        <li class="active"><a href="#edit" data-toggle="tab"><i class="icon-pencil"></i> Seite bearbeiten</a></li>
                        <li><a href="#options" data-toggle="tab"><i class="icon-list-alt"></i> Optionen</a></li>
                        <li><a href="#recover" data-toggle="tab"><i class="icon-hdd"></i> Wiederherstellen</a></li>
                      </ul>
                      <div class="tab-content">
                        <div class="tab-pane active" id="edit">
                            <textarea class="padding-top" name="txtContent" cols="160" rows="25" ></textarea>
                        </div>

                        <div class="tab-pane" id="options">
                            <form class="form-horizontal">
                              <div class="form-group">
                                <label class="col-sm-2 control-label " for="inputTitel">Titel mit HTML</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputTitel" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputAlias">Alias</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputAlias" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputInfotext">Infotext</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputInfotext" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputUrl">URL</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputUrl" class="form-control" placeholder="">
                                </div>
                            </div>
                            <hr style="border-top: solid 1px #ccc; border-bottom: solid 1px transparent;">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputIcon">Icon</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputIcon" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputIconAlt">Icon Alt Text</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputIconAlt" class="form-control" placeholder="">
                                        </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="inputIconTitle">Icon Titel</label>
                                <div class="col-sm-10 controls">
                                    <input type="text" id="inputIconTitle" class="form-control" placeholder="">
                                </div>
                            </div>
                            </form>
                        </div>


                        <div class="tab-pane" id="recover">
                              <table class="table">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th style="width:100px">Datum</th>
                                  <th>Uhrzeit</th>
                                  <th class="span5"></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>28.8.2013</td>
                                  <td>16:15</td>
                                  <td>
                                      <span class="btn btn-prompt btn-primary btn-light" data-prompt="Sind Sie sicher?" data-prompt-buttons="Ja|Abbrechen" data-prompt-icons="ok|remove" data-callback="globaleFunktion">
                                        <i class="icon-ok icon-white"></i>
                                        <span class="title"> Wiederherstellen</span>
                                    </span>
                                  </td>
                                </tr>
                                <tr>
                                  <td>2</td>
                                  <td>28.8.2013</td>
                                  <td>16:15</td>
                                  <td>
                                      <span class="btn btn-prompt btn-primary btn-light" data-prompt="Sind Sie sicher?" data-prompt-buttons="Ja|Abbrechen" data-prompt-icons="ok|remove" data-callback="globaleFunktion">
                                        <i class="icon-ok icon-white"></i>
                                        <span class="title"> Wiederherstellen</span>
                                    </span>
                                  </td>
                                </tr>
                                <tr>
                                  <td>3</td>
                                  <td>28.8.2013</td>
                                  <td>16:15</td>
                                  <td>
                                      <span class="btn btn-prompt btn-primary btn-light" data-prompt="Seite publizieren?" data-prompt-buttons="Ja|Abbrechen" data-prompt-icons="ok|remove" data-callback="globaleFunktion">
                                        <i class="icon-ok icon-white"></i>
                                        <span class="title"> Wiederherstellen</span>
                                    </span>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                      </div>
                </div>
            </div>
            <!--==== Hauptteil Ende ====-->

        </div>

        <!--=======================================================================================-->
        <!--================================== Conten endet hier ==================================-->
        <!--=======================================================================================-->


    </div>

<?php require('common_footer.php'); ?>

</body>

</html>
