<?php
include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";
include_once SERVER_ROOT_PATH . "pm/views/wiki/diff/WikiHtmlDiff.php";

define( 'REGEX_MATH_TEX', '/<span\s+class="math-tex"\s*>([^<]+)<\/span>/i' );
define( 'REGEX_COMMENTS', '/<span\s+comment-id="(\d+)"\s*>([^<]+)<\/span>/i' );
define( 'TABLE_ROW_NUMBERING', '/<td>\s*<ol\s*([^>]*)>/i' );
define( 'CODE_ISOLATE', '/<code([^>]*)>([\S\s]+)<\/code>/i' );
define( 'CODE_RESTORE', '/<code([^>]*)>([0-9]+)<\/code>/i' );

class WrtfCKEditorPageParser extends WikiParser
{
    private $comment_it = null;
    private $tableRowIndex = 0;
    private $codeBlocks = array();

    function parse( $content = null )
    {
        $this->tableRowIndex = 0;

        $comment = getFactory()->getObject('Comment');
        if ( is_object($this->getObjectIt()) ) {
            $this->comment_it = $comment->getAllForObject($this->getObjectIt());
        }
        else {
            $this->comment_it = $comment->getEmptyIterator();
        }



        if ( function_exists('preg_replace_callback_array') ) {
            return preg_replace_callback_array(
                array(
                    CODE_ISOLATE => array($this, 'codeIsolate'),
                    REGEX_INCLUDE_PAGE => array($this, 'parseIncludePageCallback'),
                    REGEX_INCLUDE_REVISION => array($this, 'parseIncludeRevisionCallback'),
                    REGEX_UPDATE_UID => array($this, 'parseUpdateUidCallback'),
                    REGEX_UID => array($this, 'parseUidCallback'),
                    REGEX_COMMENTS => array($this, 'checkComments'),
                    TABLE_ROW_NUMBERING => array($this, 'tableRowNumbering'),
                    '/\s+src="([^d][^"]+)"/i' => array($this, 'parseImageSrcCallback'),
                    '/<div><\/div>/i' => function($match) {
                        return "";
                    },
                    CODE_RESTORE => array($this, 'codeRestore')
                ),
                $content
            );
        }
        else {
            $content = preg_replace_callback(CODE_ISOLATE, array($this, 'codeIsolate'), $content);
            $content = preg_replace_callback(REGEX_INCLUDE_PAGE, array($this, 'parseIncludePageCallback'), $content);
            $content = preg_replace_callback(REGEX_INCLUDE_REVISION, array($this, 'parseIncludeRevisionCallback'), $content);
            $content = preg_replace_callback(REGEX_UPDATE_UID, array($this, 'parseUpdateUidCallback'), $content);
            $content = preg_replace_callback(REGEX_UID, array($this, 'parseUidCallback'), $content);
            $content = preg_replace_callback(REGEX_COMMENTS, array($this, 'checkComments'), $content);
            $content = preg_replace_callback(TABLE_ROW_NUMBERING, array($this, 'tableRowNumbering'), $content);
            $content = preg_replace_callback('/\s+src="([^d][^"]+)"/i', array($this, 'parseImageSrcCallback'), $content);
            $content = preg_replace('/<div><\/div>/i', "", $content);
            $content = preg_replace_callback(CODE_RESTORE, array($this, 'codeRestore'), $content);
            return $content;
        }
    }

    function parseMathTex( $match ) {
        $url = defined('MATH_TEX_IMG') ? MATH_TEX_IMG : 'http://latex.codecogs.com/gif.latex?';
        return '<img src="'.$url.rawurlencode(trim(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML401, APP_ENCODING ))).'">';
    }

    function checkComments( $match ) {
        $this->comment_it->moveToId($match[1]);
        if ( $this->comment_it->getId() != $match[1] ) {
            return $match[2];
        }
        else {
            return $match[0];
        }
    }

    function tableRowNumbering( $match ) {
        if ( strpos($match[1], 'start') === false ) {
            return '<td><ol start="'.(++$this->tableRowIndex).'" '.$match[1].'>';
        }
        else {
            if ( preg_match('/start="(\d+)"/i', $match[1], $result) ) {
                $this->tableRowIndex = $result[1];
            }
            return $match[0];
        }
    }

    function codeIsolate( $match ) {
        return '<code'.$match[1].'>'.array_push($this->codeBlocks, $match[2]).'</code>';
    }

    function codeRestore( $match ) {
        return '<code'.$match[1].'>'.$this->codeBlocks[$match[2] - 1].'</code>';
    }

    function parseIncludeRevisionCallback( $match )
    {
        $info = $this->getUidInfo(trim($match[1], '[]'));
        $object_it = $info['object_it'];
        $content = '';

        if ( !is_object($object_it) || $object_it->getId() < 1 ) {
            return str_replace('%1', $match[1], text(1166));
        }

        $revisions = preg_split('/-/', $match[2]);
        $changeIt = getFactory()->getObject('WikiPageChange')->getExact($revisions[0]);

        if ( $changeIt->getId() != '' ) {
            if ( count($revisions) > 1 ) {
                if ( $revisions[1] > 0 ) {
                    $freshContent = $changeIt->object->getExact($revisions[1])->getHtmlDecoded('Content');
                }
                else {
                    $freshContent = $object_it->getHtmlDecoded('Content');
                }
                $parser = new WrtfCKEditorComparerParser($this->getObjectIt());
                $content .= '<div class="reset wysiwyg">';
                $diffBuilder = new WikiHtmlDiff(
                    $parser->parse($changeIt->getHtmlDecoded('Content')),
                    $parser->parse($freshContent)
                );
                $content .= $diffBuilder->build();
                $content .= '</div>';
            }
            else {
                $content = $changeIt->getHtmlDecoded('Content');
            }
        }
        else {
            $content = $object_it->getHtmlDecoded('Content');
        }

        $content .= '<div class="wiki-page-help">'.sprintf(text(2332), '<a target="_blank" href="'.$info['url'].'">'.$info['uid'].'</a>').'</div>';
        return $content;
    }

}