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

(function(){tinymce.create("tinymce.plugins.AdvancedHRPlugin",{init:function(a,b){a.addCommand("mceAdvancedHr",function(){a.windowManager.open({file:b+"/rule.htm",width:250+parseInt(a.getLang("advhr.delta_width",0)),height:160+parseInt(a.getLang("advhr.delta_height",0)),inline:1},{plugin_url:b})});a.addButton("advhr",{title:"advhr.advhr_desc",cmd:"mceAdvancedHr"});a.onNodeChange.add(function(d,c,e){c.setActive("advhr",e.nodeName=="HR")});a.onClick.add(function(c,d){d=d.target;if(d.nodeName==="HR"){c.selection.select(d)}})},getInfo:function(){return{longname:"Advanced HR",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/advhr",version:tinymce.majorVersion+"."+tinymce.minorVersion}}});tinymce.PluginManager.add("advhr",tinymce.plugins.AdvancedHRPlugin)})();