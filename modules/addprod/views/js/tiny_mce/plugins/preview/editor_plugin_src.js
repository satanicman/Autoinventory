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
	tinymce.create('tinymce.plugins.Preview', {
		init : function(ed, url) {
			var t = this, css = tinymce.explode(ed.settings.content_css);

			t.editor = ed;

			// Force absolute CSS urls	
			tinymce.each(css, function(u, k) {
				css[k] = ed.documentBaseURI.toAbsolute(u);
			});

			ed.addCommand('mcePreview', function() {
				ed.windowManager.open({
					file : ed.getParam("plugin_preview_pageurl", url + "/preview.html"),
					width : parseInt(ed.getParam("plugin_preview_width", "550")),
					height : parseInt(ed.getParam("plugin_preview_height", "600")),
					resizable : "yes",
					scrollbars : "yes",
					popup_css : css ? css.join(',') : ed.baseURI.toAbsolute("themes/" + ed.settings.theme + "/skins/" + ed.settings.skin + "/content.css"),
					inline : ed.getParam("plugin_preview_inline", 1)
				}, {
					base : ed.documentBaseURI.getURI()
				});
			});

			ed.addButton('preview', {title : 'preview.preview_desc', cmd : 'mcePreview'});
		},

		getInfo : function() {
			return {
				longname : 'Preview',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/preview',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('preview', tinymce.plugins.Preview);
})();