/**
 * NavEditor JavaScript Tools
 * @class NavTools
 * @property {Object} settings settings
 * @type NavTools
 * @returns {NavTools}
 */
var NavTools = new function(){
    if(typeof jQuery === 'undefined'){return {};}
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
     * @param {Object} [_s] - settings object, with setting values
     */
    this.set_settings = function(_s){
        var s = self.ifsetor(_s,{});
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
     * returns <code>ifNotSetValue</code> if 'value' is null or undefined and 'value' otherwise
     * @param {*} value value to test for null or undefined
     * @param {*} ifNotSetValue value to return in case 'value' is null or undefined
     * @return {*}
     */
    this.ifsetor = function(value, ifNotSetValue){
        if(value === undefined || value === null){
            return ifNotSetValue;
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

    /**
     * dirname, php like
     * {@link http://phpjs.org/functions/dirname/}
     * @param {String} path path string to parse
     * @example dirname('c:/Temp/x');<br/>returns 'c:/Temp'
     * @example dirname('/dir/test/');<br/>returns '/dir'
     * @returns {String} directory name
     */
    this.dirname = function(path) {
        return path.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '');
    };


    /**
     * basename, php like
     * {@link http://phpjs.org/functions/basename/}
     * @param {String} path path string to parse
     * @param {String} [suffix] extension to cut
     * @example basename('/www/site/home.htm', '.htm');<br/>returns 'home'
     * @example basename('ecra.php?p=1');<br/>returns 'ecra.php?p=1'
     * @returns {String} file/folder name
     */
    this.basename = function(path, suffix) {
        var b = path;
        var lastChar = b.charAt(b.length-1);
        if(lastChar === "/" || lastChar === "\\") {
            b = b.slice(0, -1);
        }

        b = b.replace(/^.*[\/\\]/g, '');

        if (typeof suffix === 'string' && b.substr(b.length - suffix.length) == suffix) {
            b = b.substr(0, b.length - suffix.length);
        }

        return b;
    };


    /**
     * Pathinfo, php like
     * {@link http://phpjs.org/functions/pathinfo/}
     * @param {String} path path to parse
     * @param {mixed} options options:<br/>
     *   'PATHINFO_DIRNAME': 1<br/>
     *   'PATHINFO_BASENAME': 2<br/>
     *   'PATHINFO_EXTENSION': 4<br/>
     *   'PATHINFO_FILENAME': 8<br/>
     *   'PATHINFO_ALL': 0
     * @example pathinfo('/www/htdocs/index.html', 1);<br/>returns '/www/htdocs'
     * @example pathinfo('/www/htdocs/index.html', 'PATHINFO_BASENAME')<br/>returns 'index.html'
     * @example pathinfo('/www/htdocs/index.html', 2 | 4)<br/>returns {basename: 'index.html', extension: 'html'}
     * @example pathinfo('/www/htdocs/index.html');<br/>returns {dirname: '/www/htdocs', basename: 'index.html', extension: 'html', filename: 'index'}
     * @returns {String|Object} Object wih data, if no or multiple options set. Or String value if one option set
     */
    this.pathinfo = function(path, options) {
        var opt = '',
            real_opt = '',
            optName = '',
            optTemp = 0,
            tmp_arr = {},
            cnt = 0,
            i = 0;
        var have_basename = false,
            have_extension = false,
            have_filename = false;

        // Input defaulting & sanitation
        if (!path) {
            return false;
        }
        if (!options) {
            options = 'PATHINFO_ALL';
        }

        // Initialize binary arguments. Both the string & integer (constant) input is
        // allowed
        var OPTS = {
            'PATHINFO_DIRNAME': 1,
            'PATHINFO_BASENAME': 2,
            'PATHINFO_EXTENSION': 4,
            'PATHINFO_FILENAME': 8,
            'PATHINFO_ALL': 0
        };
        // PATHINFO_ALL sums up all previously defined PATHINFOs (could just pre-calculate)
        for (optName in OPTS) {
            if(OPTS.hasOwnProperty(optName)){
                OPTS.PATHINFO_ALL = OPTS.PATHINFO_ALL | OPTS[optName];
            }
        }
        if (typeof options !== 'number') { // Allow for a single string or an array of string flags
            options = [].concat(options);
            for (i = 0; i < options.length; i++) {
                // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
                if (OPTS[options[i]]) {
                    optTemp = optTemp | OPTS[options[i]];
                }
            }
            options = optTemp;
        }

        // Internal Functions
        var __getExt = function(path) {
            var str = path + '';
            var dotP = str.lastIndexOf('.') + 1;
            return !dotP ? false : dotP !== str.length ? str.substr(dotP) : '';
        };


        // Gather path infos
        if (options & OPTS.PATHINFO_DIRNAME) {
            var dirname = this.dirname(path);
            tmp_arr.dirname = dirname === path ? '.' : dirname;
        }

        if (options & OPTS.PATHINFO_BASENAME) {
            if (false === have_basename) {
                have_basename = this.basename(path);
            }
            tmp_arr.basename = have_basename;
        }

        if (options & OPTS.PATHINFO_EXTENSION) {
            if (false === have_basename) {
                have_basename = this.basename(path);
            }
            if (false === have_extension) {
                have_extension = __getExt(have_basename);
            }
            if (false !== have_extension) {
                tmp_arr.extension = have_extension;
            }
        }

        if (options & OPTS.PATHINFO_FILENAME) {
            if (false === have_basename) {
                have_basename = this.basename(path);
            }
            if (false === have_extension) {
                have_extension = __getExt(have_basename);
            }
            if (false === have_filename) {
                have_filename = have_basename.slice(0, have_basename.length - (have_extension ? have_extension.length + 1 : have_extension === false ? 0 : 1));
            }

            tmp_arr.filename = have_filename;
        }


        // If array contains only 1 element: return string
        cnt = 0;
        for (opt in tmp_arr) {
            if(tmp_arr.hasOwnProperty(opt)){
                cnt++;
                real_opt = opt;
            }
        }
        if (cnt === 1) {
            return tmp_arr[real_opt];
        }

        // Return full-blown array
        return tmp_arr;
    };






    //set default settings
    this.set_settings();

}();