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

(function(){tinymce.create("tinymce.plugins.Save",{init:function(a,b){var c=this;c.editor=a;a.addCommand("mceSave",c._save,c);a.addCommand("mceCancel",c._cancel,c);a.addButton("save",{title:"save.save_desc",cmd:"mceSave"});a.addButton("cancel",{title:"save.cancel_desc",cmd:"mceCancel"});a.onNodeChange.add(c._nodeChange,c);a.addShortcut("ctrl+s",a.getLang("save.save_desc"),"mceSave")},getInfo:function(){return{longname:"Save",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/save",version:tinymce.majorVersion+"."+tinymce.minorVersion}},_nodeChange:function(b,a,c){var b=this.editor;if(b.getParam("save_enablewhendirty")){a.setDisabled("save",!b.isDirty());a.setDisabled("cancel",!b.isDirty())}},_save:function(){var c=this.editor,a,e,d,b;a=tinymce.DOM.get(c.id).form||tinymce.DOM.getParent(c.id,"form");if(c.getParam("save_enablewhendirty")&&!c.isDirty()){return}tinyMCE.triggerSave();if(e=c.getParam("save_onsavecallback")){if(c.execCallback("save_onsavecallback",c)){c.startContent=tinymce.trim(c.getContent({format:"raw"}));c.nodeChanged()}return}if(a){c.isNotDirty=true;if(a.onsubmit==null||a.onsubmit()!=false){a.submit()}c.nodeChanged()}else{c.windowManager.alert("Error: No form element found.")}},_cancel:function(){var a=this.editor,c,b=tinymce.trim(a.startContent);if(c=a.getParam("save_oncancelcallback")){a.execCallback("save_oncancelcallback",a);return}a.setContent(b);a.undoManager.clear();a.nodeChanged()}});tinymce.PluginManager.add("save",tinymce.plugins.Save)})();