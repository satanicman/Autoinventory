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

(function(){var a=tinymce.dom.Event,c=tinymce.each,b=tinymce.DOM;tinymce.create("tinymce.plugins.ContextMenu",{init:function(e){var h=this,f,d,i;h.editor=e;d=e.settings.contextmenu_never_use_native;h.onContextMenu=new tinymce.util.Dispatcher(this);f=e.onContextMenu.add(function(j,k){if((i!==0?i:k.ctrlKey)&&!d){return}a.cancel(k);if(k.target.nodeName=="IMG"){j.selection.select(k.target)}h._getMenu(j).showMenu(k.clientX||k.pageX,k.clientY||k.pageY);a.add(j.getDoc(),"click",function(l){g(j,l)});j.nodeChanged()});e.onRemove.add(function(){if(h._menu){h._menu.removeAll()}});function g(j,k){i=0;if(k&&k.button==2){i=k.ctrlKey;return}if(h._menu){h._menu.removeAll();h._menu.destroy();a.remove(j.getDoc(),"click",g);h._menu=null}}e.onMouseDown.add(g);e.onKeyDown.add(g);e.onKeyDown.add(function(j,k){if(k.shiftKey&&!k.ctrlKey&&!k.altKey&&k.keyCode===121){a.cancel(k);f(j,k)}})},getInfo:function(){return{longname:"Contextmenu",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/contextmenu",version:tinymce.majorVersion+"."+tinymce.minorVersion}},_getMenu:function(e){var g=this,d=g._menu,j=e.selection,f=j.isCollapsed(),h=j.getNode()||e.getBody(),i,k;if(d){d.removeAll();d.destroy()}k=b.getPos(e.getContentAreaContainer());d=e.controlManager.createDropMenu("contextmenu",{offset_x:k.x+e.getParam("contextmenu_offset_x",0),offset_y:k.y+e.getParam("contextmenu_offset_y",0),constrain:1,keyboard_focus:true});g._menu=d;d.add({title:"advanced.cut_desc",icon:"cut",cmd:"Cut"}).setDisabled(f);d.add({title:"advanced.copy_desc",icon:"copy",cmd:"Copy"}).setDisabled(f);d.add({title:"advanced.paste_desc",icon:"paste",cmd:"Paste"});if((h.nodeName=="A"&&!e.dom.getAttrib(h,"name"))||!f){d.addSeparator();d.add({title:"advanced.link_desc",icon:"link",cmd:e.plugins.advlink?"mceAdvLink":"mceLink",ui:true});d.add({title:"advanced.unlink_desc",icon:"unlink",cmd:"UnLink"})}d.addSeparator();d.add({title:"advanced.image_desc",icon:"image",cmd:e.plugins.advimage?"mceAdvImage":"mceImage",ui:true});d.addSeparator();i=d.addMenu({title:"contextmenu.align"});i.add({title:"contextmenu.left",icon:"justifyleft",cmd:"JustifyLeft"});i.add({title:"contextmenu.center",icon:"justifycenter",cmd:"JustifyCenter"});i.add({title:"contextmenu.right",icon:"justifyright",cmd:"JustifyRight"});i.add({title:"contextmenu.full",icon:"justifyfull",cmd:"JustifyFull"});g.onContextMenu.dispatch(g,d,h,f);return d}});tinymce.PluginManager.add("contextmenu",tinymce.plugins.ContextMenu)})();