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
	tinymce.create('tinymce.plugins.Nonbreaking', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			// Register commands
			ed.addCommand('mceNonBreaking', function() {
				ed.execCommand('mceInsertContent', false, (ed.plugins.visualchars && ed.plugins.visualchars.state) ? '<span data-mce-bogus="1" class="mceItemHidden mceItemNbsp">&nbsp;</span>' : '&nbsp;');
			});

			// Register buttons
			ed.addButton('nonbreaking', {title : 'nonbreaking.nonbreaking_desc', cmd : 'mceNonBreaking'});

			if (ed.getParam('nonbreaking_force_tab')) {
				ed.onKeyDown.add(function(ed, e) {
					if (e.keyCode == 9) {
						e.preventDefault();
	
						ed.execCommand('mceNonBreaking');
						ed.execCommand('mceNonBreaking');
						ed.execCommand('mceNonBreaking');
					}
				});
			}
		},

		getInfo : function() {
			return {
				longname : 'Nonbreaking space',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/nonbreaking',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}

		// Private methods
	});

	// Register plugin
	tinymce.PluginManager.add('nonbreaking', tinymce.plugins.Nonbreaking);
})();