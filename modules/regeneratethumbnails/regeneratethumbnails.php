<?php
/**
 * Regenerate thumbnails
 * Allows you to regenerate thumbnails
 * if the prestashop's method doesn't work.
 * 
 * @category Prestashop
 * @category Module
 * @author Samdha <contact@samdha.net>
 * @copyright Samdha
 * @license commercial license see license.txt
 * @author logo Alessandro Rei
 * @license logo http://www.gnu.org/copyleft/gpl.html GPLv3
 * @version 1.3.5.0
**/
if (!class_exists('regeneratethumbnails', false)) {

	if (!defined('_PS_MODULE_DIR_')) // PS 1.0
		define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR);
	require_once(_PS_MODULE_DIR_.'regeneratethumbnails'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'modulesamdha.php');
	class regeneratethumbnails extends modulesamdha
	{
		public $shortName = 'regenthb';
		private $dirs = array(
				'categories' => '_PS_CAT_IMG_DIR_',
				'manufacturers' => '_PS_MANU_IMG_DIR_',
				'suppliers' => '_PS_SUPP_IMG_DIR_',
				'scenes' => '_PS_SCENE_IMG_DIR_',
				'products' => '_PS_PROD_IMG_DIR_',
				'stores' => '_PS_STORE_IMG_DIR_'
		);
	
		public function __construct() 
		{
			$this->name = 'regeneratethumbnails';
			$this->tab = version_compare(_PS_VERSION_, '1.4.0.0', '<')?'Tools':'administration';
			$this->version = '1.3.5.0';
			$this->module_key = 'a982656ba7822fea24fa802670384ba2';
	
			parent::__construct();
	
			$this->displayName = $this->l('Regenerate thumbnails');
			$this->description = $this->l('Allows you to regenerate thumbnails if the prestashop\'s method doesn\'t work.');
		}
		
		public function _postProcess($token) 
		{
			if (Tools::getValue('ajax')) {
				if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
					@ob_end_clean();
				}
				if (version_compare(_PS_VERSION_, '1.5.3.0', '>=')) {
					register_shutdown_function(array($this, 'fixDisplayFatalError'));
				}
				ob_start();
    			header("Content-Type: application/json");
				echo $this->regenerateThumbnail(Tools::getValue('type'),
										   Tools::getValue('format'),
										   Tools::getValue('number'));
				die();
			}
			
			return parent::_postProcess($token);
		}
		
		public function _displayForm($token, $big = true, $space = false) 
		{
			global $currentIndex;
			$output = parent::_displayForm($token, false, false);

			$types = array(
				'categories' => $this->l('Categories'),
				'manufacturers' => $this->l('Manufacturers'),
				'suppliers' => $this->l('Suppliers'),
				'scenes' => $this->l('Scenes'),
				'products' => $this->l('Products'),
				'stores' => $this->l('Stores'),
				'nopicture' => $this->l('No picture images'), // @since 1.3.1.0
			);
			
			// remove unexisting folders
			
			foreach ($this->dirs as $type => $dir)
				if (!defined($dir))
					unset($types[$type]);
	
			$output .= '
			<form id="regeneratethumbnails" action="" method="post" onsubmit="return false;">
				<fieldset class="width3" id="regeneratethumbnails_form">
					<legend>'.$this->l('Regenerate thumbnails').'</legend><br />
					<label>'.$this->l('Select image:').'</label>
					<div class="margin-form">
						<select id="regeneratethumbnails_type" name="type" onchange="changeFormat(this)">
							<option value="all">'.$this->l('All').'</option>';
					foreach ($types as $k => $type)
						$output .= '<option value="'.$k.'">'.$type.'</option>';
					$output .= '
						</select>
					</div>';
					
				foreach ($types as $k => $type)
				{
					$output .= '
					<label class="second-select format_'.$k.'" style="display:none;">'.$this->l('Select format:').'</label>
					<div class="second-select margin-form format_'.$k.'" style="display:none;">
					<select class="second-select format_'.$k.'" name="format_'.Tools::strtolower($type).'">
						<option value="all">'.$this->l('All').'</option>';
					$formats = ImageType::getImagesTypes($k != 'nopicture'?$k:null);
					foreach ($formats as $format)
						$output .= '<option value="'.$format['id_image_type'].'">'.$format['name'].'</option>';
					$output .= '</select></div>';
				}
				$output .= '
					<script type="text/javascript">
						function changeFormat(elt)
						{
							$elt = $(elt);
							$(\'.second-select\').hide();
							$(\'.format_\' + $elt.val()).show();
						}
					</script>
					<input type="submit" name="submitRegenerate" value="'.$this->l('Regenerate thumbnails').'" class="button space" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');" />
				</fieldset>
				<fieldset class="width3" id="regeneratethumbnails_log" style="display: none;">
					<legend>'.$this->l('Regeneration status').'</legend><br />
					<table class="table">
						<tr class="first">
							<th>'.$this->l('Image').'</th>
							<th>'.$this->l('Format').'</th>
							<th>'.$this->l('Status').'</th>
							<th>'.$this->l('Progress').'</th>
						</tr>
					</table>
					<p class="errors" style="display: none;"><b>'.$this->l('Errors').'</b><br/></p>
				</fieldset>
			</form>
			<br class="clear" />
			<script type="text/javascript" src="'.$this->_path.'js/jProgressBar.js"></script>
			<style type="text/css">
				span.progress .border {
					background-color: #000000;
				}
				span.progress .border .background {
					background-color: #ffffff;
				}
				span.progress .border .background .bar {
					background: #6dd436;
				}
			</style>
			<script type="text/javascript"><!--//
				/*
				 * jQuery AjaxQ - AJAX request queueing for jQuery
				 *
				 * Version: 0.0.1
				 * Date: July 22, 2008
				 *
				 * Copyright (c) 2008 Oleg Podolsky (oleg.podolsky@gmail.com)
				 * Licensed under the MIT (MIT-LICENSE.txt) license.
				 *
				 * http://plugins.jquery.com/project/ajaxq
				 * http://code.google.com/p/jquery-ajaxq/
				 */
				
				jQuery.ajaxq = function (queue, options)
				{
					// Initialize storage for request queues if it\'s not initialized yet
					if (typeof document.ajaxq == "undefined") document.ajaxq = {q:{}, r:null};
				
					// Initialize current queue if it\'s not initialized yet
					if (typeof document.ajaxq.q[queue] == "undefined") document.ajaxq.q[queue] = [];
					
					if (typeof options != "undefined") // Request settings are given, enqueue the new request
					{
						// Copy the original options, because options.complete is going to be overridden
				
						var optionsCopy = {};
						for (var o in options) optionsCopy[o] = options[o];
						options = optionsCopy;
						
						// Override the original callback
				
						var originalCompleteCallback = options.complete;
				
						options.complete = function (request, status)
						{
							// Dequeue the current request
							document.ajaxq.q[queue].shift ();
							document.ajaxq.r = null;
							
							// Run the original callback
							if (originalCompleteCallback) originalCompleteCallback (request, status);
				
							// Run the next request from the queue
							if (document.ajaxq.q[queue].length > 0) document.ajaxq.r = jQuery.ajax (document.ajaxq.q[queue][0]);
						};
				
						// Enqueue the request
						document.ajaxq.q[queue].push (options);
				
						// Also, if no request is currently running, start it
						if (document.ajaxq.q[queue].length == 1) document.ajaxq.r = jQuery.ajax (options);
					}
					else // No request settings are given, stop current request and clear the queue
					{
						if (document.ajaxq.r)
						{
							document.ajaxq.r.abort ();
							document.ajaxq.r = null;
						}
				
						document.ajaxq.q[queue] = [];
					}
				}
				
				var regeneratethumbnails_working = 0;
				$(document).ready(function() {
					$("#regeneratethumbnails").submit(function () {
						$("#regeneratethumbnails_log tr:not(.first)").remove();
						$("#regeneratethumbnails_log").show();
						$("#regeneratethumbnails_form").hide();
						if ($("#regeneratethumbnails_type").val() == "all") {
							$("#regeneratethumbnails_type option").each(function () {
								if ($(this).val() != "all")
									regeneratethumbnails_1($(this).val());
							});
						} else {
							regeneratethumbnails_1($("#regeneratethumbnails_type").val());
						}
						return false;
					});
				});
				function regeneratethumbnails_1(type) {
					if ($("select.format_"+type).val() == "all") {
						$("select.format_"+type+" option").each(function () {
							if ($(this).val() != "all")
								regeneratethumbnails_2(type, $(this).val());
						});
					} else {
						regeneratethumbnails_2(type, $("select.format_"+type).val());
					}
				}
				function regeneratethumbnails_2(type, format) {
					regeneratethumbnails_working++;
					$("#regeneratethumbnails_log table.table").append("<tr id=\'regeneratethumbnails_"+type+"_"+format+"\'><td>"+$("#regeneratethumbnails_type option[value="+type+"]").text()+"<\/td><td>"+$("select.format_"+type+" option[value="+format+"]").text()+"<\/td><td class=\'text\'><\/td><td><span class=\'progress\'></span><\/td><\/tr>");
					var bar = $("#regeneratethumbnails_"+type+"_"+format+" .progress").jProgressBar(0);
					regeneratethumbnails_3(type, format, 1);
				}
				function regeneratethumbnails_3(type, format, number) {
					var bar = $("#regeneratethumbnails_"+type+"_"+format+" .progress").jProgressBar();
					$.ajaxq("queue"+Math.floor(Math.random()*3), {
						type: "GET",
						url: "'.$currentIndex.'&configure='.$this->name.'&token='.$token.'",
						data: {
							"ajax": 1,
							"type": type,
							"format": format,
							"number": number
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							$(".errors").show().append(type+" "+format+" "+" '.$this->l('Image').' "+number+" : "+textStatus+" "+XMLHttpRequest.responseText+"<br/>");
							regeneratethumbnails_3(type, format, number + 1);
						},
						success: function(data) {
							if (!data.error) {
								$("#regeneratethumbnails_"+type+"_"+format+" .text").text("'.$this->l('Image').' "+number+" '.$this->l('of').' "+data.max);
							} else {
								$(".errors").show().append(data.error+"<br/>");
							}
							bar.setPercent(100*number/data.max);
							if (number < data.max)
								regeneratethumbnails_3(type, format, number + 1);
							else {
								regeneratethumbnails_working--;
								$("#regeneratethumbnails_"+type+"_"+format+" .text").text("'.$this->l('done').' (" + number + ")");
								if (regeneratethumbnails_working == 0) {
									alert("'.$this->l('done').'");
									$("#regeneratethumbnails_log").hide();
									$("#regeneratethumbnails_form").show();
								}
							}
						},
						dataType: "json"
					});
				}
			//--></script>
			';
	
			return $output;
		}
	
		private function regenerateThumbnail($type, $id_image_type, $number) 
		{
			//@ini_set('display_error', 'on');
			if (file_exists(_PS_ROOT_DIR_.'/images.inc.php'))
				require_once(_PS_ROOT_DIR_.'/images.inc.php');
			else
				require_once(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'images.inc.php');
				
			if ($type == 'nopicture')
				return $this->regenerateNoPictureImages($id_image_type, $number);

			if (!isset($this->dirs[$type]))
				return json_encode(array('error' => $this->l('Type unknowed').' '.$type,
										 'max' => 0));
						
			$imagesTypes = ImageType::getImagesTypes($type);
			$imageType = NULL;
			foreach ($imagesTypes as $i) {
				if ($i['id_image_type'] == $id_image_type) {
					$imageType = $i;
					break;
				}
			}
			if (!$imageType)
				return json_encode(array('error' => $this->l('Format unknowed').' '.$id_image_type,
										 'max' => 0));
				
			if (($type == 'products')
				&& method_exists('Image', 'getImgFolder'))
				return $this->regenerateProducts($imageType, $number);
						
			$images = scandir(constant($this->dirs[$type]));
			$images2 = array();
			foreach ($images AS $i)
				if (preg_match('/^([0-9]+'.($type == 'products'?'\-[0-9]+':'').')\.jpg$/', $i) ||
					preg_match('/^(\w+)\.jpg$/', $i))
					$images2[] = $i;
					
			if ($number > count($images2))
				return json_encode(array('error' => $this->l('Image unknowed').' '.$number,
										 'max' => count($images2)));
			
			// let's begin
			// get object ID
			if (!preg_match('/^([0-9]+'.($type == 'products'?'\-[0-9]+':'').')\.jpg$/', $images2[$number - 1], $matches) &&
				!preg_match('/^(\w+)\.jpg$/', $images2[$number - 1], $matches))
				return json_encode(array('error' => $this->l('Can\'t find image').' '.$number,
										 'max' => count($images2)));
			$id_object = $matches[1];
			if (!@getimagesize(constant($this->dirs[$type]).$id_object.'.jpg'))
				return json_encode(array('error' => $this->l('Image invalid:').' '.constant($this->dirs[$type]).$id_object.'.jpg',
										 'max' => count($images2)));
	
			// delete old images
			if (file_exists(constant($this->dirs[$type]).$id_object.'-'.$imageType['name'].'.jpg'))
				@unlink(constant($this->dirs[$type]).$id_object.'-'.$imageType['name'].'.jpg');
			
			// regenerate images
			if (class_exists('ImageManager')) {
				$result = ImageManager::resize(constant($this->dirs[$type]).$id_object.'.jpg', constant($this->dirs[$type]).$id_object.'-'.(is_numeric($id_object[0])?'':'default-').stripslashes($imageType['name']).'.jpg', intval($imageType['width']), intval($imageType['height']));
			} else {
				$result = imageResize(constant($this->dirs[$type]).$id_object.'.jpg', constant($this->dirs[$type]).$id_object.'-'.(is_numeric($id_object[0])?'':'default-').stripslashes($imageType['name']).'.jpg', intval($imageType['width']), intval($imageType['height']));
			}
			if (!$result)
				return json_encode(array('error' => $this->l('Can\'t regenerate image').' '.constant($this->dirs[$type]).$id_object.'-'.(is_numeric($id_object[0])?'':'default-').stripslashes($imageType['name']).'.jpg'.' '.$this->l('from').' '.constant($this->dirs[$type]).$id_object.'.jpg',
										 'max' => count($images2)));
	
			// regenerate watermarks
			if ($type == 'products' && is_numeric($id_object[0])) {
				$result = Db::getInstance()->ExecuteS('
					SELECT m.`name` FROM `'._DB_PREFIX_.'module` m
					LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
					LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
					WHERE h.`name` = \'watermark\' AND m.`active` = 1');
				if ($result AND sizeof($result))
					foreach ($result AS $k => $module)
						if ($moduleInstance = Module::getInstanceByName($module['name']) AND is_callable(array($moduleInstance, 'hookwatermark'))) {
							list($id_product, $id_image) = explode('-', $id_object);
							call_user_func(array($moduleInstance, 'hookwatermark'), array('id_image' => $id_image, 'id_product' => $id_product));
						}
			}
			
			return json_encode(array('error' => '',
									 'max' => count($images2)));
		}
		
		private function regenerateProducts($imageType, $number) 
		{
			$dir = constant($this->dirs['products']);
			$productsImages = Image::getAllImages();
			if (!array_key_exists($number - 1, $productsImages))
				return json_encode(array('error' => $this->l('Image unknowed').' '.$number,
										 'max' => count($productsImages)));
				
			$image = $productsImages[$number - 1];
			$imageObj = new Image($image['id_image']);
			$imageObj->id_product = $image['id_product'];
			
			if (file_exists($dir.$imageObj->getImgFolder().DIRECTORY_SEPARATOR.$imageObj->id.'\-'.stripslashes($imageType['name']).'.jpg'))
				unlink($dir.$imageObj->getImgFolder().DIRECTORY_SEPARATOR.$imageObj->id.'\-'.stripslashes($imageType['name']).'.jpg');
			if (file_exists($dir.$imageObj->getImgFolder().DIRECTORY_SEPARATOR.$imageObj->id_product.'\-'.$imageObj->id.'\-'.stripslashes($imageType['name']).'.jpg'))
				unlink($dir.$imageObj->getImgFolder().DIRECTORY_SEPARATOR.$imageObj->id_product.'\-'.$imageObj->id.'\-'.stripslashes($imageType['name']).'.jpg');
			if (file_exists($dir.DIRECTORY_SEPARATOR.$imageObj->id_product.'\-'.$imageObj->id.'\-'.stripslashes($imageType['name']).'.jpg'))
				unlink($dir.DIRECTORY_SEPARATOR.$imageObj->id_product.'\-'.$imageObj->id.'\-'.stripslashes($imageType['name']).'.jpg');
				
			if (file_exists($dir.$imageObj->getExistingImgPath().'.jpg')) {
				if (!@getimagesize($dir.$imageObj->getExistingImgPath().'.jpg'))
					return json_encode(array('error' => $this->l('Image invalid:').' '.$dir.$imageObj->getExistingImgPath().'.jpg',
											 'max' => count($productsImages)));

				if (class_exists('ImageManager')) {
					$result = ImageManager::resize($dir.$imageObj->getExistingImgPath().'.jpg', $dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height']));
				} else {
					$result = imageResize($dir.$imageObj->getExistingImgPath().'.jpg', $dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height']));					
				}
				if (!$result)
					return json_encode(array('error' => $this->l('Can\'t regenerate image').' '.$dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg '.$this->l('from').' '.$dir.$imageObj->getExistingImgPath().'.jpg',
											 'max' => count($productsImages)));
					
				$result = Db::getInstance()->ExecuteS('
					SELECT m.`name` FROM `'._DB_PREFIX_.'module` m
					LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
					LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
					WHERE (h.`name` = \'actionWatermark\' OR h.`name` = \'watermark\') AND m.`active` = 1');
				if ($result AND sizeof($result))
					foreach ($result AS $k => $module)
						if ($moduleInstance = Module::getInstanceByName($module['name'])) {
							if (is_callable(array($moduleInstance, 'hookActionWatermark'))) {
								@call_user_func(array($moduleInstance, 'hookActionWatermark'), array('id_image' => $imageObj->id, 'id_product' => $imageObj->id_product));
							} elseif (is_callable(array($moduleInstance, 'hookwatermark'))) {
								@call_user_func(array($moduleInstance, 'hookwatermark'), array('id_image' => $imageObj->id, 'id_product' => $imageObj->id_product));
							}
						}
			} else
				return json_encode(array('error' => $this->l('Can\'t find image').' '.$dir.$imageObj->getExistingImgPath().'.jpg',
										 'max' => count($productsImages)));
			
			return json_encode(array('error' => '',
									 'max' => count($productsImages)));
		}

		// Regenerate no-pictures images
		private function regenerateNoPictureImages($id_image_type, $number)
		{
			$imageType = new ImageType($id_image_type);
			if (Validate::isLoadedObject($imageType)) {
				$images = array();
				$languages = Language::getLanguages(false);
				foreach ($this->dirs as $type => $dir)
					if ($imageType->$type)
						foreach ($languages as $language)
							$images[] = array('dir' => constant($dir), 'iso_code' => $language['iso_code']);
				if (!array_key_exists($number - 1, $images))
					return json_encode(array('error' => $this->l('Image unknowed').' '.$number,
											 'max' => count($images)));
				$image = $images[$number - 1];
				$file = $image['dir'].$image['iso_code'].'.jpg';
				if (!file_exists($file))
					$file = _PS_PROD_IMG_DIR_.Language::getIsoById((int)(Configuration::get('PS_LANG_DEFAULT'))).'.jpg';
				if (file_exists($image['dir'].$image['iso_code'].'-default-'.stripslashes($imageType->name).'.jpg'))
					@unlink($image['dir'].$image['iso_code'].'-default-'.stripslashes($imageType->name).'.jpg');
				if (class_exists('ImageManager')) {
					$result = ImageManager::resize($file, $image['dir'].$image['iso_code'].'-default-'.stripslashes($imageType->name).'.jpg', (int) $imageType->width, (int) $imageType->height);
				} else {
					$result = imageResize($file, $image['dir'].$image['iso_code'].'-default-'.stripslashes($imageType->name).'.jpg', (int) $imageType->width, (int) $imageType->height);
				}
				if (!$result)
					return json_encode(array('error' => $this->l('Can\'t regenerate image').' '.$image['dir'].$image['iso_code'].'-default-'.stripslashes($imageType->name).'.jpg '.$this->l('from').' '.$file,
											 'max' => count($images)));
				else
					return json_encode(array('error' => '',
											 'max' => count($images)));

			} else
				return json_encode(array('error' => $this->l('Format unknowed').' '.$id_image_type,
										 'max' => 0));
		}

		/**
		 * remove error message added by displayFatalError()
		 * in Prestashop 1.5.3.x
		 *
		 * @since 1.3.4.0
		 */
		public function fixDisplayFatalError() 
		{
			$buffer = ob_get_contents();
			$position = strpos($buffer, '}[PrestaShop] Fatal error in module ');
			if ($position !== false) {
				ob_clean();
				$buffer = substr($buffer, 0, $position + 1);
				echo $buffer;
			}
		}
	}
}
?>
