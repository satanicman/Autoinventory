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

(function(){var a=tinymce.dom.Event;tinymce.create("tinymce.plugins.NonEditablePlugin",{init:function(d,e){var f=this,c,b,g;f.editor=d;c=d.getParam("noneditable_editable_class","mceEditable");b=d.getParam("noneditable_noneditable_class","mceNonEditable");d.onNodeChange.addToTop(function(i,h,l){var k,j;k=i.dom.getParent(i.selection.getStart(),function(m){return i.dom.hasClass(m,b)});j=i.dom.getParent(i.selection.getEnd(),function(m){return i.dom.hasClass(m,b)});if(k||j){g=1;f._setDisabled(1);return false}else{if(g==1){f._setDisabled(0);g=0}}})},getInfo:function(){return{longname:"Non editable elements",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/noneditable",version:tinymce.majorVersion+"."+tinymce.minorVersion}},_block:function(c,d){var b=d.keyCode;if((b>32&&b<41)||(b>111&&b<124)){return}return a.cancel(d)},_setDisabled:function(d){var c=this,b=c.editor;tinymce.each(b.controlManager.controls,function(e){e.setDisabled(d)});if(d!==c.disabled){if(d){b.onKeyDown.addToTop(c._block);b.onKeyPress.addToTop(c._block);b.onKeyUp.addToTop(c._block);b.onPaste.addToTop(c._block);b.onContextMenu.addToTop(c._block)}else{b.onKeyDown.remove(c._block);b.onKeyPress.remove(c._block);b.onKeyUp.remove(c._block);b.onPaste.remove(c._block);b.onContextMenu.remove(c._block)}c.disabled=d}}});tinymce.PluginManager.add("noneditable",tinymce.plugins.NonEditablePlugin)})();