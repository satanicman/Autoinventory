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

/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.Directionality', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			ed.addCommand('mceDirectionLTR', function() {
				var e = ed.dom.getParent(ed.selection.getNode(), ed.dom.isBlock);

				if (e) {
					if (ed.dom.getAttrib(e, "dir") != "ltr")
						ed.dom.setAttrib(e, "dir", "ltr");
					else
						ed.dom.setAttrib(e, "dir", "");
				}

				ed.nodeChanged();
			});

			ed.addCommand('mceDirectionRTL', function() {
				var e = ed.dom.getParent(ed.selection.getNode(), ed.dom.isBlock);

				if (e) {
					if (ed.dom.getAttrib(e, "dir") != "rtl")
						ed.dom.setAttrib(e, "dir", "rtl");
					else
						ed.dom.setAttrib(e, "dir", "");
				}

				ed.nodeChanged();
			});

			ed.addButton('ltr', {title : 'directionality.ltr_desc', cmd : 'mceDirectionLTR'});
			ed.addButton('rtl', {title : 'directionality.rtl_desc', cmd : 'mceDirectionRTL'});

			ed.onNodeChange.add(t._nodeChange, t);
		},

		getInfo : function() {
			return {
				longname : 'Directionality',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/directionality',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},

		// Private methods

		_nodeChange : function(ed, cm, n) {
			var dom = ed.dom, dir;

			n = dom.getParent(n, dom.isBlock);
			if (!n) {
				cm.setDisabled('ltr', 1);
				cm.setDisabled('rtl', 1);
				return;
			}

			dir = dom.getAttrib(n, 'dir');
			cm.setActive('ltr', dir == "ltr");
			cm.setDisabled('ltr', 0);
			cm.setActive('rtl', dir == "rtl");
			cm.setDisabled('rtl', 0);
		}
	});

	// Register plugin
	tinymce.PluginManager.add('directionality', tinymce.plugins.Directionality);
})();