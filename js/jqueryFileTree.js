// jQuery File Tree Plugin
//
// Version 2.00
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// Visit http://abeautifulsite.net/notebook.php?article=58 for more information
//
// Usage: $('.fileTreeDemo').fileTree(options);
// Usage: var fileTree = new $('.fileTreeDemo').fileTree(options);
//
// function fired after click any link
// callback: function(path, isFile);
//   isFile: true for file, false for folder
//   path  : relative path of selected element
//
// Options:  root           - root folder to display; default = /
//           script         - location of the serverside AJAX file to use; default = jqueryFileTree.php
//           folderEvent    - event to trigger expand/collapse; default = click
//           expandSpeed    - default = 500 (ms); use -1 for no animation
//           collapseSpeed  - default = 500 (ms); use -1 for no animation
//           expandEasing   - easing function to use on expand (optional)
//           collapseEasing - easing function to use on collapse (optional)
//           multiFolder    - whether or not to limit the browser to one subfolder at a time
//           loadMessage    - Message to display while initial tree loads (can be HTML)
//           loadCallBack   - Function after tree is loaded
//           permissions    - User permissions for the files
//           checkPermFunc(current_relative_path)   - if returns false, dont allow to open tree/show file.
//           selectCallBack(path, isFile)           - Function after select a link
//               isFile: true for file, false for folder
//               path  : relative path of selected element
//           triggerDelay   - Delay for recursive call of opening tree (to apply all callbacks after open, than next recursive call); default = 100 (ms)
//           showRoot       - Show root dir ('/') on the top; true/false; default = false
//
// History:
// 2.00 - made Object based (December 2013) by Dmitry.
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// TERMS OF USE
//
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC.
//
// Modified by Dmitry Gorelenkov
// New functions like "openPath"
// $('.fileTreeDemo').data("openPath","/path/here/");
// or
// var fileTree = new $('.fileTreeDemo').fileTree(options);
// fileTree.openPath("/path/here.txt");



if(jQuery) (function($){


    /**
     * FileTree Object
     * @class FileTree
     * @param {Object} settings
     * @property {Object} fileInfoArray array for all cached file info, in opened tree
     * @returns {FileTree}
     */
    var FileTree = function(settings){

        /*
         * Reference for itself for private funcs
         * @type FileTree
         */
        var self = this;


        /**
         * array for all cached file info, in opened tree
         * @public
         * @type Object
         */
        this.fileInfoArray = {};


        /**
         * jQuery placeholder for FileTree
         * @public
         * @type Object
         */
        this.boundTo = {};

        /**
         * Options for this FileTree instance
         * @public
         * @type Object
         */
        this.o = {};




        /**
         * opens path, if already loaded/exists/visible
         * @public
         * @param {String} path
         * @returns {Boolean} Success
         */
        this.openPathIfFound = function(path){

            if (path === "/") {
                this.refreshTree();
                return true;
            }


            try{
                var element = this.boundTo.find("[rel='"+path+"']").first();
                if (element.length < 1){return false;}

                //if folder
                if(path.substr(-1,1) === "/"){
                    openFolder(element);
                //else if file
                }else{
                    element.trigger(this.o.folderEvent);
                }

            }catch(e){
                console.log(e);
                return false;
            }

            return true;
        };

        /**
         * refresh, try to reopen the path
         * @public
         * @param {String} path path to refresh
         */
        this.refreshPath = function(path){
            if (path === "/") {
                this.refreshTree();
                return;
            }

            var objToOperate = $(".jqueryFileTree [rel='"+ path +"']");
            closeFolder(objToOperate, function(){openFolder(objToOperate);});
        };

        /**
         * refresh the whole tree (rebuild)
         */
        this.refreshTree = function(){
            build(self.boundTo, self.o);
        };


        /**
         * opens any path, file or directory (try to find it)
         * @public
         * @param {String} path path that have to be opened
         * @type Boolean
         */
        this.openPath = function(path){
            var aPathSplitted = getSplittedPath(path);


            //if elements found, start recursion
            if(aPathSplitted.length > 0){
                //try to open, if possible
                if (this.openPathIfFound(aPathSplitted[aPathSplitted.length-1])) {
                    return true;
                }

                return openPathHelperRecursive(aPathSplitted, 0);
            }

            return false;
        };

        /**
         * @constructor
         * @param {Object} src_obj jQuery html container
         * @param {Object} settings
         * @returns {FileTree} has jQuery properties of bounded object
         */
        function build(src_obj, settings) {
            self.boundTo = src_obj;

            // Defaults
            if( !settings ) var settings = {};
            self.o.root             = ( settings.root !== undefined )            ?settings.root :            '/';
            self.o.script           = ( settings.script !== undefined )          ?settings.script :          'app/jqueryFileTree.php';
            self.o.folderEvent      = ( settings.folderEvent !== undefined )     ?settings.folderEvent :     'click';
            self.o.expandSpeed      = ( settings.expandSpeed !== undefined )     ?settings.expandSpeed :     500;
            self.o.collapseSpeed    = ( settings.collapseSpeed !== undefined )   ?settings.collapseSpeed :   500;
            self.o.expandEasing     = ( settings.expandEasing !== undefined )    ?settings.expandEasing :    null;
            self.o.collapseEasing   = ( settings.collapseEasing !== undefined )  ?settings.collapseEasing :  null;
            self.o.multiFolder      = ( settings.multiFolder !== undefined )     ?settings.multiFolder :     true;
            self.o.loadMessage      = ( settings.loadMessage !== undefined )     ?settings.loadMessage :     'Loading...';
            self.o.expandCallBack   = ( settings.expandCallBack !== undefined )  ?settings.expandCallBack :  function(){};
            self.o.collapseCallBack = ( settings.collapseCallBack !== undefined )?settings.collapseCallBack :function(){};
            self.o.loadCallBack     = ( settings.loadCallBack !== undefined )    ?settings.loadCallBack :    function(){};
            self.o.selectCallBack   = ( settings.selectCallBack !== undefined )  ?settings.selectCallBack :  function(){};
            self.o.permissions      = ( settings.permissions !== undefined )     ?settings.permissions :     '0';
            self.o.checkPermFunc    = ( settings.checkPermFunc !== undefined )   ?settings.checkPermFunc :   function(){return true;};
            self.o.triggerDelay     = ( settings.triggerDelay !== undefined )    ?settings.triggerDelay :    100;
            self.o.showRoot         = ( settings.showRoot !== undefined )        ?settings.showRoot :        false;


            self.boundTo.each( function() {
                //function per data object
                self.boundTo.data('openPathIfFound', function(path){
                    self.openPathIfFound(path);
                });

                self.boundTo.data('openPath', function(path){
                    self.openPath(path);
                });

                self.boundTo.data('refreshPath', function(path){
                    self.refreshPath(path);
                });

                self.boundTo.data('refreshTree', function(){
                    self.refreshTree();
                });


                // Loading message
                self.boundTo.html('<ul class="jqueryFileTree start"><li class="wait">' + self.o.loadMessage + '<li></ul>');
                // Get the initial file list
                showTree( self.boundTo, escape(self.o.root) );

            });

            self.o.loadCallBack();

            return true;
        };

        /**
         * requests ajax data, shows new tree
         * @private
         * @param {Object} jQObj jquery selected object/s
         * @param {String} sPath start path
         * @returns {undefined}
         */
        function showTree(jQObj, sPath) {
            $(jQObj).addClass('wait');
            $(".jqueryFileTree.start").remove();
            $.post(self.o.script, {
                dir: sPath,
                permissions: self.o.permissions
            }, function(data) {
                $(jQObj).find('.start').html('');
                $(jQObj).removeClass('wait').append(JSON.parse(data).html);

                //add to object
                for(var property in JSON.parse(data).filesinfo){
                    var dataTmp = JSON.parse(data);
                    self.fileInfoArray[dataTmp.filesinfo[property].url] = dataTmp.filesinfo[property]; //TODO
                }

                if( self.o.root === sPath ){
                    //onyl if options.showRoot set
                    if(self.o.showRoot){
                        //add root folder
                        var rootDir = "<ul class='jqueryFileTree'><li class='directory expanded'><a rel='/' href='#'>/</a></li></ul>";
                        var treeBefore = $(jQObj).find('UL:hidden');
                        $(jQObj).prepend(rootDir).find('LI').first().append(treeBefore);
                    }

                    //show all
                    $(jQObj).find('UL:hidden').show();
                } else{
                    $(jQObj).find('UL:hidden').slideDown(self.o.expandSpeed, self.o.expandEasing,self.o.expandCallBack);
                }
                bindTree(jQObj);
            });
        }

        /**
         * binds tree with events for each tree element
         * @private
         * @param {Object} t jQuery element of the tree
         */
        function bindTree(t) {
            $(t).find('LI A').bind(self.o.folderEvent, function() {
                var isFile;
                var sPath;

                sPath = $(this).attr('rel');

                //no open tree event for root
                if (sPath === '/') {
                    isFile = false;
                    //emulate expand - bad :/
                    setTimeout(function(){self.o.expandCallBack();},self.o.expandSpeed);

                //for others files and folders..
                } else {

                    //if permission check function from current relation = false
                    if (!self.o.checkPermFunc(sPath)) {
                        //do nothing
                        return false;
                    }
                    if ($(this).parent().hasClass('directory')) {
                        if ($(this).parent().hasClass('collapsed')) {
                            // Expand
                            openFolder($(this));

                        } else {
                            // Collapse
                            closeFolder($(this));
                        }
                        isFile = false;
                    } else {
                        isFile = true;
                    }

                }

                self.o.selectCallBack(sPath, isFile);
                return false;
            });
            // Prevent A from triggering the # on non-click events
            if( self.o.folderEvent.toLowerCase !== 'click' ) $(t).find('LI A').bind('click', function() {
                return false;
            });
        }

        /**
         * Opens/expands folder
         * @private
         * @param {Object} obj jQuery object of the tree (with relation), that have to be opened/expanded
         */
        function openFolder(obj){
            if( !self.o.multiFolder ) {
                obj.parent().parent().find("li.expanded > A").each(function(idx, element){
                    closeFolder($(element));
                });
            }

            showTree( obj.parent(), escape(obj.attr('rel').match( /.*\// )) );
            obj.parent().removeClass('collapsed').addClass('expanded');
        }

        /**
         * collapses/closes folder
         * @private
         * @param {Object} obj jQuery object of the tree (with relation), that have to be closed
         * @param {Function} fnAdditionalCallback own callback after close
         */
        function closeFolder(obj, fnAdditionalCallback){
            var totalCallback = function(){
                self.o.collapseCallBack();
                cleanUp(obj);// cleanup
                if(typeof(fnAdditionalCallback) === 'function' ) fnAdditionalCallback();// own callback
            };

            var toSlideUp = obj.parent().find('UL').first();


            //in case opened
            if(toSlideUp.length > 0){
                toSlideUp.slideUp(self.o.collapseSpeed,self.o.collapseEasing, totalCallback);
                obj.parent().removeClass('expanded').addClass('collapsed');
            //in case closed
            }else{
                totalCallback();
            }
        }

        /**
         * cleans elements after collapse
         * @private
         * @param {Object} obj jQuery object of the tree (with relation), that have to be cleaned
         */
        function cleanUp(obj){
            obj.parent().find('UL').remove();
        }

        /**
         * recursive helper to open deep path
         * @private
         * @param {Array} aPathSplitted
         * @param {Number} idx
         * @returns {Boolean} success
         */
        function openPathHelperRecursive(aPathSplitted, idx){

            //build path to open
            var bResult = false;
            bResult = self.openPathIfFound(aPathSplitted[idx]);

            //if opened before, and there are more elements to open,
            //open them, recursive. wait for expand.
            var nextPathIdx = idx+1;
            if(bResult && nextPathIdx < aPathSplitted.length){
                setTimeout(function(){
                    return openPathHelperRecursive(aPathSplitted, nextPathIdx);
                }, self.o.expandSpeed+self.o.triggerDelay);
            }

            //last call
            return bResult;

        }


        /**
         * splits path to array with parts of this path.
         * replaces "//" to "/"
         * @private
         * @example getSplittedPath(/folder/subfolder//file.txt)
         *          returns:  array("/", "/folder/", "/folder/subfolder/", "/folder/subfolder/file.txt")
         * @param {string} sPath
         * @return array that contains paths parts
         * @type Array
         */
        function getSplittedPath(sPath){
            sPath = sPath.replace("//", "/");
            var aResArray = new Array();
            //sPath.length-1 because we dont need to push the full path. will be done after the loop
            for (var i = 0; i < sPath.length-1; i++) {
                if(sPath.charAt(i) === '/'){
                    aResArray.push(sPath.substr(0, i+1));
                }
            }
            aResArray.push(sPath);
            return aResArray;
        };

        //create the object, and apply to the selector
        build($(this), settings);
        //bind jquery properties to the object
        $.extend(this,$(this));

        return this;
    };



    //jQuery extension
    $.fn.extend({
        fileTree: FileTree
    });

})(jQuery);