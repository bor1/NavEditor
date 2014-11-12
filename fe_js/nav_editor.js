
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
