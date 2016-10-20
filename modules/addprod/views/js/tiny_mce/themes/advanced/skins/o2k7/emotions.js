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

tinyMCEPopup.requireLangPack();

var EmotionsDialog = {
	addKeyboardNavigation: function(){
		var tableElm, cells, settings;
			
		cells = tinyMCEPopup.dom.select("a.emoticon_link", "emoticon_table");
			
		settings ={
			root: "emoticon_table",
			items: cells
		};
		cells[0].tabindex=0;
		tinyMCEPopup.dom.addClass(cells[0], "mceFocus");
		if (tinymce.isGecko) {
			cells[0].focus();		
		} else {
			setTimeout(function(){
				cells[0].focus();
			}, 100);
		}
		tinyMCEPopup.editor.windowManager.createInstance('tinymce.ui.KeyboardNavigation', settings, tinyMCEPopup.dom);
	}, 
	init : function(ed) {
		tinyMCEPopup.resizeToInnerSize();
		this.addKeyboardNavigation();
	},

	insert : function(file, title) {
		var ed = tinyMCEPopup.editor, dom = ed.dom;

		tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
			src : tinyMCEPopup.getWindowArg('plugin_url') + '/img/' + file,
			alt : ed.getLang(title),
			title : ed.getLang(title),
			border : 0
		}));

		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(EmotionsDialog.init, EmotionsDialog);
