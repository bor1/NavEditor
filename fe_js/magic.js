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
var ne3_magic = function(){
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
ne3_magic.createLink = function(text, link, onclick, css_class, id){
    retString = '<a href="' + link + '"';
    if (onclick   !== false)
        retString += ' onclick="' + onclick + '"';
    if (css_class !== false)
        retString += ' class="' + css_class + '"';
    if (id        !== false)
        retString += ' id="' + id + '"';
    
    retString += '>' + text + '</a>' + "\n";
    return retString;
};

/**
 * Creates an html button and returns the tag in a string
 * @param {String} name
 * @param {String} value
 * @param {String} onclick
 * @param {String} css_class
 * @param {String} id
 * @returns {retString|String}
 */
ne3_magic.createButton = function(name, value, onclick, css_class, id){
    retString = '<input type="button" name="' + name
              + '" value="' + value + '"';

    if (onclick   !== false)
        retString += ' onclick="' + onclick + '"';
    if (css_class !== false)
        retString += ' class="' + css_class + '"';
    if (id        !== false)
        retString += ' id="' + id + '"';
    
    retString += '>';
    return retString;
};

ne3_magic.createFormField = function(name, type, size, maxlength, value){
    //
};

ne3_magic.createTextArea = function(name, cols, rows, content){
    //
};

ne3_magic.createForm = function(JSONdata){
    if (! (JSONdata instanceof JSON))
        return '<!-- no JSON data given to ne3_magic.createForm --!>';
    
    retString = '';
    
    
    return retString;
};