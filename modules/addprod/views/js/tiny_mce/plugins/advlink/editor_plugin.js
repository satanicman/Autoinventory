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

(function(){tinymce.create("tinymce.plugins.AdvancedLinkPlugin",{init:function(a,b){this.editor=a;a.addCommand("mceAdvLink",function(){var c=a.selection;if(c.isCollapsed()&&!a.dom.getParent(c.getNode(),"A")){return}a.windowManager.open({file:b+"/link.htm",width:480+parseInt(a.getLang("advlink.delta_width",0)),height:400+parseInt(a.getLang("advlink.delta_height",0)),inline:1},{plugin_url:b})});a.addButton("link",{title:"advlink.link_desc",cmd:"mceAdvLink"});a.addShortcut("ctrl+k","advlink.advlink_desc","mceAdvLink");a.onNodeChange.add(function(d,c,f,e){c.setDisabled("link",e&&f.nodeName!="A");c.setActive("link",f.nodeName=="A"&&!f.name)})},getInfo:function(){return{longname:"Advanced link",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advlink",version:tinymce.majorVersion+"."+tinymce.minorVersion}}});tinymce.PluginManager.add("advlink",tinymce.plugins.AdvancedLinkPlugin)})();