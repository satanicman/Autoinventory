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

var AdvHRDialog = {
	init : function(ed) {
		var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;

		w = dom.getAttrib(n, 'width');
		f.width.value = w ? parseInt(w) : (dom.getStyle('width') || '');
		f.size.value = dom.getAttrib(n, 'size') || parseInt(dom.getStyle('height')) || '';
		f.noshade.checked = !!dom.getAttrib(n, 'noshade') || !!dom.getStyle('border-width');
		selectByValue(f, 'width2', w.indexOf('%') != -1 ? '%' : 'px');
	},

	update : function() {
		var ed = tinyMCEPopup.editor, h, f = document.forms[0], st = '';

		h = '<hr';

		if (f.size.value) {
			h += ' size="' + f.size.value + '"';
			st += ' height:' + f.size.value + 'px;';
		}

		if (f.width.value) {
			h += ' width="' + f.width.value + (f.width2.value == '%' ? '%' : '') + '"';
			st += ' width:' + f.width.value + (f.width2.value == '%' ? '%' : 'px') + ';';
		}

		if (f.noshade.checked) {
			h += ' noshade="noshade"';
			st += ' border-width: 1px; border-style: solid; border-color: #CCCCCC; color: #ffffff;';
		}

		if (ed.settings.inline_styles)
			h += ' style="' + tinymce.trim(st) + '"';

		h += ' />';

		ed.execCommand("mceInsertContent", false, h);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(AdvHRDialog.init, AdvHRDialog);
