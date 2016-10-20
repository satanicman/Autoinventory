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

(function(){tinymce.create("tinymce.plugins.PageBreakPlugin",{init:function(b,d){var f='<img src="'+b.theme.url+'/img/trans.gif" class="mcePageBreak mceItemNoResize" />',a="mcePageBreak",c=b.getParam("pagebreak_separator","<!-- pagebreak -->"),e;e=new RegExp(c.replace(/[\?\.\*\[\]\(\)\{\}\+\^\$\:]/g,function(g){return"\\"+g}),"g");b.addCommand("mcePageBreak",function(){b.execCommand("mceInsertContent",0,f)});b.addButton("pagebreak",{title:"pagebreak.desc",cmd:a});b.onInit.add(function(){if(b.theme.onResolveName){b.theme.onResolveName.add(function(g,h){if(h.node.nodeName=="IMG"&&b.dom.hasClass(h.node,a)){h.name="pagebreak"}})}});b.onClick.add(function(g,h){h=h.target;if(h.nodeName==="IMG"&&g.dom.hasClass(h,a)){g.selection.select(h)}});b.onNodeChange.add(function(h,g,i){g.setActive("pagebreak",i.nodeName==="IMG"&&h.dom.hasClass(i,a))});b.onBeforeSetContent.add(function(g,h){h.content=h.content.replace(e,f)});b.onPostProcess.add(function(g,h){if(h.get){h.content=h.content.replace(/<img[^>]+>/g,function(i){if(i.indexOf('class="mcePageBreak')!==-1){i=c}return i})}})},getInfo:function(){return{longname:"PageBreak",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/pagebreak",version:tinymce.majorVersion+"."+tinymce.minorVersion}}});tinymce.PluginManager.add("pagebreak",tinymce.plugins.PageBreakPlugin)})();