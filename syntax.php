<?php
/**
 * @license    GNU_GPL_v2
 * @author     Olivier Cortes <olive@deep-ocean.net>
 * @author     Franz Haefner <fhaefner@informatik.tu-cottbus.de>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');


class syntax_plugin_kasten extends DokuWiki_Syntax_Plugin {
 
    function getInfo(){
	//TODO fehlende Info.txt
        return confToHash(dirname(__FILE__).'/info.txt');
    }
 
    function getType(){ return 'container'; }
    function getPType(){ return 'block'; }

    function getAllowedTypes() { 
        return array('container','substition','protected','disabled','formatting','paragraphs');
    }

    function getSort(){ return 158; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern('<kasten.*?>(?=.*?</kasten>)',$mode,'plugin_kasten');
    }
    function postConnect() {
        $this->Lexer->addExitPattern('</kasten>','plugin_kasten');
    }
 
    function handle($match, $state, $pos, &$handler){
        global $ID;
        
        switch ($state) {

          case DOKU_LEXER_ENTER : 
            $pageid = cleanID(trim(substr($match,7,-1)));
            resolve_pageid(curNS($ID), $pageid, $exists);
            return array($state, $pageid);
            
          case DOKU_LEXER_UNMATCHED :
            return array($state, $match);
        
          default:
            return array($state);
        }
    }
 
    function render($mode, &$renderer, $indata) {

        if($mode == 'xhtml'){

          list($state, $data) = $indata;

          switch ($state) {
            case DOKU_LEXER_ENTER :
              $renderer->doc .= '<div class="kasten_box">';
              $renderer->doc .= '<a class="kasten_link" href="'.wl($data).'" style=""></a>';
              $renderer->doc .= '<div class="kasten_header">'.p_get_first_heading($data, METADATA_RENDER_USING_SIMPLE_CACHE).'</div>'."\n";
              $renderer->doc .= '<div class="kasten_content">';
              break;
  
            case DOKU_LEXER_UNMATCHED :
              $renderer->doc .= $renderer->_xmlEntities($data);
              break;
  
            case DOKU_LEXER_EXIT :
              $renderer->doc .= "</div>";
              $renderer->doc .= "</div>\n";
              break;
          }
          return true;

        }
        
        // unsupported $mode
        return false;
    } 
}
 
//Setup VIM: ex: et ts=4 enc=utf-8 :
?>
