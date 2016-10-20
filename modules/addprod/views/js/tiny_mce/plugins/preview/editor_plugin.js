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

(function(){tinymce.create("tinymce.plugins.Preview",{init:function(a,b){var d=this,c=tinymce.explode(a.settings.content_css);d.editor=a;tinymce.each(c,function(f,e){c[e]=a.documentBaseURI.toAbsolute(f)});a.addCommand("mcePreview",function(){a.windowManager.open({file:a.getParam("plugin_preview_pageurl",b+"/preview.html"),width:parseInt(a.getParam("plugin_preview_width","550")),height:parseInt(a.getParam("plugin_preview_height","600")),resizable:"yes",scrollbars:"yes",popup_css:c?c.join(","):a.baseURI.toAbsolute("themes/"+a.settings.theme+"/skins/"+a.settings.skin+"/content.css"),inline:a.getParam("plugin_preview_inline",1)},{base:a.documentBaseURI.getURI()})});a.addButton("preview",{title:"preview.preview_desc",cmd:"mcePreview"})},getInfo:function(){return{longname:"Preview",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/preview",version:tinymce.majorVersion+"."+tinymce.minorVersion}}});tinymce.PluginManager.add("preview",tinymce.plugins.Preview)})();