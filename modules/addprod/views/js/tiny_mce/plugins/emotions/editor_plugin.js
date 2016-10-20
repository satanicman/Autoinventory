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

(function(a){a.create("tinymce.plugins.EmotionsPlugin",{init:function(b,c){b.addCommand("mceEmotion",function(){b.windowManager.open({file:c+"/emotions.htm",width:250+parseInt(b.getLang("emotions.delta_width",0)),height:160+parseInt(b.getLang("emotions.delta_height",0)),inline:1},{plugin_url:c})});b.addButton("emotions",{title:"emotions.emotions_desc",cmd:"mceEmotion"})},getInfo:function(){return{longname:"Emotions",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/emotions",version:a.majorVersion+"."+a.minorVersion}}});a.PluginManager.add("emotions",a.plugins.EmotionsPlugin)})(tinymce);