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
tinyMCEPopup.onInit.add(onLoadInit);

function saveContent() {
	tinyMCEPopup.editor.setContent(document.getElementById('htmlSource').value, {source_view : true});
	tinyMCEPopup.close();
}

function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();

	// Remove Gecko spellchecking
	if (tinymce.isGecko)
		document.body.spellcheck = tinyMCEPopup.editor.getParam("gecko_spellcheck");

	document.getElementById('htmlSource').value = tinyMCEPopup.editor.getContent({source_view : true});

	if (tinyMCEPopup.editor.getParam("theme_advanced_source_editor_wrap", true)) {
		setWrap('soft');
		document.getElementById('wraped').checked = true;
	}

	resizeInputs();
}

function setWrap(val) {
	var v, n, s = document.getElementById('htmlSource');

	s.wrap = val;

	if (!tinymce.isIE) {
		v = s.value;
		n = s.cloneNode(false);
		n.setAttribute("wrap", val);
		s.parentNode.replaceChild(n, s);
		n.value = v;
	}
}

function toggleWordWrap(elm) {
	if (elm.checked)
		setWrap('soft');
	else
		setWrap('off');
}

function resizeInputs() {
	var vp = tinyMCEPopup.dom.getViewPort(window), el;

	el = document.getElementById('htmlSource');

	if (el) {
		el.style.width = (vp.w - 20) + 'px';
		el.style.height = (vp.h - 65) + 'px';
	}
}
