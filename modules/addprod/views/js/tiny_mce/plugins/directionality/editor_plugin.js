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

(function(){tinymce.create("tinymce.plugins.Directionality",{init:function(a,b){var c=this;c.editor=a;a.addCommand("mceDirectionLTR",function(){var d=a.dom.getParent(a.selection.getNode(),a.dom.isBlock);if(d){if(a.dom.getAttrib(d,"dir")!="ltr"){a.dom.setAttrib(d,"dir","ltr")}else{a.dom.setAttrib(d,"dir","")}}a.nodeChanged()});a.addCommand("mceDirectionRTL",function(){var d=a.dom.getParent(a.selection.getNode(),a.dom.isBlock);if(d){if(a.dom.getAttrib(d,"dir")!="rtl"){a.dom.setAttrib(d,"dir","rtl")}else{a.dom.setAttrib(d,"dir","")}}a.nodeChanged()});a.addButton("ltr",{title:"directionality.ltr_desc",cmd:"mceDirectionLTR"});a.addButton("rtl",{title:"directionality.rtl_desc",cmd:"mceDirectionRTL"});a.onNodeChange.add(c._nodeChange,c)},getInfo:function(){return{longname:"Directionality",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/directionality",version:tinymce.majorVersion+"."+tinymce.minorVersion}},_nodeChange:function(b,a,e){var d=b.dom,c;e=d.getParent(e,d.isBlock);if(!e){a.setDisabled("ltr",1);a.setDisabled("rtl",1);return}c=d.getAttrib(e,"dir");a.setActive("ltr",c=="ltr");a.setDisabled("ltr",0);a.setActive("rtl",c=="rtl");a.setDisabled("rtl",0)}});tinymce.PluginManager.add("directionality",tinymce.plugins.Directionality)})();