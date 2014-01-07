/**
 * NavEditor JavaScript Tools
 * @class NavTools
 * @property {Object} settings settings
 * @type NavTools
 * @returns {NavTools}
 */
var NavTools = new function(){
    if(!jQuery){return {};}
    var $ = jQuery; //ensure for $ == jQuery

    /**
     * Settings for the Tools
     * @type {Object}
     * @private
     * @property {String} ajax_handler_fullpath full path
     */
    var settings = {
        current_host: '',
        nav_editor_path: '',
        ajax_handler_path: '',
        ajax_handler_fullpath: ''
    };

    var self = this;

    /**
     * Sets settings, with 's' elements, or default.
     * @param {Object} [s] - settings object, with setting values
     */
    this.set_settings = function(s){
        if( !s ){var s = {};}
        settings.current_host = location.protocol + "//" + location.host + "/";
        settings.nav_editor_path = self.ifsetor(s.nav_editor_path, 'vkdaten/tools/NavEditor3/');
        settings.ajax_handler_path = self.ifsetor(s.ajax_handler_path, 'app/ajax_handler.php');
        settings.ajax_handler_fullpath = settings.current_host + settings.nav_editor_path + settings.ajax_handler_path;
    };

    /**
     * Settings Getter (reference so its possible to change settings values)
     * @return {Object} - reference to settings object
     */
    this.get_settings = function(){
        return settings;
    };


    /**
     * returns ifnullvalue if 'value' is null or undefined and 'value' otherwise
     * @param {mixed} value value to test for null or undefined
     * @param {mixed} ifNotSetvalue value to return in case 'value' is null or undefined
     * @return {mixed}
     */
    this.ifsetor = function(value, ifNotSetvalue){
        if(value === undefined || value === null){
            return ifNotSetvalue;
        }
        return value;
    };

    /**
     * request 'phpFileName' php file, his function 'phpFunction' with args: 'args'<br/>
     * call 'fnCallback' function after.
     * @param {String} phpFileName php file/class to call
     * @param {String} phpFunction functions/method of the php file/class
     * @param {Array} args arguments for the function
     * @param {Function} fnCallback
     */
    this.call_php = function(phpFileName, phpFunction, args, fnCallback){

        $.post(settings.ajax_handler_fullpath, {
            "file": phpFileName,
            "function": phpFunction,
            "args": args
        }, fnCallback);
    };


    /**
     * escapes html
     * {@link http://stackoverflow.com/questions/1787322/htmlspecialchars-equivalent-in-javascript}
     * @param {String} unsafe string to escape
     * @returns {String} safe html string
     */
    this.escapeHtml = function(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    /**
     * encode value to html
     * {@link http://stackoverflow.com/questions/1219860/javascript-jquery-html-encoding}
     * @param {String} value string to encode
     * @returns {String} html encoded string
     */
    this.htmlEncode = function(value){
        //create a in-memory div, set it's inner text(which jQuery automatically encodes)
        //then grab the encoded contents back out.  The div never exists on the page.
        return $('<div/>').text(value).html();
    };

    /**
     * decode html value
     * {@link http://stackoverflow.com/questions/1219860/javascript-jquery-html-encoding}
     * @param {String} value string to decode
     * @returns {String} decoded string
     */
    this.htmlDecode = function (value){
        return $('<div/>').html(value).text();
    };

    /**
     * add slashes to string
     * {@link http://phpjs.org/functions/addslashes/}
     * @param {String} str source sring
     * @returns {String} string with slashesh
     */
    this.addslashes = function(str){
        return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
    };

    /**
     * strip slashes from string
     * {@link http://phpjs.org/functions/stripslashes/}
     * @param {String} str source string
     * @returns {String} stripped string
     */
    this.stripslashesh = function (str) {
        return (str + '').replace(/\\(.?)/g, function (s, n1) {
            switch (n1) {
                case '\\':
                    return '\\';
                case '0':
                    return '\u0000';
                case '':
                    return '';
                default:
                    return n1;
            }
        });
    };





    //set default settings
    this.set_settings();

}();