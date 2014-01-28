/**
 * @author Dmitry Gorelenkov
 * @requires jQuery, Bootstrap(Modal)
 * @description Adds help link in navigation by NavEditor<br>
 * by clicking on the link, current page help will be loaded as Bootstrap-modal
 */
var Help = {};
//Initialize Help object only after document loaded
$(function(){
    Help = new function(){
        var self = this;
        var helpPageName = '';

        //help menue drop down
        var helpMenue = $('nav a:contains("Hilfe")').parent();

        //id of new help link
        var dynHelpId = 'dyn_help_link';
        //id of modal
        var modalId = 'HelpModal';
        //id of modal Label
        var modalLabelId = 'helpModalLabel';
        //new help link in navigation
        var newHelpLinkHtml = $('<li>').html($("<a>")
                .attr('href','javascript:;')
                .attr('id',dynHelpId)
                .html('Diese Seite'));

        //modal HTML
        var helpPopoverHtml = $.parseHTML(
                '<div id="'+modalId+'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Hilfe" aria-hidden="true">' +
                    '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>' +
                        '<h3 id="'+modalLabelId+'"></h3>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    '</div>' +
                    '<div class="modal-footer">' +
                        '<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>' +
                    '</div>' +
                '</div>'
                );

        /**
         * loads content from PHP to help container
         * @public
         */
        this.loadAjaxContent = function(){
            $.get("app/get_help.php?r=" + Math.random(), {
                    "page_name": self.getHelpPageName() //file name of current url
                }, self.setContent);
        };

        /**
         * sets content of help container
         * @public
         * @param {String} content can be HTML content, if empty, sets "No help found" message
         */
        this.setContent = function(content) {
            content = content || "No help found";
            $(helpPopoverHtml).find('.modal-body').html(content);
        };

        /**
         * getter for current loaded help-container content
         * @public
         * @returns {String} content of help container
         */
        this.getHelpContent = function(){
            return $(helpPopoverHtml).find('.modal-body').html();
        };

        /**
         * @public
         * @returns {String} current helpPageName if set, otherwiese default filename depending on path
         */
        this.getHelpPageName = function(){
            return helpPageName || window.location.pathname.match(/([^\/]+)(?=\.\w+$)/)[0];
        };

        /**
         * sets current help file name
         * @public
         * @param {String} sName helpPageName wihtout extension
         */
        this.setHelpPageName = function(sName){
            helpPageName = sName || '';
            $('#'+modalLabelId).html(helpPageName + ' - Hilfe');
        };

        /**
         * @public
         * @returns {jQuery} modal jQuery object
         */
        this.getModal = function(){
            return $(helpPopoverHtml);
        }



        //add modal html to the page
        $('body').append(helpPopoverHtml);

        //add help link
        helpMenue.find('.dropdown-menu')
                .append($('<li>').html('--------'))
                .append(newHelpLinkHtml);

        //on click on the new help link
        helpMenue.on('click', '#'+dynHelpId, function(){
            var content = self.getHelpContent();
            //load if no help content yet
            if (!content) {
                self.loadAjaxContent();
            }
            //show modal
            $(helpPopoverHtml).modal('show');
        });

        //set current help page default name TODO better name
        self.setHelpPageName(window.location.pathname.match(/[^\/]+$/)[0]); //e.g. nav_editor.php

    };
});