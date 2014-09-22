<?php
// diff.php
//
// PhpWiki diff output code.
//
// Copyright (C) 2000, 2001 Geoffrey T. Dairiki <dairiki@dairiki.org>
// You may copy this code freely under the conditions of the GPL.
//

if (!class_exists("XmlElement"))
    require_once(dirname(__FILE__)."/XmlElement.php");
if (class_exists("HtmlElement"))
    return;

/**
 * An XML element.
 */
//apd_set_session_trace(35);

class HtmlElement extends XmlElement
{
    function __construct ($tagname /* , $attr_or_content , ...*/) {
        $this->_init(func_get_args());
        $this->_properties = HTML::getTagProperties($tagname);
    }

    function _init ($args) {
        if (!is_array($args))
            $args = func_get_args();

        assert(count($args) >= 1);
        assert(is_string($args[0]));
        $this->_tag = array_shift($args);
        
        if ($args && is_array($args[0]))
            $this->_attr = array_shift($args);
        else {
            $this->_attr = array();
            if ($args && $args[0] === false)
                array_shift($args);
        }
        $this->setContent($args);
        $this->_properties = HTML::getTagProperties($this->_tag);
    }

    /**
     * @access protected
     * This is used by the static factory methods is class HTML.
     */
    function _init2 ($args) {
        if ($args) {
            if (is_array($args[0]))
                $this->_attr = array_shift($args);
            elseif ($args[0] === false)
                array_shift($args);
        }
        
        if (count($args) == 1 && is_array($args[0]))
            $args = $args[0];
        $this->_content = $args;
        return $this;
    }

    /** Add a "tooltip" to an element.
     *
     * @param $tooltip_text string The tooltip text.
     */
    function addTooltip ($tooltip_text) {
        $this->setAttr('title', $tooltip_text);

        // FIXME: this should be initialized from title by an onLoad() function.
        //        (though, that may not be possible.)
        $qtooltip = str_replace("'", "\\'", $tooltip_text);
        $this->setAttr('onmouseover',
                       sprintf('window.status="%s"; return true;',
                               addslashes($tooltip_text)));
        $this->setAttr('onmouseout', "window.status='';return true;");
    }

    function emptyTag () {
        if (($this->_properties & HTMLTAG_EMPTY) == 0)
            return $this->startTag() . "</$this->_tag>";

        return substr($this->startTag(), 0, -1) . " />";
    }

    function hasInlineContent () {
        return ($this->_properties & HTMLTAG_ACCEPTS_INLINE) != 0;
    }

    function isInlineElement () {
        return ($this->_properties & HTMLTAG_INLINE) != 0;
    }
};

function HTML (/* $content, ... */) {
    return new XmlContent(func_get_args());
}

class HTML extends HtmlElement {
    function raw ($html_text) {
        return new RawXml($html_text);
    }
    
    function getTagProperties($tag) {
        $props = &$GLOBALS['HTML_TagProperties'];
        return isset($props[$tag]) ? $props[$tag] : 0;
    }

    function _setTagProperty($prop_flag, $tags) {
        $props = &$GLOBALS['HTML_TagProperties'];
        if (is_string($tags))
            $tags = preg_split('/\s+/', $tags);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag)
                if (isset($props[$tag]))
                    $props[$tag] |= $prop_flag;
                else
                    $props[$tag] = $prop_flag;
        }
    }

    //
    // Shell script to generate the following static methods:
    //
    // #!/bin/sh
    // function mkfuncs () {
    //     for tag in "$@"
    //     do
    //         echo "    function $tag (/*...*/) {"
    //         echo "        \$el = new HtmlElement('$tag');"
    //         echo "        return \$el->_init2(func_get_args());"
    //         echo "    }"
    //     done
    // }
    // d='
    //     /****************************************/'
    // mkfuncs link meta style script noscript
    // echo "$d"
    // mkfuncs a img br span
    // echo "$d"
    // mkfuncs h1 h2 h3 h4 h5 h6
    // echo "$d"
    // mkfuncs hr div p pre blockquote
    // echo "$d"
    // mkfuncs em strong small
    // echo "$d"
    // mkfuncs tt u sup sub
    // echo "$d"
    // mkfuncs ul ol dl li dt dd
    // echo "$d"
    // mkfuncs table caption thead tbody tfoot tr td th colgroup col
    // echo "$d"
    // mkfuncs form input option select textarea
    // echo "$d"
    // mkfuncs area map frame frameset iframe nobody

    function link (/*...*/) {
        $el = new HtmlElement('link');
        return $el->_init2(func_get_args());
    }
    function meta (/*...*/) {
        $el = new HtmlElement('meta');
        return $el->_init2(func_get_args());
    }
    function style (/*...*/) {
        $el = new HtmlElement('style');
        return $el->_init2(func_get_args());
    }
    function script (/*...*/) {
        $el = new HtmlElement('script');
        return $el->_init2(func_get_args());
    }
    function noscript (/*...*/) {
        $el = new HtmlElement('noscript');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function a (/*...*/) {
        $el = new HtmlElement('a');
        return $el->_init2(func_get_args());
    }
    function img (/*...*/) {
        $el = new HtmlElement('img');
        return $el->_init2(func_get_args());
    }
    function br (/*...*/) {
        $el = new HtmlElement('br');
        return $el->_init2(func_get_args());
    }
    function span (/*...*/) {
        $el = new HtmlElement('span');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function h1 (/*...*/) {
        $el = new HtmlElement('h1');
        return $el->_init2(func_get_args());
    }
    function h2 (/*...*/) {
        $el = new HtmlElement('h2');
        return $el->_init2(func_get_args());
    }
    function h3 (/*...*/) {
        $el = new HtmlElement('h3');
        return $el->_init2(func_get_args());
    }
    function h4 (/*...*/) {
        $el = new HtmlElement('h4');
        return $el->_init2(func_get_args());
    }
    function h5 (/*...*/) {
        $el = new HtmlElement('h5');
        return $el->_init2(func_get_args());
    }
    function h6 (/*...*/) {
        $el = new HtmlElement('h6');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function hr (/*...*/) {
        $el = new HtmlElement('hr');
        return $el->_init2(func_get_args());
    }
    function div (/*...*/) {
        $el = new HtmlElement('div');
        return $el->_init2(func_get_args());
    }
    function p (/*...*/) {
        $el = new HtmlElement('p');
        return $el->_init2(func_get_args());
    }
    function pre (/*...*/) {
        $el = new HtmlElement('pre');
        return $el->_init2(func_get_args());
    }
    function blockquote (/*...*/) {
        $el = new HtmlElement('blockquote');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function em (/*...*/) {
        $el = new HtmlElement('em');
        return $el->_init2(func_get_args());
    }
    function strong (/*...*/) {
        $el = new HtmlElement('strong');
        return $el->_init2(func_get_args());
    }
    function small (/*...*/) {
        $el = new HtmlElement('small');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function tt (/*...*/) {
        $el = new HtmlElement('tt');
        return $el->_init2(func_get_args());
    }
    function u (/*...*/) {
        $el = new HtmlElement('u');
        return $el->_init2(func_get_args());
    }
    function sup (/*...*/) {
        $el = new HtmlElement('sup');
        return $el->_init2(func_get_args());
    }
    function sub (/*...*/) {
        $el = new HtmlElement('sub');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function ul (/*...*/) {
        $el = new HtmlElement('ul');
        return $el->_init2(func_get_args());
    }
    function ol (/*...*/) {
        $el = new HtmlElement('ol');
        return $el->_init2(func_get_args());
    }
    function dl (/*...*/) {
        $el = new HtmlElement('dl');
        return $el->_init2(func_get_args());
    }
    function li (/*...*/) {
        $el = new HtmlElement('li');
        return $el->_init2(func_get_args());
    }
    function dt (/*...*/) {
        $el = new HtmlElement('dt');
        return $el->_init2(func_get_args());
    }
    function dd (/*...*/) {
        $el = new HtmlElement('dd');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function table (/*...*/) {
        $el = new HtmlElement('table');
        return $el->_init2(func_get_args());
    }
    function caption (/*...*/) {
        $el = new HtmlElement('caption');
        return $el->_init2(func_get_args());
    }
    function thead (/*...*/) {
        $el = new HtmlElement('thead');
        return $el->_init2(func_get_args());
    }
    function tbody (/*...*/) {
        $el = new HtmlElement('tbody');
        return $el->_init2(func_get_args());
    }
    function tfoot (/*...*/) {
        $el = new HtmlElement('tfoot');
        return $el->_init2(func_get_args());
    }
    function tr (/*...*/) {
        $el = new HtmlElement('tr');
        return $el->_init2(func_get_args());
    }
    function td (/*...*/) {
        $el = new HtmlElement('td');
        return $el->_init2(func_get_args());
    }
    function th (/*...*/) {
        $el = new HtmlElement('th');
        return $el->_init2(func_get_args());
    }
    function colgroup (/*...*/) {
        $el = new HtmlElement('colgroup');
        return $el->_init2(func_get_args());
    }
    function col (/*...*/) {
        $el = new HtmlElement('col');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function form (/*...*/) {
        $el = new HtmlElement('form');
        return $el->_init2(func_get_args());
    }
    function input (/*...*/) {
        $el = new HtmlElement('input');
        return $el->_init2(func_get_args());
    }
    function button (/*...*/) {
        $el = new HtmlElement('button');
        return $el->_init2(func_get_args());
    }
    function option (/*...*/) {
        $el = new HtmlElement('option');
        return $el->_init2(func_get_args());
    }
    function select (/*...*/) {
        $el = new HtmlElement('select');
        return $el->_init2(func_get_args());
    }
    function textarea (/*...*/) {
        $el = new HtmlElement('textarea');
        return $el->_init2(func_get_args());
    }
    function label (/*...*/) {
        $el = new HtmlElement('label');
        return $el->_init2(func_get_args());
    }

    /****************************************/
    function area (/*...*/) {
        $el = new HtmlElement('area');
        return $el->_init2(func_get_args());
    }
    function map (/*...*/) {
        $el = new HtmlElement('map');
        return $el->_init2(func_get_args());
    }
    function frame (/*...*/) {
        $el = new HtmlElement('frame');
        return $el->_init2(func_get_args());
    }
    function frameset (/*...*/) {
        $el = new HtmlElement('frameset');
        return $el->_init2(func_get_args());
    }
    function iframe (/*...*/) {
        $el = new HtmlElement('iframe');
        return $el->_init2(func_get_args());
    }
    function nobody (/*...*/) {
        $el = new HtmlElement('nobody');
        return $el->_init2(func_get_args());
    }
    function object (/*...*/) {
        $el = new HtmlElement('object');
        return $el->_init2(func_get_args());
    }
    function embed (/*...*/) {
        $el = new HtmlElement('embed');
        return $el->_init2(func_get_args());
    }
}

define('HTMLTAG_EMPTY', 1);
define('HTMLTAG_INLINE', 2);
define('HTMLTAG_ACCEPTS_INLINE', 4);


HTML::_setTagProperty(HTMLTAG_EMPTY,
                      'area base basefont br col frame hr img input isindex link meta param');
HTML::_setTagProperty(HTMLTAG_ACCEPTS_INLINE,
                      // %inline elements:
                      'b big i small tt ' // %fontstyle
                      . 's strike u ' // (deprecated)
                      . 'abbr acronym cite code dfn em kbd samp strong var ' //%phrase
                      . 'a img object embed br script map q sub sup span bdo '//%special
                      . 'button input label option select textarea label ' //%formctl

                      // %block elements which contain inline content
                      . 'address h1 h2 h3 h4 h5 h6 p pre '
                      // %block elements which contain either block or inline content
                      . 'div fieldset frameset'

                      // other with inline content
                      . 'caption dt label legend '
                      // other with either inline or block
                      . 'dd del ins li td th colgroup');

HTML::_setTagProperty(HTMLTAG_INLINE,
                      // %inline elements:
                      'b big i small tt ' // %fontstyle
                      . 's strike u ' // (deprecated)
                      . 'abbr acronym cite code dfn em kbd samp strong var ' //%phrase
                      . 'a img object br script map q sub sup span bdo '//%special
                      . 'button input label option select textarea ' //%formctl
                      . 'nobody iframe'
                      );

/**
 * Generate hidden form input fields.
 *
 * @param $query_args hash  A hash mapping names to values for the hidden inputs.
 * Values in the hash can themselves be hashes.  The will result in hidden inputs
 * which will reconstruct the nested structure in the resulting query args as
 * processed by PHP.
 *
 * Example:
 *
 * $args = array('x' => '2',
 *               'y' => array('a' => 'aval', 'b' => 'bval'));
 * $inputs = HiddenInputs($args);
 *
 * Will result in:
 *
 *  <input type="hidden" name="x" value = "2" />
 *  <input type="hidden" name="y[a]" value = "aval" />
 *  <input type="hidden" name="y[b]" value = "bval" />
 *
 * @return object An XmlContent object containing the inputs.
 */
function HiddenInputs ($query_args, $pfx = false, $exclude = array()) {
    $inputs = HTML();

    foreach ($query_args as $key => $val) {
        if (in_array($key, $exclude)) continue;
        $name = $pfx ? $pfx . "[$key]" : $key;
        if (is_array($val))
            $inputs->pushContent(HiddenInputs($val, $name));
        else
            $inputs->pushContent(HTML::input(array('type' => 'hidden',
                                                   'name' => $name,
                                                   'value' => $val)));
    }
    return $inputs;
}


/** Generate a <script> tag containing javascript.
 *
 * @param string $js  The javascript.
 * @param string $script_args  (optional) hash of script tags options
 *                             e.g. to provide another version or the defer attr
 * @return HtmlElement A <script> element.
 */
function JavaScript ($js, $script_args = false) {
    $default_script_args = array(//'version' => 'JavaScript', // not xhtml conformant
                                 'type' => 'text/javascript');
    $script_args = $script_args ? array_merge($default_script_args, $script_args)
                                : $default_script_args;
    if (empty($js))
        return HTML::script($script_args);
    else
        // see http://devedge.netscape.com/viewsource/2003/xhtml-style-script/
        return HTML::script($script_args,
                            new RawXml((ENABLE_XHTML_XML ? "\n//<![CDATA[" : "\n<!--//")
                                       . "\n".rtrim($js)."\n"
                                       . (ENABLE_XHTML_XML ? "//]]>\n" : "// -->")));
}

/** Conditionally display content based of whether javascript is supported.
 *
 * This conditionally (on the client side) displays one of two alternate
 * contents depending on whether the client supports javascript.
 *
 * NOTE:
 * The content you pass as arguments to this function must be block-level.
 * (This is because the <noscript> tag is block-level.)
 *
 * @param mixed $if_content Content to display if the browser supports
 * javascript.
 *
 * @param mixed $else_content Content to display if the browser does
 * not support javascript.
 *
 * @return XmlContent
 */
function IfJavaScript($if_content = false, $else_content = false) {
    $html = array();
    if ($if_content) {
        $xml = AsXML($if_content);
        $js = sprintf('document.write("%s");',
                      addcslashes($xml, "\0..\37!@\\\177..\377"));
        $html[] = JavaScript($js);
    }
    if ($else_content) {
        $html[] = HTML::noscript(false, $else_content);
    }
    return HTML($html);
}
    
/**
 $Log: HtmlElement.php,v $
 Revision 1.46  2005/01/25 06:50:33  rurban
 added label

 Revision 1.45  2005/01/10 18:05:56  rurban
 php5 case-sensitivity

 Revision 1.44  2005/01/08 20:58:19  rurban
 ending space after colgroup breaks _setTagProperty

 Revision 1.43  2004/11/21 11:59:14  rurban
 remove final \n to be ob_cache independent

 Revision 1.42  2004/09/26 17:09:23  rurban
 add SVG support for Ploticus (and hopefully all WikiPluginCached types)
 SWF not yet.

 Revision 1.41  2004/08/05 17:31:50  rurban
 more xhtml conformance fixes

 Revision 1.40  2004/06/25 14:29:17  rurban
 WikiGroup refactoring:
   global group attached to user, code for not_current user.
   improved helpers for special groups (avoid double invocations)
 new experimental config option ENABLE_XHTML_XML (fails with IE, and document.write())
 fixed a XHTML validation error on userprefs.tmpl

 Revision 1.39  2004/05/17 13:36:49  rurban
 Apply RFE #952323 "ExternalSearchPlugin improvement", but
   with <button><img></button>

 Revision 1.38  2004/05/12 10:49:54  rurban
 require_once fix for those libs which are loaded before FileFinder and
   its automatic include_path fix, and where require_once doesn't grok
   dirname(__FILE__) != './lib'
 upgrade fix with PearDB
 navbar.tmpl: remove spaces for IE &nbsp; button alignment

 Revision 1.37  2004/04/26 20:44:34  rurban
 locking table specific for better databases

 Revision 1.36  2004/04/19 21:51:41  rurban
 php5 compatibility: it works!

 Revision 1.35  2004/04/19 18:27:45  rurban
 Prevent from some PHP5 warnings (ref args, no :: object init)
   php5 runs now through, just one wrong XmlElement object init missing
 Removed unneccesary UpgradeUser lines
 Changed WikiLink to omit version if current (RecentChanges)

 Revision 1.34  2004/03/24 19:39:02  rurban
 php5 workaround code (plus some interim debugging code in XmlElement)
   php5 doesn't work yet with the current XmlElement class constructors,
   WikiUserNew does work better than php4.
 rewrote WikiUserNew user upgrading to ease php5 update
 fixed pref handling in WikiUserNew
 added Email Notification
 added simple Email verification
 removed emailVerify userpref subclass: just a email property
 changed pref binary storage layout: numarray => hash of non default values
 print optimize message only if really done.
 forced new cookie policy: delete pref cookies, use only WIKI_ID as plain string.
   prefs should be stored in db or homepage, besides the current session.

 Revision 1.33  2004/03/18 22:32:33  rurban
 work to make it php5 compatible

 Revision 1.32  2004/02/15 21:34:37  rurban
 PageList enhanced and improved.
 fixed new WikiAdmin... plugins
 editpage, Theme with exp. htmlarea framework
   (htmlarea yet committed, this is really questionable)
 WikiUser... code with better session handling for prefs
 enhanced UserPreferences (again)
 RecentChanges for show_deleted: how should pages be deleted then?

 Revision 1.31  2003/02/27 22:47:26  dairiki
 New functions in HtmlElement:

 JavaScript($js)
    Helper for generating javascript.

 IfJavaScript($if_content, $else_content)
    Helper for generating
       <script>document.write('...')</script><noscript>...</noscript>
    constructs.

 Revision 1.30  2003/02/17 06:02:25  dairiki
 Remove functions HiddenGets() and HiddenPosts().

 These functions were evil.  They didn't check the request method,
 so they often resulted in GET args being converted to POST args,
 etc...

 One of these is still used in lib/plugin/WikiAdminSelect.php,
 but, so far as I can tell, that code is both broken _and_ it
 doesn't do anything.

 Revision 1.29  2003/02/15 01:54:19  dairiki
 Added HTML::meta() for <meta> tag.

 Revision 1.28  2003/01/04 02:32:30  carstenklapp
 Added 'col' and 'colgroup' table elements used by PluginManager.

 */

// (c-file-style: "gnu")
// Local Variables:
// mode: php
// tab-width: 8
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End:

// difflib.php
//
// A PHP diff engine for phpwiki.
//
// Copyright (C) 2000, 2001 Geoffrey T. Dairiki <dairiki@dairiki.org>
// You may copy this code freely under the conditions of the GPL.
//

// FIXME: possibly remove assert()'s for production version?

// PHP3 does not have assert()
define('USE_ASSERTS', function_exists('assert'));

class _DiffOp {
    var $type;
    var $orig;
    var $final;

    function reverse() {
        trigger_error("pure virtual", E_USER_ERROR);
    }

    function norig() {
        return $this->orig ? sizeof($this->orig) : 0;
    }

    function nfinal() {
        return $this->final ? sizeof($this->final) : 0;
    }
}

class _DiffOp_Copy extends _DiffOp {
    var $type = 'copy';

    function _DiffOp_Copy ($orig, $final = false) {
        if (!is_array($final))
            $final = $orig;
        $this->orig = $orig;
        $this->final = $final;
    }

    function reverse() {
        return new _DiffOp_Copy($this->final, $this->orig);
    }
}

class _DiffOp_Delete extends _DiffOp {
    var $type = 'delete';

    function _DiffOp_Delete ($lines) {
        $this->orig = $lines;
        $this->final = false;
    }

    function reverse() {
        return new _DiffOp_Add($this->orig);
    }
}

class _DiffOp_Add extends _DiffOp {
    var $type = 'add';

    function _DiffOp_Add ($lines) {
        $this->final = $lines;
        $this->orig = false;
    }

    function reverse() {
        return new _DiffOp_Delete($this->final);
    }
}

class _DiffOp_Change extends _DiffOp {
    var $type = 'change';

    function _DiffOp_Change ($orig, $final) {
        $this->orig = $orig;
        $this->final = $final;
    }

    function reverse() {
        return new _DiffOp_Change($this->final, $this->orig);
    }
}


/**
 * Class used internally by Diff to actually compute the diffs.
 *
 * The algorithm used here is mostly lifted from the perl module
 * Algorithm::Diff (version 1.06) by Ned Konz, which is available at:
 *   http://www.perl.com/CPAN/authors/id/N/NE/NEDKONZ/Algorithm-Diff-1.06.zip
 *
 * More ideas are taken from:
 *   http://www.ics.uci.edu/~eppstein/161/960229.html
 *
 * Some ideas are (and a bit of code) are from from analyze.c, from GNU
 * diffutils-2.7, which can be found at:
 *   ftp://gnudist.gnu.org/pub/gnu/diffutils/diffutils-2.7.tar.gz
 *
 * Finally, some ideas (subdivision by NCHUNKS > 2, and some optimizations)
 * are my own.
 *
 * @author Geoffrey T. Dairiki
 * @access private
 */
class _DiffEngine
{
    function diff ($from_lines, $to_lines) {
        $n_from = sizeof($from_lines);
        $n_to = sizeof($to_lines);

        $this->xchanged = $this->ychanged = array();
        $this->xv = $this->yv = array();
        $this->xind = $this->yind = array();
        unset($this->seq);
        unset($this->in_seq);
        unset($this->lcs);

        // Skip leading common lines.
        for ($skip = 0; $skip < $n_from && $skip < $n_to; $skip++) {
            if ($from_lines[$skip] != $to_lines[$skip])
                break;
            $this->xchanged[$skip] = $this->ychanged[$skip] = false;
        }
        // Skip trailing common lines.
        $xi = $n_from; $yi = $n_to;
        for ($endskip = 0; --$xi > $skip && --$yi > $skip; $endskip++) {
            if ($from_lines[$xi] != $to_lines[$yi])
                break;
            $this->xchanged[$xi] = $this->ychanged[$yi] = false;
        }

        // Ignore lines which do not exist in both files.
        for ($xi = $skip; $xi < $n_from - $endskip; $xi++)
            $xhash[$from_lines[$xi]] = 1;
        for ($yi = $skip; $yi < $n_to - $endskip; $yi++) {
            $line = $to_lines[$yi];
            if ( ($this->ychanged[$yi] = empty($xhash[$line])) )
                continue;
            $yhash[$line] = 1;
            $this->yv[] = $line;
            $this->yind[] = $yi;
        }
        for ($xi = $skip; $xi < $n_from - $endskip; $xi++) {
            $line = $from_lines[$xi];
            if ( ($this->xchanged[$xi] = empty($yhash[$line])) )
                continue;
            $this->xv[] = $line;
            $this->xind[] = $xi;
        }

        // Find the LCS.
        $this->_compareseq(0, sizeof($this->xv), 0, sizeof($this->yv));

        // Merge edits when possible
        $this->_shift_boundaries($from_lines, $this->xchanged, $this->ychanged);
        $this->_shift_boundaries($to_lines, $this->ychanged, $this->xchanged);

        // Compute the edit operations.
        $edits = array();
        $xi = $yi = 0;
        while ($xi < $n_from || $yi < $n_to) {
            USE_ASSERTS && assert($yi < $n_to || $this->xchanged[$xi]);
            USE_ASSERTS && assert($xi < $n_from || $this->ychanged[$yi]);

            // Skip matching "snake".
            $copy = array();
            while ( $xi < $n_from && $yi < $n_to
                    && !$this->xchanged[$xi] && !$this->ychanged[$yi]) {
                $copy[] = $from_lines[$xi++];
                ++$yi;
            }
            if ($copy)
                $edits[] = new _DiffOp_Copy($copy);

            // Find deletes & adds.
            $delete = array();
            while ($xi < $n_from && $this->xchanged[$xi])
                $delete[] = $from_lines[$xi++];

            $add = array();
            while ($yi < $n_to && $this->ychanged[$yi])
                $add[] = $to_lines[$yi++];

            if ($delete && $add)
                $edits[] = new _DiffOp_Change($delete, $add);
            elseif ($delete)
                $edits[] = new _DiffOp_Delete($delete);
            elseif ($add)
                $edits[] = new _DiffOp_Add($add);
        }
        return $edits;
    }


    /* Divide the Largest Common Subsequence (LCS) of the sequences
     * [XOFF, XLIM) and [YOFF, YLIM) into NCHUNKS approximately equally
     * sized segments.
     *
     * Returns (LCS, PTS).  LCS is the length of the LCS. PTS is an
     * array of NCHUNKS+1 (X, Y) indexes giving the diving points between
     * sub sequences.  The first sub-sequence is contained in [X0, X1),
     * [Y0, Y1), the second in [X1, X2), [Y1, Y2) and so on.  Note
     * that (X0, Y0) == (XOFF, YOFF) and
     * (X[NCHUNKS], Y[NCHUNKS]) == (XLIM, YLIM).
     *
     * This function assumes that the first lines of the specified portions
     * of the two files do not match, and likewise that the last lines do not
     * match.  The caller must trim matching lines from the beginning and end
     * of the portions it is going to specify.
     */
    function _diag ($xoff, $xlim, $yoff, $ylim, $nchunks) {
	$flip = false;
	
	if ($xlim - $xoff > $ylim - $yoff) {
	    // Things seems faster (I'm not sure I understand why)
            // when the shortest sequence in X.
            $flip = true;
	    list ($xoff, $xlim, $yoff, $ylim)
		= array( $yoff, $ylim, $xoff, $xlim);
        }

	if ($flip)
	    for ($i = $ylim - 1; $i >= $yoff; $i--)
		$ymatches[$this->xv[$i]][] = $i;
	else
	    for ($i = $ylim - 1; $i >= $yoff; $i--)
		$ymatches[$this->yv[$i]][] = $i;

	$this->lcs = 0;
	$this->seq[0]= $yoff - 1;
	$this->in_seq = array();
	$ymids[0] = array();

	$numer = $xlim - $xoff + $nchunks - 1;
	$x = $xoff;
	for ($chunk = 0; $chunk < $nchunks; $chunk++) {
	    if ($chunk > 0)
		for ($i = 0; $i <= $this->lcs; $i++)
		    $ymids[$i][$chunk-1] = $this->seq[$i];

	    $x1 = $xoff + (int)(($numer + ($xlim-$xoff)*$chunk) / $nchunks);
	    for ( ; $x < $x1; $x++) {
                $line = $flip ? $this->yv[$x] : $this->xv[$x];
                if (empty($ymatches[$line]))
		    continue;
		$matches = $ymatches[$line];
                reset($matches);
		while (list ($junk, $y) = each($matches))
		    if (empty($this->in_seq[$y])) {
			$k = $this->_lcs_pos($y);
			USE_ASSERTS && assert($k > 0);
			$ymids[$k] = $ymids[$k-1];
			break;
                    }
		while (list ($junk, $y) = each($matches)) {
		    if ($y > $this->seq[$k-1]) {
			USE_ASSERTS && assert($y < $this->seq[$k]);
			// Optimization: this is a common case:
			//  next match is just replacing previous match.
			$this->in_seq[$this->seq[$k]] = false;
			$this->seq[$k] = $y;
			$this->in_seq[$y] = 1;
                    }
		    else if (empty($this->in_seq[$y])) {
			$k = $this->_lcs_pos($y);
			USE_ASSERTS && assert($k > 0);
			$ymids[$k] = $ymids[$k-1];
                    }
                }
            }
        }

	$seps[] = $flip ? array($yoff, $xoff) : array($xoff, $yoff);
	$ymid = $ymids[$this->lcs];
	for ($n = 0; $n < $nchunks - 1; $n++) {
	    $x1 = $xoff + (int)(($numer + ($xlim - $xoff) * $n) / $nchunks);
	    $y1 = $ymid[$n] + 1;
	    $seps[] = $flip ? array($y1, $x1) : array($x1, $y1);
        }
	$seps[] = $flip ? array($ylim, $xlim) : array($xlim, $ylim);

	return array($this->lcs, $seps);
    }

    function _lcs_pos ($ypos) {
	$end = $this->lcs;
	if ($end == 0 || $ypos > $this->seq[$end]) {
	    $this->seq[++$this->lcs] = $ypos;
	    $this->in_seq[$ypos] = 1;
	    return $this->lcs;
        }

	$beg = 1;
	while ($beg < $end) {
	    $mid = (int)(($beg + $end) / 2);
	    if ( $ypos > $this->seq[$mid] )
		$beg = $mid + 1;
	    else
		$end = $mid;
        }

	USE_ASSERTS && assert($ypos != $this->seq[$end]);

	$this->in_seq[$this->seq[$end]] = false;
	$this->seq[$end] = $ypos;
	$this->in_seq[$ypos] = 1;
	return $end;
    }

    /* Find LCS of two sequences.
     *
     * The results are recorded in the vectors $this->{x,y}changed[], by
     * storing a 1 in the element for each line that is an insertion
     * or deletion (ie. is not in the LCS).
     *
     * The subsequence of file 0 is [XOFF, XLIM) and likewise for file 1.
     *
     * Note that XLIM, YLIM are exclusive bounds.
     * All line numbers are origin-0 and discarded lines are not counted.
     */
    function _compareseq ($xoff, $xlim, $yoff, $ylim) {
	// Slide down the bottom initial diagonal.
	while ($xoff < $xlim && $yoff < $ylim
               && $this->xv[$xoff] == $this->yv[$yoff]) {
	    ++$xoff;
	    ++$yoff;
        }

	// Slide up the top initial diagonal.
	while ($xlim > $xoff && $ylim > $yoff
               && $this->xv[$xlim - 1] == $this->yv[$ylim - 1]) {
	    --$xlim;
	    --$ylim;
        }

	if ($xoff == $xlim || $yoff == $ylim)
	    $lcs = 0;
	else {
	    // This is ad hoc but seems to work well.
	    //$nchunks = sqrt(min($xlim - $xoff, $ylim - $yoff) / 2.5);
	    //$nchunks = max(2,min(8,(int)$nchunks));
	    $nchunks = min(7, $xlim - $xoff, $ylim - $yoff) + 1;
	    list ($lcs, $seps)
		= $this->_diag($xoff,$xlim,$yoff, $ylim,$nchunks);
        }

	if ($lcs == 0) {
	    // X and Y sequences have no common subsequence:
	    // mark all changed.
	    while ($yoff < $ylim)
		$this->ychanged[$this->yind[$yoff++]] = 1;
	    while ($xoff < $xlim)
		$this->xchanged[$this->xind[$xoff++]] = 1;
        }
	else {
	    // Use the partitions to split this problem into subproblems.
	    reset($seps);
	    $pt1 = $seps[0];
	    while ($pt2 = next($seps)) {
		$this->_compareseq ($pt1[0], $pt2[0], $pt1[1], $pt2[1]);
		$pt1 = $pt2;
            }
        }
    }

    /* Adjust inserts/deletes of identical lines to join changes
     * as much as possible.
     *
     * We do something when a run of changed lines include a
     * line at one end and has an excluded, identical line at the other.
     * We are free to choose which identical line is included.
     * `compareseq' usually chooses the one at the beginning,
     * but usually it is cleaner to consider the following identical line
     * to be the "change".
     *
     * This is extracted verbatim from analyze.c (GNU diffutils-2.7).
     */
    function _shift_boundaries ($lines, &$changed, $other_changed) {
	$i = 0;
	$j = 0;

	USE_ASSERTS && assert('sizeof($lines) == sizeof($changed)');
	$len = sizeof($lines);
	$other_len = sizeof($other_changed);

	while (1) {
	    /*
	     * Scan forwards to find beginning of another run of changes.
	     * Also keep track of the corresponding point in the other file.
	     *
	     * Throughout this code, $i and $j are adjusted together so that
	     * the first $i elements of $changed and the first $j elements
	     * of $other_changed both contain the same number of zeros
	     * (unchanged lines).
	     * Furthermore, $j is always kept so that $j == $other_len or
	     * $other_changed[$j] == false.
	     */
	    while ($j < $other_len && $other_changed[$j])
		$j++;
	
	    while ($i < $len && ! $changed[$i]) {
		USE_ASSERTS && assert('$j < $other_len && ! $other_changed[$j]');
		$i++; $j++;
		while ($j < $other_len && $other_changed[$j])
		    $j++;
            }

	    if ($i == $len)
		break;

	    $start = $i;

	    // Find the end of this run of changes.
	    while (++$i < $len && $changed[$i])
		continue;

	    do {
		/*
		 * Record the length of this run of changes, so that
		 * we can later determine whether the run has grown.
		 */
		$runlength = $i - $start;

		/*
		 * Move the changed region back, so long as the
		 * previous unchanged line matches the last changed one.
		 * This merges with previous changed regions.
		 */
		while ($start > 0 && $lines[$start - 1] == $lines[$i - 1]) {
		    $changed[--$start] = 1;
		    $changed[--$i] = false;
		    while ($start > 0 && $changed[$start - 1])
			$start--;
		    USE_ASSERTS && assert('$j > 0');
		    while ($other_changed[--$j])
			continue;
		    USE_ASSERTS && assert('$j >= 0 && !$other_changed[$j]');
                }

		/*
		 * Set CORRESPONDING to the end of the changed run, at the last
		 * point where it corresponds to a changed run in the other file.
		 * CORRESPONDING == LEN means no such point has been found.
		 */
		$corresponding = $j < $other_len ? $i : $len;

		/*
		 * Move the changed region forward, so long as the
		 * first changed line matches the following unchanged one.
		 * This merges with following changed regions.
		 * Do this second, so that if there are no merges,
		 * the changed region is moved forward as far as possible.
		 */
		while ($i < $len && $lines[$start] == $lines[$i]) {
		    $changed[$start++] = false;
		    $changed[$i++] = 1;
		    while ($i < $len && $changed[$i])
			$i++;

		    USE_ASSERTS && assert('$j < $other_len && ! $other_changed[$j]');
		    $j++;
		    if ($j < $other_len && $other_changed[$j]) {
			$corresponding = $i;
			while ($j < $other_len && $other_changed[$j])
			    $j++;
                    }
                }
            } while ($runlength != $i - $start);

	    /*
	     * If possible, move the fully-merged run of changes
	     * back to a corresponding run in the other file.
	     */
	    while ($corresponding < $i) {
		$changed[--$start] = 1;
		$changed[--$i] = 0;
		USE_ASSERTS && assert('$j > 0');
		while ($other_changed[--$j])
		    continue;
		USE_ASSERTS && assert('$j >= 0 && !$other_changed[$j]');
            }
        }
    }
}

/**
 * Class representing a 'diff' between two sequences of strings.
 */
class Diff
{
    var $edits;

    /**
     * Constructor.
     * Computes diff between sequences of strings.
     *
     * @param $from_lines array An array of strings.
     *        (Typically these are lines from a file.)
     * @param $to_lines array An array of strings.
     */
    function Diff($from_lines, $to_lines) {
        $eng = new _DiffEngine;
        $this->edits = $eng->diff($from_lines, $to_lines);
        //$this->_check($from_lines, $to_lines);
    }

    /**
     * Compute reversed Diff.
     *
     * SYNOPSIS:
     *
     *  $diff = new Diff($lines1, $lines2);
     *  $rev = $diff->reverse();
     * @return object A Diff object representing the inverse of the
     *                original diff.
     */
    function reverse () {
	$rev = $this;
        $rev->edits = array();
        foreach ($this->edits as $edit) {
            $rev->edits[] = $edit->reverse();
        }
	return $rev;
    }

    /**
     * Check for empty diff.
     *
     * @return bool True iff two sequences were identical.
     */
    function isEmpty () {
        foreach ($this->edits as $edit) {
            if ($edit->type != 'copy')
                return false;
        }
        return true;
    }

    /**
     * Compute the length of the Longest Common Subsequence (LCS).
     *
     * This is mostly for diagnostic purposed.
     *
     * @return int The length of the LCS.
     */
    function lcs () {
	$lcs = 0;
        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $lcs += sizeof($edit->orig);
        }
	return $lcs;
    }

    /**
     * Get the original set of lines.
     *
     * This reconstructs the $from_lines parameter passed to the
     * constructor.
     *
     * @return array The original sequence of strings.
     */
    function orig() {
        $lines = array();

        foreach ($this->edits as $edit) {
            if ($edit->orig)
                array_splice($lines, sizeof($lines), 0, $edit->orig);
        }
        return $lines;
    }

    /**
     * Get the final set of lines.
     *
     * This reconstructs the $to_lines parameter passed to the
     * constructor.
     *
     * @return array The sequence of strings.
     */
    function _final() {
        $lines = array();

        foreach ($this->edits as $edit) {
            if ($edit->final)
                array_splice($lines, sizeof($lines), 0, $edit->final);
        }
        return $lines;
    }

    /**
     * Check a Diff for validity.
     *
     * This is here only for debugging purposes.
     */
    function _check ($from_lines, $to_lines) {
        if (serialize($from_lines) != serialize($this->orig()))
            trigger_error("Reconstructed original doesn't match", E_USER_ERROR);
        if (serialize($to_lines) != serialize($this->_final()))
            trigger_error("Reconstructed final doesn't match", E_USER_ERROR);

        $rev = $this->reverse();
        if (serialize($to_lines) != serialize($rev->orig()))
            trigger_error("Reversed original doesn't match", E_USER_ERROR);
        if (serialize($from_lines) != serialize($rev->_final()))
            trigger_error("Reversed final doesn't match", E_USER_ERROR);


        $prevtype = 'none';
        foreach ($this->edits as $edit) {
            if ( $prevtype == $edit->type )
                trigger_error("Edit sequence is non-optimal", E_USER_ERROR);
            $prevtype = $edit->type;
        }

        $lcs = $this->lcs();
        trigger_error("Diff okay: LCS = $lcs", E_USER_NOTICE);
    }
}




/**
 * FIXME: bad name.
 */
class MappedDiff
extends Diff
{
    /**
     * Constructor.
     *
     * Computes diff between sequences of strings.
     *
     * This can be used to compute things like
     * case-insensitve diffs, or diffs which ignore
     * changes in white-space.
     *
     * @param $from_lines array An array of strings.
     *  (Typically these are lines from a file.)
     *
     * @param $to_lines array An array of strings.
     *
     * @param $mapped_from_lines array This array should
     *  have the same size number of elements as $from_lines.
     *  The elements in $mapped_from_lines and
     *  $mapped_to_lines are what is actually compared
     *  when computing the diff.
     *
     * @param $mapped_to_lines array This array should
     *  have the same number of elements as $to_lines.
     */
    function MappedDiff($from_lines, $to_lines,
                        $mapped_from_lines, $mapped_to_lines) {

        assert(sizeof($from_lines) == sizeof($mapped_from_lines));
        assert(sizeof($to_lines) == sizeof($mapped_to_lines));

        $this->Diff($mapped_from_lines, $mapped_to_lines);

        $xi = $yi = 0;
        // Optimizing loop invariants:
        // http://phplens.com/lens/php-book/optimizing-debugging-php.php
        for ($i = 0, $max = sizeof($this->edits); $i < $max; $i++) {
            $orig = &$this->edits[$i]->orig;
            if (is_array($orig)) {
                $orig = array_slice($from_lines, $xi, sizeof($orig));
                $xi += sizeof($orig);
            }

            $final = &$this->edits[$i]->final;
            if (is_array($final)) {
                $final = array_slice($to_lines, $yi, sizeof($final));
                $yi += sizeof($final);
            }
        }
    }
}


/**
 * A class to format Diffs
 *
 * This class formats the diff in classic diff format.
 * It is intended that this class be customized via inheritance,
 * to obtain fancier outputs.
 */
class DiffFormatter
{
    /**
     * Number of leading context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     */
    var $leading_context_lines = 0;

    /**
     * Number of trailing context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     */
    var $trailing_context_lines = 0;

    /**
     * Format a diff.
     *
     * @param $diff object A Diff object.
     * @return string The formatted output.
     */
    function format($diff) {

        $xi = $yi = 1;
        $block = false;
        $context = array();

        $nlead = $this->leading_context_lines;
        $ntrail = $this->trailing_context_lines;

        $this->_start_diff();

        foreach ($diff->edits as $edit) {
            if ($edit->type == 'copy') {
                if (is_array($block)) {
                    if (sizeof($edit->orig) <= $nlead + $ntrail) {
                        $block[] = $edit;
                    }
                    else{
                        if ($ntrail) {
                            $context = array_slice($edit->orig, 0, $ntrail);
                            $block[] = new _DiffOp_Copy($context);
                        }
                        $this->_block($x0, $ntrail + $xi - $x0,
                                      $y0, $ntrail + $yi - $y0,
                                      $block);
                        $block = false;
                    }
                }
                $context = $edit->orig;
            }
            else {
                if (! is_array($block)) {
                    $context = array_slice($context, max(0, sizeof($context) - $nlead));
                    $x0 = $xi - sizeof($context);
                    $y0 = $yi - sizeof($context);
                    $block = array();
                    if ($context)
                        $block[] = new _DiffOp_Copy($context);
                }
                $block[] = $edit;
            }

            if ($edit->orig)
                $xi += sizeof($edit->orig);
            if ($edit->final)
                $yi += sizeof($edit->final);
        }

        if (is_array($block))
            $this->_block($x0, $xi - $x0,
                          $y0, $yi - $y0,
                          $block);

        return $this->_end_diff();
    }

    function _block($xbeg, $xlen, $ybeg, $ylen, &$edits) {
        $this->_start_block($this->_block_header($xbeg, $xlen, $ybeg, $ylen));
        foreach ($edits as $edit) {
            if ($edit->type == 'copy')
                $this->_context($edit->orig);
            elseif ($edit->type == 'add')
                $this->_added($edit->final);
            elseif ($edit->type == 'delete')
                $this->_deleted($edit->orig);
            elseif ($edit->type == 'change')
                $this->_changed($edit->orig, $edit->final);
            else
                trigger_error("Unknown edit type", E_USER_ERROR);
        }
        $this->_end_block();
    }

    function _start_diff() {
        ob_start();
    }

    function _end_diff() {
        $val = ob_get_contents();
        ob_end_clean();
        return $val;
    }

    function _block_header($xbeg, $xlen, $ybeg, $ylen) {
        if ($xlen > 1)
            $xbeg .= "," . ($xbeg + $xlen - 1);
        if ($ylen > 1)
            $ybeg .= "," . ($ybeg + $ylen - 1);

        return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
    }

    function _start_block($header) {
        echo $header;
    }

    function _end_block() {
    }

    function _lines($lines, $prefix = ' ') {
        foreach ($lines as $line)
            echo "$prefix $line\n";
    }

    function _context($lines) {
        $this->_lines($lines);
    }

    function _added($lines) {
        $this->_lines($lines, ">");
    }
    function _deleted($lines) {
        $this->_lines($lines, "<");
    }

    function _changed($orig, $final) {
        $this->_deleted($orig);
        echo "---\n";
        $this->_added($final);
    }
}

/**
 * "Unified" diff formatter.
 *
 * This class formats the diff in classic "unified diff" format.
 */
class UnifiedDiffFormatter extends DiffFormatter
{
    function UnifiedDiffFormatter($context_lines = 4) {
        $this->leading_context_lines = $context_lines;
        $this->trailing_context_lines = $context_lines;
    }

    function _block_header($xbeg, $xlen, $ybeg, $ylen) {
        if ($xlen != 1)
            $xbeg .= "," . $xlen;
        if ($ylen != 1)
            $ybeg .= "," . $ylen;
        return "@@ -$xbeg +$ybeg @@\n";
    }

    function _added($lines) {
        $this->_lines($lines, "+");
    }
    function _deleted($lines) {
        $this->_lines($lines, "-");
    }
    function _changed($orig, $final) {
        $this->_deleted($orig);
        $this->_added($final);
    }
}

/**
 * block conflict diff formatter.
 *
 * This class will format a diff identical to Diff3 (i.e. editpage
 * conflicts), but when there are only two source files. To be used by
 * future enhancements to reloading / upgrading pgsrc.
 *
 * Functional but not finished yet, need to eliminate redundant block
 * suffixes (i.e. "=======" immediately followed by another prefix)
 * see class LoadFileConflictPageEditor
 */
class BlockDiffFormatter extends DiffFormatter
{
    function BlockDiffFormatter($context_lines = 4) {
        $this->leading_context_lines = $context_lines;
        $this->trailing_context_lines = $context_lines;
    }
    function _lines($lines, $prefix = '') {
        if (! $prefix == '')
            echo "$prefix\n";
        foreach ($lines as $line)
            echo "$line\n";
        if (! $prefix == '')
            echo "$prefix\n";
    }
    function _added($lines) {
        $this->_lines($lines, ">>>>>>>");
    }
    function _deleted($lines) {
        $this->_lines($lines, "<<<<<<<");
    }
    function _block_header($xbeg, $xlen, $ybeg, $ylen) {
        return "";
    }
    function _changed($orig, $final) {
        $this->_deleted($orig);
        $this->_added($final);
    }
}

/**
 $Log: difflib.php,v $
 Revision 1.12  2005/02/04 13:44:45  rurban
 prevent from php5 nameclash

 Revision 1.11  2004/11/21 11:59:19  rurban
 remove final \n to be ob_cache independent

 Revision 1.10  2004/04/08 01:22:53  rurban
 fixed PageChange Notification

 Revision 1.9  2003/11/30 18:43:18  carstenklapp
 Fixed careless mistakes in my last optimization commit.

 Revision 1.8  2003/11/30 18:20:34  carstenklapp
 Minor code optimization: reduce invariant loops

 Revision 1.7  2003/01/03 22:27:17  carstenklapp
 Minor adjustments to diff block markers ("<<<<<<<"). Source reformatting.

 Revision 1.6  2003/01/02 22:51:43  carstenklapp
 Specifying a leading diff context size larger than the available
 context now returns the available number of lines instead of the
 default. (Prevent negative offsets to array_slice() when $nlead >
 sizeof($context)). Added BlockDiffFormatter, to be used by future
 enhancements to reload / upgrade pgsrc.

 */

// Local Variables:
// mode: php
// tab-width: 8
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End:

class _HWLDF_WordAccumulator {
    function _HWLDF_WordAccumulator () {
        $this->_lines = array();
        $this->_line = false;
        $this->_group = false;
        $this->_tag = '~begin';
    }

    function _flushGroup ($new_tag) {
        if ($this->_group !== false) {
            if (!$this->_line)
                $this->_line = HTML();
            $this->_line->pushContent($this->_tag
                                      ? new HtmlElement($this->_tag,
                                                        $this->_group)
                                      : $this->_group);
        }
        $this->_group = '';
        $this->_tag = $new_tag;
    }

    function _flushLine ($new_tag) {
        $this->_flushGroup($new_tag);
        if ($this->_line)
            $this->_lines[] = $this->_line;
        $this->_line = HTML();
    }

    function addWords ($words, $tag = '') {
        if ($tag != $this->_tag)
            $this->_flushGroup($tag);

        foreach ($words as $word) {
            // new-line should only come as first char of word.
            if (!$word)
                continue;
            if ($word[0] == "\n") {
                $this->_group .= " ";
                $this->_flushLine($tag);
                $word = substr($word, 1);
            }
            assert(!strstr($word, "\n"));
            $this->_group .= $word;
        }
    }

    function getLines() {
        $this->_flushLine('~done');
        return $this->_lines;
    }
}

class WordLevelDiff extends MappedDiff
{
    function WordLevelDiff ($orig_lines, $final_lines) {
        list ($orig_words, $orig_stripped) = $this->_split($orig_lines);
        list ($final_words, $final_stripped) = $this->_split($final_lines);


        $this->MappedDiff($orig_words, $final_words,
                          $orig_stripped, $final_stripped);
    }

    function _split($lines) {
        // FIXME: fix POSIX char class.
        if (!preg_match_all('/ ( [^\S\n]+ | [[:alnum:]]+ | . ) (?: (?!< \n) [^\S\n])? /xs',
                            implode("\n", $lines),
                            $m)) {
            return array(array(''), array(''));
        }
        return array($m[0], $m[1]);
    }

    function orig () {
        $orig = new _HWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $orig->addWords($edit->orig);
            elseif ($edit->orig)
                $orig->addWords($edit->orig, 'del');
        }
        return $orig->getLines();
    }

    function _final () {
        $final = new _HWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $final->addWords($edit->final);
            elseif ($edit->final)
                $final->addWords($edit->final, 'ins');
        }
        return $final->getLines();
    }
}


/**
 * HTML unified diff formatter.
 *
 * This class formats a diff into a CSS-based
 * unified diff format.
 *
 * Within groups of changed lines, diffs are highlit
 * at the character-diff level.
 */
class HtmlUnifiedDiffFormatter extends UnifiedDiffFormatter
{
    function HtmlUnifiedDiffFormatter($context_lines = 4) {
        $this->UnifiedDiffFormatter($context_lines);
    }

    function _start_diff() {
        $this->_top = HTML::div(array('class' => 'diff'));
    }
    function _end_diff() {
        $val = $this->_top;
        unset($this->_top);
        return $val;
    }

    function _start_block($header) {
        $this->_block = HTML::div(array('class' => 'block'),
                                  HTML::tt($header));
    }

    function _end_block() {
        $this->_top->pushContent($this->_block);
        unset($this->_block);
    }

    function _lines($lines, $class, $prefix = false, $elem = false) {
        if (!$prefix)
            $prefix = HTML::raw('&nbsp;');
        $div = HTML::div(array('class' => 'difftext'));
        foreach ($lines as $line) {
            if ($elem)
                $line = new HtmlElement($elem, $line);
            $div->pushContent(HTML::div(array('class' => $class),
                                        HTML::tt(array('class' => 'prefix'),
                                                 $prefix),
                                        $line, HTML::raw('&nbsp;')));
        }
        $this->_block->pushContent($div);
    }

    function _context($lines) {
        //$this->_lines($lines, 'context');
    }
    function _deleted($lines) {
        $this->_lines($lines, 'deleted', '-', 'del');
    }

    function _added($lines) {
        $this->_lines($lines, 'added', '+', 'ins');
    }

    function _changed($orig, $final) {
        $diff = new WordLevelDiff($orig, $final);
        $this->_lines($diff->orig(), 'original', '-');
        $this->_lines($diff->_final(), 'final', '+');
    }
}

class HtmlFullDiffFormatter extends HtmlUnifiedDiffFormatter
{
    function _context($lines) {
        $this->_lines($lines, 'context');
    }
}

class FullUnifiedDiffFormatter extends UnifiedDiffFormatter
{
    function _context($lines) {
        $this->_lines($lines, ' ');
    }
}

/**
 * HTML table-based unified diff formatter.
 *
 * This class formats a diff into a table-based
 * unified diff format.  (Similar to what was produced
 * by previous versions of PhpWiki.)
 *
 * Within groups of changed lines, diffs are highlit
 * at the character-diff level.
 */
class TableUnifiedDiffFormatter extends HtmlUnifiedDiffFormatter
{
    function TableUnifiedDiffFormatter($context_lines = 4) {
        $this->HtmlUnifiedDiffFormatter($context_lines);
    }

    function _start_diff() {
        $this->_top = HTML::table(array('width' => '100%',
                                        'class' => 'diff',
                                        'cellspacing' => 1,
                                        'cellpadding' => 1,
                                        'border' => 1));
    }

    function _start_block($header) {
        $this->_block = HTML::table(array('width' => '100%',
                                          'class' => 'block',
                                          'cellspacing' => 0,
                                          'cellpadding' => 1,
                                          'border' => 0),
                                    HTML::tr(HTML::td(array('colspan' => 2),
                                                      HTML::tt($header))));
    }

    function _end_block() {
        $this->_top->pushContent(HTML::tr(HTML::td($this->_block)));
        unset($this->_block);
    }

    function _lines($lines, $class, $prefix = false, $elem = false) {
        if (!$prefix)
            $prefix = HTML::raw('&nbsp;');
        $prefix = HTML::td(array('class' => 'prefix',
                                 'width' => "1%"), $prefix);
        foreach ($lines as $line) {
            if (! trim($line))
                $line = HTML::raw('&nbsp;');
            elseif ($elem)
                $line = new HtmlElement($elem, $line);
            $this->_block->pushContent(HTML::tr(array('valign' => 'top'),
                                                $prefix,
                                                HTML::td(array('class' => $class),
                                                         $line)));
        }
    }
}


/////////////////////////////////////////////////////////////////

function PageInfoRow ($label, $rev, &$request, $is_current = false)
{
    global $WikiTheme;

    $row = HTML::tr(HTML::td(array('align' => 'right'), $label));
    if ($rev) {
        $author = $rev->get('author');
        $dbi = $request->getDbh();

        $iswikipage = (isWikiWord($author) && $dbi->isWikiPage($author));
        $authorlink = $iswikipage ? WikiLink($author) : $author;
        $version = $rev->getVersion();
        $linked_version = WikiLink($rev, 'existing', $version);
        if ($is_current)
            $revertbutton = HTML();
        else
            $revertbutton = $WikiTheme->makeActionButton(array('action' => 'revert',
                                                               'version' => $version),
                                                         false, $rev);
        $row->pushContent(HTML::td(fmt("version %s", $linked_version)),
                          HTML::td($WikiTheme->getLastModifiedMessage($rev,
                                                                      false)),
                          HTML::td(fmt("by %s", $authorlink)),
                          HTML::td($revertbutton)
                          );
    } else {
        $row->pushContent(HTML::td(array('colspan' => '4'), _("None")));
    }
    return $row;
}

function showDiff (&$request) {
    $pagename = $request->getArg('pagename');
    if (is_array($versions = $request->getArg('versions'))) {
        // Version selection from pageinfo.php display:
        rsort($versions);
        list ($version, $previous) = $versions;
    }
    else {
        $version = $request->getArg('version');
        $previous = $request->getArg('previous');
    }
 
    // abort if page doesn't exist
    $dbi = $request->getDbh();
    $page = $request->getPage();
    $current = $page->getCurrentRevision();
    if ($current->getVersion() < 1) {
        $html = HTML::div(array('id'=>'content'),
                          HTML::p(fmt("I'm sorry, there is no such page as %s.",
                                      WikiLink($pagename, 'unknown'))));
        include_once('lib/Template.php');
        GeneratePage($html, sprintf(_("Diff: %s"), $pagename), false);
        return; //early return
    }

    if ($version) {
        if (!($new = $page->getRevision($version)))
            NoSuchRevision($request, $page, $version);
        $new_version = fmt("version %d", $version);
    }
    else {
        $new = $current;
        $new_version = _("current version");
    }

    if (preg_match('/^\d+$/', $previous)) {
        if ( !($old = $page->getRevision($previous)) )
            NoSuchRevision($request, $page, $previous);
        $old_version = fmt("version %d", $previous);
        $others = array('major', 'minor', 'author');
    }
    else {
        switch ($previous) {
        case 'author':
            $old = $new;
            while ($old = $page->getRevisionBefore($old)) {
                if ($old->get('author') != $new->get('author'))
                    break;
            }
            $old_version = _("revision by previous author");
            $others = array('major', 'minor');
            break;
        case 'minor':
            $previous='minor';
            $old = $page->getRevisionBefore($new);
            $old_version = _("previous revision");
            $others = array('major', 'author');
            break;
        case 'major':
        default:
            $old = $new;
            while ($old && $old->get('is_minor_edit'))
                $old = $page->getRevisionBefore($old);
            if ($old)
                $old = $page->getRevisionBefore($old);
            $old_version = _("predecessor to the previous major change");
            $others = array('minor', 'author');
            break;
        }
    }

    $new_link = WikiLink($new, '', $new_version);
    $old_link = $old ? WikiLink($old, '', $old_version) : $old_version;
    $page_link = WikiLink($page);

    $html = HTML::div(array('id'=>'content'),
                     HTML::p(fmt("Differences between %s and %s of %s.",
                                 $new_link, $old_link, $page_link)));

    $otherdiffs = HTML::p(_("Other diffs:"));
    $label = array('major' => _("Previous Major Revision"),
                   'minor' => _("Previous Revision"),
                   'author'=> _("Previous Author"));
    foreach ($others as $other) {
        $args = array('action' => 'diff', 'previous' => $other);
        if ($version)
            $args['version'] = $version;
        if (count($otherdiffs->getContent()) > 1)
            $otherdiffs->pushContent(", ");
        else
            $otherdiffs->pushContent(" ");
        $otherdiffs->pushContent(Button($args, $label[$other]));
    }
    $html->pushContent($otherdiffs);


    if ($old and $old->getVersion() == 0)
        $old = false;

    $html->pushContent(HTML::Table(PageInfoRow(_("Newer page:"), $new,
                                               $request, empty($version)),
                                   PageInfoRow(_("Older page:"), $old,
                                               $request, false)));

    if ($new && $old) {
        $diff = new Diff($old->getContent(), $new->getContent());

        if ($diff->isEmpty()) {
            $html->pushContent(HTML::hr(),
                               HTML::p('[', _("Versions are identical"),
                                       ']'));
        }
        else {
            // New CSS formatted unified diffs (ugly in NS4).
            $fmt = new HtmlUnifiedDiffFormatter;

            // Use this for old table-formatted diffs.
            //$fmt = new TableUnifiedDiffFormatter;
            $html->pushContent($fmt->format($diff));
        }
    }

    include_once('lib/Template.php');
    GeneratePage($html, sprintf(_("Diff: %s"), $pagename), $new);
}

// $Log: diff.php,v $
// Revision 1.52  2005/04/01 14:45:14  rurban
// fix dirty side-effect: dont printf too early bypassing ob_buffering.
// fixes MSIE.
//
// Revision 1.51  2005/02/04 15:26:57  rurban
// need div=content for blog
//
// Revision 1.50  2005/02/04 13:44:45  rurban
// prevent from php5 nameclash
//
// Revision 1.49  2004/11/21 11:59:19  rurban
// remove final \n to be ob_cache independent
//
// Revision 1.48  2004/06/14 11:31:36  rurban
// renamed global $Theme to $WikiTheme (gforge nameclash)
// inherit PageList default options from PageList
//   default sortby=pagename
// use options in PageList_Selectable (limit, sortby, ...)
// added action revert, with button at action=diff
// added option regex to WikiAdminSearchReplace
//
// Revision 1.47  2004/06/08 13:51:57  rurban
// some comments only
//
// Revision 1.46  2004/05/01 15:59:29  rurban
// nothing changed
//
// Revision 1.45  2004/01/25 03:57:15  rurban
// use isWikiWord()
//
// Revision 1.44  2003/02/17 02:17:31  dairiki
// Fix so that action=diff will work when the most recent version
// of a page has been "deleted".
//
// Revision 1.43  2003/01/29 19:17:37  carstenklapp
// Bugfix for &nbsp showing on diff page.
//
// Revision 1.42  2003/01/11 23:05:04  carstenklapp
// Tweaked diff formatting.
//
// Revision 1.41  2003/01/08 02:23:02  carstenklapp
// Don't perform a diff when the page doesn't exist (such as a
// nonexistant calendar day/sub-page)
//

// Local Variables:
// mode: php
// tab-width: 8
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End:
?>