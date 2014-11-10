/**
 * The class and functions to generate Formulars.
 * (will be) used under "Erweitert" etc.
 * @author Simon Michalke
 */

/**
 * This var will be our "static class" that generates the Settings
 * selectors and the formulars to edit config files.
 * ne3_magic uses JSON encoded files to generate the selectors and forms.
 */
var ne3_magic = function () {
    return 'version: 0.1';
};

/**
 * Creates an html hyperlink and returns the tag in a string
 * @param {String} text
 * @param {String} link
 * @param {String} onclick
 * @param {String} css_class
 * @param {String} id
 * @returns {retString|String}
 */
ne3_magic.createLink = function (text, link, onclick, css_class, id) {
    retString = '<a href="' + link + '"';
    if ((typeof onclick !== "undefined") && (onclick !== "undefined"))
        retString += ' onclick="' + onclick + '"';
    if ((typeof css_class !== "undefined") && (css_class !== "undefined"))
        retString += ' class="' + css_class + '"';
    if ((typeof id !== "undefined") && (id !== "undefined"))
        retString += ' id="' + id + '"';

    retString += '>' + text + '</a>';
    return retString;
};

/**
 * Creates an html button and returns the tag in a string
 * @param {String} name
 * @param {String} label
 * @param {String} onclick
 * @param {String} css_class
 * @param {String} id
 * @returns {String}
 */
ne3_magic.createButton = function (name, label, onclick, css_class, id) {
    retString = '<button name="' + name + '"';

    if ((typeof onclick !== "undefined") && (onclick !== "undefined"))
        retString += ' onclick="' + onclick + '"';
    if ((typeof css_class !== "undefined") && (css_class !== "undefined"))
        retString += ' class="' + css_class + '"';
    if ((typeof id !== "undefined") && (id !== "undefined"))
        retString += ' id="' + id + '"';

    retString += '>' + label + '</button>';
    return retString;
};

/**
 * Creates a simple <input> form Field.
 * @param {String} name
 * @param {String} type
 * @param {String} size
 * @param {String} maxlength
 * @param {String} value
 * @param {String} css_class
 * @param {String} id 
 * @returns {String}
 */
ne3_magic.createFormField = function (name, type, size, maxlength, value, css_class, id) {
    var retString = '<input name="' + name + '" type="' + type + '"';

    if ((typeof size !== "undefined") && (size !== "undefined"))
        retString += ' size="' + size + '"';

    if ((typeof maxlength !== "undefined") && (maxlength !== "undefined"))
        retString += ' maxlength="' + maxlength + '"';

    if ((typeof value !== "undefined") && (value !== "undefined"))
        retString += ' value="' + value + '"';

    if ((typeof css_class !== "undefined") && (css_class !== "undefined"))
        retString += ' class="' + css_class + '"';

    if ((typeof id !== "undefined") && (id !== "undefined"))
        retString += ' id="' + id + '"';

    retString += ">";
    return retString;
};

/**
 * Creates a text area.
 * @param {String} name
 * @param {String} cols
 * @param {String} rows
 * @param {String} content
 * @param {String} css_class
 * @param {String} id 
 * @returns {String} html tag in a string
 */
ne3_magic.createTextArea = function (name, cols, rows, content, css_class, id) {
    if ((typeof cols === "undefined") || (size === "undefined"))
        cols = 30;
    if ((typeof rows === "undefined") || (size === "undefined"))
        rows = 5;

    var retString = '<textarea name="' + name + '" cols="' + cols + '" rows="' + rows + '"';

    if ((typeof css_class !== "undefined") && (css_class !== "undefined"))
        retString += ' class="' + css_class + '"';
    if ((typeof id !== "undefined") && (css_class !== "undefined"))
        retString += ' id="' + id + '"';

    retString += '>';

    if ((content !== false) && (content !== ""))
        retString += content;

    retString += '</textarea>';
    return retString;
};

/**
 * Generates the html code for a dropbox
 * @param {String} name
 * @param {String} elements
 * @param {String} css_class
 * @param {String} id
 * @returns {retString|String}
 */
ne3_magic.createDropBox = function (name, elements, css_class, id) {

    //size = elements.length;
    size = 1;

    retString = '<select name="' + name + '" size="' + size + '"';

    if ((typeof css_class !== "undefined") && (css_class !== "undefined"))
        retString += ' class="' + css_class + '"';
    if ((typeof id !== "undefined") && (id !== "undefined"))
        retString += ' id="' + id + '"';

    retString += '>';

    for (i = 0; i < (elements.length); i++)
        retString += '<option value="' + elements[i].value + '">' + elements[i].content + '</option>';

    retString += '</select>';

    return retString;
};

/**
 * This function generates an html form out of a JSON data string OR
 * out of the json information parsed into JSONdata
 * For the data format, see ./../fe_json/RULES
 * @param {JSONstring} JSONdata
 * @returns {retString|String}
 */
ne3_magic.createForm = function (JSONdata) {

    try { //JSON.parse() throws a "SyntaxError" exception if the string to parse is not valid JSON.
        //see: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/parse
        var data = JSON.parse(JSONdata);
    }
    catch (error) {
        if (!(JSONdata.identifier))
            return '<!-- no JSON data given to ne3_magic.createForm --!>';

        //JSONdata is already parsed
        data = JSONdata;
    }

    retString = '';

    if (data.identifier !== "json_form_data")
        return '<!-- wrong format in ne3_magic.createForm --!>';

    retString += '<form name="' + data.form.name + '"';

    if (data.form.css_class !== "")
        retString += ' class="' + data.form.css_class + '"';
    if (data.form.css_id !== "")
        retString += ' id="' + data.form.id + '"';

    retString += '>';
    retString += '<table>';

    for (i = 0; i < data.form.elements.length; i++) {

        var curEl = data.form.elements[i];

        retString += '<tr><td>';
        retString += '<b>' + curEl.e_label + '</b></td><td>';
        //our current element

        switch (curEl.type) {
            case "link":
                retString += ne3_magic.createLink(curEl.text, curEl.link, curEl.onclick, curEl.css_class, curEl.css_id);
                break;

            case "button":
                retString += ne3_magic.createButton(curEl.name, curEl.label, curEl.onclick, curEl.css_class, curEl.css_id);
                break;

            case "input":
                retString += ne3_magic.createFormField(curEl.name, curEl.f_type, curEl.size, curEl.maxlength, curEl.value, curEl.css_class, curEl.css_id);
                break;

            case "textarea":
                retString += ne3_magic.createTextArea(curEl.name, curEl.cols, curEl.rows, curEl.content, curEl.css_class, curEl.css_id);
                break;

            case "dropbox":
                retString += ne3_magic.createDropBox(curEl.name, curEl.elements, curEl.css_class, curEl.css_id);
                break;

            default:
                retString += '<!-- Unknown type in magic.js::createForm-->' + "\n";
        }

        retString += '</td></tr>' + "\n";
    }

    retString += '</table>';
    retString += '</form>';
    
    retString += '<hr>';
    for (i=0; i< data.form.buttons.length; i++){
        var curEl = data.form.buttons[i];
        
        retString += '<button name="' + curEl.name + '"';
        
        
        if ((typeof curEl.css_class !== "undefined") && (curEl.css_class !== "undefined"))
            retString += ' class="' + curEl.css_class + '"';
        if ((typeof curEl.css_id !== "undefined") && (curEl.css_id !== "undefined"))
            retString += ' id="' + curEl.css_id + '"';
        
        retString += '>' + curEl.e_label + '</button>';
    
        
    }

    return retString;
};

ne3_magic.createList = function (JSONdata) {
    var retString = "";

    try { //JSON.parse() throws a "SyntaxError" exception if the string to parse is not valid JSON.
        //see: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/parse
        var data = JSON.parse(JSONdata);
    }
    catch (error) {
        if (!(JSONdata.identifier))
            return '<!-- no JSON data given to ne3_magic.createList --!>';

        //JSONdata is already parsed
        data = JSONdata;
    }

    for (i = 0; i < data.elements.length; i++) {
        switch (data.elements[i].type) {
            case "h1":
            case "h2":
            case "h3":
            case "h4":
            case "h5":
            case "h6":
                
                retString += '<' + data.elements[i].type;
                
                if ((typeof data.elements[i].css_class !== "undefined") && (data.elements[i].css_class !== "undefined"))
                    retString += ' class="' + data.elements[i].css_class + '"';
                if ((typeof data.elements[i].css_id !== "undefined") && (data.elements[i].css_id !== "undefined"))
                    retString += ' id="' + data.elements[i].css_id + '"';
    
                retString += '>' + data.elements[i].content + '</' + data.elements[i].type + ">\n";
                
              break;

            case "link":
                
                
                retString += '<a href="#" ';
                
                if ((typeof data.elements[i].css_class !== "undefined") && (data.elements[i].css_class !== "undefined"))
                    retString += ' class="' + data.elements[i].css_class + '"';
                if ((typeof data.elements[i].css_id !== "undefined") && (data.elements[i].css_id !== "undefined"))
                    retString += ' id="' + data.elements[i].css_id + '"';
                    
                retString += 'onclick="' + data.elements[i].onclick + '">' + data.elements[i].content + "</a>\n";
                retString += "<br>";
                break;

            default:
                retString += "<!-- unknown list type in magic.js::ne3_magic.createList -->";
        }
    }

    //console.log(retString);
    return retString;
};
