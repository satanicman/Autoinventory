/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA    <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

(function(){tinymce.create("tinymce.plugins.WordCount",{block:0,id:null,countre:null,cleanre:null,init:function(c,d){var e=this,f=0,g=tinymce.VK;e.countre=c.getParam("wordcount_countregex",/[\w\u2019\'-]+/g);e.cleanre=c.getParam("wordcount_cleanregex",/[0-9.(),;:!?%#$?\'\"_+=\\\/-]*/g);e.update_rate=c.getParam("wordcount_update_rate",2000);e.update_on_delete=c.getParam("wordcount_update_on_delete",false);e.id=c.id+"-word-count";c.onPostRender.add(function(i,h){var j,k;k=i.getParam("wordcount_target_id");if(!k){j=tinymce.DOM.get(i.id+"_path_row");if(j){tinymce.DOM.add(j.parentNode,"div",{style:"float: right"},i.getLang("wordcount.words","Words: ")+'<span id="'+e.id+'">0</span>')}}else{tinymce.DOM.add(k,"span",{},'<span id="'+e.id+'">0</span>')}});c.onInit.add(function(h){h.selection.onSetContent.add(function(){e._count(h)});e._count(h)});c.onSetContent.add(function(h){e._count(h)});function b(h){return h!==f&&(h===g.ENTER||f===g.SPACEBAR||a(f))}function a(h){return h===g.DELETE||h===g.BACKSPACE}c.onKeyUp.add(function(h,i){if(b(i.keyCode)||e.update_on_delete&&a(i.keyCode)){e._count(h)}f=i.keyCode})},_getCount:function(c){var a=0;var b=c.getContent({format:"raw"});if(b){b=b.replace(/\.\.\./g," ");b=b.replace(/<.[^<>]*?>/g," ").replace(/&nbsp;|&#160;/gi," ");b=b.replace(/(\w+)(&.+?;)+(\w+)/,"$1$3").replace(/&.+?;/g," ");b=b.replace(this.cleanre,"");var d=b.match(this.countre);if(d){a=d.length}}return a},_count:function(a){var b=this;if(b.block){return}b.block=1;setTimeout(function(){if(!a.destroyed){var c=b._getCount(a);tinymce.DOM.setHTML(b.id,c.toString());setTimeout(function(){b.block=0},b.update_rate)}},1)},getInfo:function(){return{longname:"Word Count plugin",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/wordcount",version:tinymce.majorVersion+"."+tinymce.minorVersion}}});tinymce.PluginManager.add("wordcount",tinymce.plugins.WordCount)})();