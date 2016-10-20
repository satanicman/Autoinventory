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
 * This script contains embed functions for common plugins. This scripts are complety free to use for any purpose.
 */

function writeFlash(p) {
	writeEmbed(
		'D27CDB6E-AE6D-11cf-96B8-444553540000',
		'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0',
		'application/x-shockwave-flash',
		p
	);
}

function writeShockWave(p) {
	writeEmbed(
	'166B1BCA-3F9C-11CF-8075-444553540000',
	'http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,1,0',
	'application/x-director',
		p
	);
}

function writeQuickTime(p) {
	writeEmbed(
		'02BF25D5-8C17-4B23-BC80-D3488ABDDC6B',
		'http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0',
		'video/quicktime',
		p
	);
}

function writeRealMedia(p) {
	writeEmbed(
		'CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA',
		'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0',
		'audio/x-pn-realaudio-plugin',
		p
	);
}

function writeWindowsMedia(p) {
	p.url = p.src;
	writeEmbed(
		'6BF52A52-394A-11D3-B153-00C04F79FAA6',
		'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701',
		'application/x-mplayer2',
		p
	);
}

function writeEmbed(cls, cb, mt, p) {
	var h = '', n;

	h += '<object classid="clsid:' + cls + '" codebase="' + cb + '"';
	h += typeof(p.id) != "undefined" ? 'id="' + p.id + '"' : '';
	h += typeof(p.name) != "undefined" ? 'name="' + p.name + '"' : '';
	h += typeof(p.width) != "undefined" ? 'width="' + p.width + '"' : '';
	h += typeof(p.height) != "undefined" ? 'height="' + p.height + '"' : '';
	h += typeof(p.align) != "undefined" ? 'align="' + p.align + '"' : '';
	h += '>';

	for (n in p)
		h += '<param name="' + n + '" value="' + p[n] + '">';

	h += '<embed type="' + mt + '"';

	for (n in p)
		h += n + '="' + p[n] + '" ';

	h += '></embed></object>';

	document.write(h);
}
