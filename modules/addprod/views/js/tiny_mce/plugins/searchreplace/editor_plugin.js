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

(function(){tinymce.create("tinymce.plugins.SearchReplacePlugin",{init:function(a,c){function b(d){window.focus();a.windowManager.open({file:c+"/searchreplace.htm",width:420+parseInt(a.getLang("searchreplace.delta_width",0)),height:170+parseInt(a.getLang("searchreplace.delta_height",0)),inline:1,auto_focus:0},{mode:d,search_string:a.selection.getContent({format:"text"}),plugin_url:c})}a.addCommand("mceSearch",function(){b("search")});a.addCommand("mceReplace",function(){b("replace")});a.addButton("search",{title:"searchreplace.search_desc",cmd:"mceSearch"});a.addButton("replace",{title:"searchreplace.replace_desc",cmd:"mceReplace"});a.addShortcut("ctrl+f","searchreplace.search_desc","mceSearch")},getInfo:function(){return{longname:"Search/Replace",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/searchreplace",version:tinymce.majorVersion+"."+tinymce.minorVersion}}});tinymce.PluginManager.add("searchreplace",tinymce.plugins.SearchReplacePlugin)})();