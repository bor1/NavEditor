/**
 * @author Dmitry Gorelenkov
 * @requires jQuery, Bootstrap(Modal)
 * @description Adds help link in navigation by NavEditor<br>
 * by clicking on the link, current page help will be loaded as Bootstrap-modal
 */
$(function(){
    //help menue drop down
    var helpMenue = $('nav a:contains("Hilfe")').parent();

    //id of new help link
    var dynHelpId = 'dyn_help_link';
    //new help link in navigation
    var newHelpLinkHtml = $('<li>').html($("<a>")
            .attr('href','javascript:;')
            .attr('id',dynHelpId)
            .html('Diese Seite'));

    //current page name TODO better name
    var pageName = window.location.pathname.match(/[^\/]+$/)[0]; //file.php

    //modal HTML
    var helpPopoverHtml = $.parseHTML(
            '<div id="HelpModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Hilfe" aria-hidden="true">' +
                '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>' +
                    '<h3 id="myModalLabel">'+pageName + ' - Hilfe'+'</h3>' +
                '</div>' +
                '<div class="modal-body">' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>' +
                '</div>' +
            '</div>'
            );

    //add modal html to the page
    $('body').append(helpPopoverHtml);

    //add help link
    helpMenue.find('.dropdown-menu')
            .append($('<li>').html('--------'))
            .append(newHelpLinkHtml);

    //on click on the new help link
    helpMenue.on('click', '#'+dynHelpId, function(){
        var content = getHelpContent();
        //load if no help content yet
        if (!content) {
            loadAjaxContent();
        }
        //show modal
        $(helpPopoverHtml).modal('show');

    });

    /**
     * loads content from PHP to help container
     */
    var loadAjaxContent = function(){
        $.get("app/get_help.php?r=" + Math.random(), {
                "page_name": window.location.pathname.match(/([^\/]+)(?=\.\w+$)/)[0] //file name of current url
            }, setContent);
    };

    /**
     * sets content of help container
     * @param {String} content can be HTML content, if empty, sets "No help found" message
     */
    var setContent = function(content) {
        content = content || "No help found";
        $(helpPopoverHtml).find('.modal-body').html(content);
    };

    /**
     * getter for current loaded help-container content
     * @returns {String} content of help container
     */
    var getHelpContent = function(){
        return $(helpPopoverHtml).find('.modal-body').html();
    };
});