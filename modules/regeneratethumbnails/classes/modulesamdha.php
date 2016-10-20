<?php
/**
 * ModuleSamdha class
 *
 * @category Prestashop
 * @category Module
 * @author Samdha <contact@samdha.net>
 * @copyright Samdha
 * @version 1.3.3.0
**/

if (!class_exists('modulesamdha', false)) {
	class modulesamdha extends Module
	{
		public $_postErrors = array();
		public $_postWarnings = array(); // @since 1.2.0.0
		public $config = array();
		public $configArrays = array();
		public $shortName = 'samdha';
		public $licence_number = false;
		public $need_licence_number = true;
		public $descriptionBig = '';
		const SUPPORT_URL = 'http://support.samdha.net/';
		const INSTALL_SQL_FILE = 'install.sql';
		const UNINSTALL_SQL_FILE = 'uninstall.sql';
		const COMA_REPLACE = '%C0MA%';
		
		public $author = 'Samdha';
		public $need_instance = 0;
		public $toolbar_btn = array();
		
		public function uninstall() {
			$this->deleteConfig();
			
			return parent::uninstall();
		}
		
		public function getContent($tab = 'AdminModules') {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$context = Context::getContext();
				$cookie = $context->cookie;
				$currentIndex = AdminController::$currentIndex;
			} else
				global $currentIndex, $cookie;
			if (method_exists('Tools', 'getAdminToken'))
				$token = Tools::getAdminToken($tab.intval(Tab::getIdFromClassName($tab)).intval($cookie->id_employee));
			else
				$token = 1;
			
			$this->getConfig();
			$this->_postProcess($token);

			if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
				$output = '<h2>'.$this->displayName.'</h2>';
			else {
				if ((@ini_get('allow_url_fopen')
					 || $this->cURLcheckBasicFunctions())
					  && $this->need_licence_number)
					if (!$this->checkLicence()) {
						$domain = $this->getHttpHost();
						$iso_lang = Language::getIsoById($cookie->id_lang);
						$employee = new Employee($cookie->id_employee);
						$this->toolbar_btn['register'] = array(
							'href' => self::SUPPORT_URL.'index.php/support/licence/?email='.urlencode($employee->email).'&amp;lang='.$iso_lang.'&amp;module_name='.urlencode($this->name).'&amp;domain='.urlencode($domain),
							'desc' => $this->l('Register this module', 'modulesamdha'),
							'js' => 'window.open(this.href); return false;');
					}
					elseif ($this->checkModuleVersion() == 'NEED_UPDATE')
						$this->toolbar_btn['update'] = array(
							'href' => $currentIndex.'&amp;configure='.$this->name.'&amp;updateModule=1&amp;token='.$token,
							'desc' => $this->l('Update this module now', 'modulesamdha'));

				$this->context->smarty->assign(array(
					'toolbar_btn' => $this->toolbar_btn,
					'toolbar_scroll' => true,
					'title' => $this->displayName,
					'table' => $this->shortName
					));
				$output = $this->context->smarty->fetch('toolbar.tpl');
				$output .= '
					<style type="text/css">
						.toolbarBox .process-icon-register {
							background-image: url("'.self::SUPPORT_URL.'img/Gnome-Dialog-Password-32.png");
						}
						.toolbarBox .process-icon-update {
							background-image: url("'.self::SUPPORT_URL.'img/Gnome-System-Software-Update-32.png");
						}
					</style>
				';
			}
			$output .= $this->_displayWarnings(); // @since 1.2.0.0
			$output .= $this->_displayErrors();

			$output .= $this->_displayForm($token);
	
			return $output;
		}
		
		public function _postProcess($token) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$context = Context::getContext();
				$cookie = $context->cookie;
				$currentIndex = AdminController::$currentIndex;
			} else
				global $currentIndex, $cookie;
			if (Tools::isSubmit('saveSettings')) {
				$this->saveConfig();
				Tools::redirectAdmin($currentIndex.'&module_name='.$this->name.'&configure='.$this->name.'&conf=6&token='.$token);
			}
			
			if (Tools::isSubmit('saveLicence')) {
				$this->saveLicence();
				Tools::redirectAdmin($currentIndex.'&module_name='.$this->name.'&configure='.$this->name.'&conf=6&token='.$token);
			}
			
			if (Tools::getValue('updateModule')) {
				if ($this->updateModule())
					Tools::redirectAdmin($currentIndex.'&module_name='.$this->name.'&configure='.$this->name.'&afterUpdateModule=1&token='.$token);
				else
					$this->_postErrors[] = $this->l('Can\'t update the module.', 'modulesamdha');
			}

			if (Tools::getValue('afterUpdateModule')) {
				$this->afterUpdateModule();
				Tools::redirectAdmin($currentIndex.'&module_name='.$this->name.'&configure='.$this->name.'&conf=4&token='.$token);
			}
			
			if ((@ini_get('allow_url_fopen')
				 || $this->cURLcheckBasicFunctions())
				  && $this->need_licence_number) {

				if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) { // for translation with Prestashop 1.x
					$tmp_page = $this->page;
					$this->page = 'modulesamdha';
				}

				if (!$this->checkLicence()) {
					$domain = $this->getHttpHost();
					$iso_lang = Language::getIsoById($cookie->id_lang);
					$employee = new Employee($cookie->id_employee);
					$this->_postWarnings[] = '<a style="text-decoration: none;" href="'.self::SUPPORT_URL.'index.php/support/licence/?email='.urlencode($employee->email).'&amp;lang='.$iso_lang.'&amp;module_name='.urlencode($this->name).'&amp;domain='.urlencode($domain).'" target="_blank">'.$this->l('This module is not registered. Why do not do it now ? It\'s free.', 'modulesamdha') .'</a> <a href="http://documentation.samdha.net/'.$iso_lang.'/faq#register"><img src="'._PS_ADMIN_IMG_.'help.png" style="vertical-align: middle" width="16" height="16" alt="?" /></a>';
				}
				elseif ($this->checkModuleVersion() == 'NEED_UPDATE')
					$this->_postWarnings[] = $this->l('There is a new version of this module. you can', 'modulesamdha')
								.' <a style="text-decoration: underline;" href="'.$currentIndex.'&amp;configure='.$this->name.'&amp;updateModule=1&amp;token='.$token.'">'.$this->l('update now', 'modulesamdha').'</a>.';

				if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) // for translation with Prestashop 1.x
					$this->page = $tmp_page;
			}
		}
	
		public function _displayErrors() {
			$nbErrors = sizeof($this->_postErrors);
			$output = '';
			if ($nbErrors) {
				if (method_exists($this, 'displayError'))
					foreach ($this->_postErrors as $error)
						$output .= $this->displayError($error);
				else {
					if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) { // for translation with Prestashop 1.x
						$tmp_page = $this->page;
						$this->page = 'modulesamdha';
					}

					$output .= '
						<p class="warning clear" style="width: auto">
							<h3>'.($nbErrors > 1 ? $this->l('There are', 'modulesamdha') : $this->l('There is', 'modulesamdha')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors', 'modulesamdha') : $this->l('error', 'modulesamdha')).'</h3>
							<ol>';
						foreach ($this->_postErrors AS $error)
							$output .= '<li>'.$error.'</li>';
						$output .= '
							</ol>
						</div>';

					if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) // for translation with Prestashop 1.x
						$this->page = $tmp_page;
				}
			}
			return $output;
		}
		
		// @since 1.2.0.0
		public function _displayWarnings() {
			$output = '';
			if (!empty($this->_postWarnings)) {
				if (method_exists($this, 'displayWarning'))
					foreach ($this->_postWarnings as $warn)
						$output .= $this->displayWarning($warn);
				else {
					if (version_compare(_PS_VERSION_, '1.4.0.0', '<')) {
						if (!defined('_PS_ADMIN_IMG_')) // PS 1.0
							define('_PS_ADMIN_IMG_',   _PS_IMG_.'admin/');

						$output .= '<style>#content .warn {border: 1px solid #D3C200;background-color: #FFFAC6;color: #383838;font-weight: 700;margin: 0 0 10px 0;line-height: 20px;padding: 10px 15px;}</style>';
						foreach ($this->_postWarnings as $warn)
							$output .= '<div class="warn clear" style="margin-bottom: 10px;"><img src="http://addons.prestashop.com/img/admin/warn2.png"> '.$warn.'</div>';
					} elseif (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
						foreach ($this->_postWarnings as $warn)
							$output .= '<div class="warn clear" style="margin-bottom: 10px;"><img src="'._PS_ADMIN_IMG_.'warn2.png"> '.$warn.'</div>';
					} else
						foreach ($this->_postWarnings as $warn)
							$output .= '<div class="warn clear" style="margin-bottom: 10px;">'.$warn.'</div>';
				}
			}
			return $output;
		}
		
		public function _displayForm($token, $big = true, $space = false) {
			if (!defined('_PS_ADMIN_IMG_')) // PS 1.0
				define('_PS_ADMIN_IMG_',   _PS_IMG_.'admin/');
			
			if ($big) {
				$output = $this->getRegisterForm($token, $space);
				$output .= $this->getAboutForm($big, $space);
				$output .= '<br style="clear: both"/>';
			} else {
				$output = $this->getAboutForm($big, $space);
				$output .= $this->getRegisterForm($token, true);
			}
			if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
				$output .= '
				<style type="text/css">
				#content .warn {
					border: 1px solid #D3C200;
					background-color: #FFFAC6;
					font-family: Arial,Verdana,Helvetica,sans-serif;
				}
				#content .conf, #content .warn, #content .error {
					color: #383838;
					font-weight: 700;
					margin: 0 0 10px 0;
					line-height: 20px;
					padding: 10px 15px;
				}
				</style>
				';
			return $output;
		}
		
		public function getAboutForm($big = false, $space = false) {
			if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) { // for translation with Prestashop 1.x
				$tmp_page = $this->page;
				$this->page = 'modulesamdha';
			}

			$output = '
				<fieldset '.($big?'class="width3"':(($space?'class="space" ':'').'style="float: right; width: 255px"')).' id="samdha_aboutform">
					<legend>'.$this->l('About', 'modulesamdha').'</legend>
					<p style="font-size: 1.5em; font-weight: bold; padding-bottom: 0"><img src="'.$this->_path.'logo.png" alt="'.$this->displayName.'" style="float: left; padding-right: 1em"/>'.$this->displayName.'</p>
					<p style="clear: left;">
					'.$this->l('Thanks for installing this module on your website.', 'modulesamdha').'
					</p>';
			if ($this->descriptionBig)
				$output .= $this->descriptionBig;
			else
				$output .= '<p>'.$this->description.'</p>';
			$output .= '
					<p>
					'.$this->l('Developped with love by', 'modulesamdha').' <a style="color: #7ba45b; text-decoration: underline;" href="http://www.samdha.net/?utm_source=module&amp;utm_medium=prestashop&amp;utm_content=homelink&amp;utm_campaign='.urlencode($this->name).'">Samdha</a>'.$this->l(', which helps you develop your e-commerce site.', 'modulesamdha').'
					</p>
					<p>
					<span style="float: right; opacity: .2; font-size: 9px; padding-top: 5px;">v'.$this->version.'</span>
					<a href="http://www.samdha.net/?utm_source=module&amp;utm_medium=prestashop&amp;utm_content=contactlink&amp;utm_campaign='.urlencode($this->name).'#contactez-nous"><img src="../img/admin/email.gif" alt="" /> '.$this->l('Contact', 'modulesamdha').'</a>
					</p>
				</fieldset>
			';

			if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) // for translation with Prestashop 1.x
				$this->page = $tmp_page;
				
			return $output;
		}
	
		/* Config management */
		
		// set default config
		public function getDefaultConfig() {
			return array();
		}
		
		/**
		 * get module config
		 * and put it in $this->config
		 **/
		public function getConfig() {
			// get real config
			$this->config = $this->getDefaultConfig();
			if (method_exists('Configuration', 'hasKey')) {
				foreach (array_keys($this->config) as $key)
					if (Configuration::hasKey($key))
						$this->config[$key] = Configuration::get($key);
			} else
				$this->config = array_merge($this->config, Configuration::getMultiple(array_keys($this->config)));
	
			foreach ($this->configArrays as $key)
				if ($this->config[$this->shortName.$key] !== '') {
					$this->config[$this->shortName.$key] = explode(',', $this->config[$this->shortName.$key]);
					foreach ($this->config[$this->shortName.$key] as $id => $value) {
						$this->config[$this->shortName.$key][$id] = str_replace(self::COMA_REPLACE, ',', $value);
					}
				}
				else
					$this->config[$this->shortName.$key] = array();
		}
	
		/**
		 * save module config from $_POST
		 * then put it in $this->config
		 **/
		public function saveConfig() {
			foreach ($this->configArrays as $key)
				if (Tools::getValue($this->shortName.$key))
					$_POST[$this->shortName.$key] = implode(',', str_replace(',', self::COMA_REPLACE, Tools::getValue($this->shortName.$key)));
				else
					$_POST[$this->shortName.$key] = '';
	
			foreach ($_POST as $key => $value)
				if (strpos($key, $this->shortName) === 0) {
					$result = true;
					Configuration::updateValue($key, Tools::getValue($key), true);
				}
	
			$this->getConfig();
			$this->postsaveConfig();
		}
		
		public function postSaveConfig() {
		}
		
		/**
		 * remove module config from database
		 **/
		public function deleteConfig() {
			$this->getConfig();
			foreach ($this->config as $key=>$value)
				Configuration::deleteByName($key);
			$this->getConfig();
		}
		
		/** Licence management **/
		public function checkLicence() {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$context = Context::getContext();
				$cookie = $context->cookie;
			} else
				global $cookie;

			static $done = false;
			if ((@ini_get('allow_url_fopen')
				 || $this->cURLcheckBasicFunctions()) 
				&& !$done) {
				$done = true;
				$this->licence_number = Configuration::get($this->shortName.'_licence');
				if ($this->licence_number) {
					$domain = $this->getHttpHost();
					$iso_lang = Language::getIsoById($cookie->id_lang);
					try {
						$valid_licence = @$this->jsonRPCCall(self::SUPPORT_URL.'jsonrpc.php',
															 'support~licence:check',
															 array('module_name'=>$this->name,
																   'lang' => $iso_lang,
																   'domain' => $domain,
																   'licence_number' => $this->licence_number));
						if ($valid_licence!='OK')
						   $this->licence_number = false;
					} catch (Exception $e) {
						$done = false;
					}
				}
			}
			return $done && ((bool) $this->licence_number !== false);
		}
		
		public function saveLicence() {
			Configuration::updateValue($this->shortName.'_licence', Tools::getValue('licence_number'));
		}
		
		public function getRegisterForm($token, $space = true) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$context = Context::getContext();
				$cookie = $context->cookie;
				$currentIndex = AdminController::$currentIndex;
			} else
				global $currentIndex, $cookie;
			
			$output = '';
			if ((@ini_get('allow_url_fopen')
				 || $this->cURLcheckBasicFunctions()) 
				&& $this->need_licence_number)
				if (!$this->checkLicence()) {
					$domain = $this->getHttpHost();
					$iso_lang = Language::getIsoById($cookie->id_lang);
					$employee = new Employee($cookie->id_employee);
					if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) { // for translation with Prestashop 1.x
						$tmp_page = $this->page;
						$this->page = 'modulesamdha';
					}

					$output = '
						<fieldset '.($space?'class="space"':'').' style="float: right; width: 255px; clear: right" id="samdha_registerform">
							<legend>'.$this->l('Register this module', 'modulesamdha').'</legend>
							<p>
							'.$this->l('By register your module you will get:', 'modulesamdha').'
							</p>
							<ul>
								<li>'.$this->l('Faster and better support,', 'modulesamdha').'</li>
								<li>'.$this->l('Latest version before everyone,', 'modulesamdha').'</li>
								<li>'.$this->l('Automatic update of the module.', 'modulesamdha').'</li>
							</ul>
							<p>
							'.$this->l('Just fill', 'modulesamdha').' <a style="text-decoration: underline;" href="'.self::SUPPORT_URL.'index.php/support/licence/?email='.urlencode($employee->email).'&amp;lang='.$iso_lang.'&amp;module_name='.urlencode($this->name).'&amp;domain='.urlencode($domain).'" target="_blank">'.$this->l('this form', 'modulesamdha').'</a>'.$this->l(', it\'s free.', 'modulesamdha').'
							</p>
							<hr/>
							<form action="'.$currentIndex.'&amp;configure='.$this->name.'&amp;token='.$token.'" method="post" enctype="multipart/form-data">
							<p>
							<label for="licence_number" class="t">'.$this->l('Licence number:', 'modulesamdha').'</label><br/>
							<input style="width: 200px" type="text" name="licence_number" id="licence_number" value="'.$this->licence_number.'"/>
							<input type="submit" name="saveLicence" class="button" value="'.$this->l('Ok', 'modulesamdha').'"/>
							</p>
							</form>
						</fieldset>';
					if (version_compare(_PS_VERSION_, '1.2.0.0', '<')) // for translation with Prestashop 1.x
						$this->page = $tmp_page;
						
				} else {
					$domain = $this->getHttpHost();
					$iso_lang = Language::getIsoById($cookie->id_lang);
					try {
						$text = @$this->jsonRPCCall(self::SUPPORT_URL.'jsonrpc.php',
															 'support~module:supportBox',
															 array('module_name'=>$this->name,
																   'domain' => $domain,
																   'licence_number' => $this->licence_number,
																   'lang' => $iso_lang));
					} catch (Exception $e) {
						$text = '';
					}
					if ($text)
						$output = '
							<fieldset '.($space?'class="space"':'').' style="float: right; width: 255px; clear: right">
							'.$text.'
							</fieldset>';
				}
			return $output;
		}
		
		/** Module management **/
		
		public function checkModuleVersion() {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$context = Context::getContext();
				$cookie = $context->cookie;
			} else
				global $cookie;
			
			if ((@ini_get('allow_url_fopen')
				 || $this->cURLcheckBasicFunctions()) 
				&& $this->licence_number) {
				$domain = $this->getHttpHost();
				$iso_lang = Language::getIsoById($cookie->id_lang);
				try {
					return @$this->jsonRPCCall(self::SUPPORT_URL.'jsonrpc.php',
														 'support~module:check',
														 array('module_name'=>$this->name,
															   'lang' => $iso_lang,
															   'domain' => $domain,
															   'version' => $this->version,
															   'licence_number' => $this->licence_number));
				} catch (Exception $e) {
				}
			}
			return false;
		}
		
		public function updateModule()
		{
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$context = Context::getContext();
				$cookie = $context->cookie;
			} else
				global $cookie;
			
			$result = false;
			if ($this->checkLicence()) {
				$domain = $this->getHttpHost();
				$iso_lang = Language::getIsoById($cookie->id_lang);
				$params = array('module_name'=>$this->name,
							'lang' => $iso_lang,
							'domain' => $domain,
							'version' => $this->version,
							'licence_number' => $this->licence_number);
				
				if (!defined('_PS_MODULE_DIR_')) // PS 1.0
					define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.'/modules/');
				
				$file = _PS_MODULE_DIR_.$this->name.'.zip';
				if (file_exists($file))
					@unlink($file);
				// copy file
				if (function_exists('curl_init')) {
					// create a new CURL resource 
					$ch = curl_init(); 
					$outfile = fopen($file, 'wb'); 
					
					// set URL and other appropriate options
					$curlopt = array(
						CURLOPT_URL => self::SUPPORT_URL.'index.php/support/module/getLastVersion/?'.http_build_query($params, '', '&'),
						CURLOPT_HEADER => false,
						CURLOPT_RETURNTRANSFER => false,
						CURLOPT_FOLLOWLOCATION => false,
						CURLOPT_USERAGENT => 'Module '.$this->name.' v'.$this->version.' for Prestashop v'._PS_VERSION_,
						CURLOPT_FILE => $outfile
					);
					@curl_setopt_array($ch, $curlopt);
					
					// grab file from URL 
					$result = curl_exec($ch); 
					
					// close CURL resource, and free up system resources 
					fclose($outfile); 
					curl_close($ch); 					
				} else
					$result = @$this->copy_url(self::SUPPORT_URL.'index.php/support/module/getLastVersion/?'.http_build_query($params, '', '&'),
									$file);
				if ($result) {
					if (file_exists($file) && filesize($file)) {
						if (method_exists('Tools', 'ZipExtract')) {
							if (Tools::ZipExtract($file, _PS_MODULE_DIR_))
								$result = true;
							else
								$this->_postErrors[] =Tools::displayError('error while extracting module (file may be corrupted).');
						}
						elseif (class_exists('ZipArchive', false))
						{
							$zip = new ZipArchive();
							if ($zip->open($file) === true AND $zip->extractTo(_PS_MODULE_DIR_) AND $zip->close())
								$result = true;
							else {
								$error = error_get_last();
								$this->_postErrors[] = Tools::displayError('error while extracting module (file may be corrupted)').($error?' '.$error['message']:'');
							}
						}
						else
							$this->_postErrors[] = Tools::displayError('zip is not installed on your server. Ask your host for further information.');
					}
					else {
						$error = error_get_last();
						$this->_postErrors[] = $this->l('Error while downloading module.', 'modulesamdha').($error?' '.$error['message']:'');
					}
				}
				else {
					$error = error_get_last();
					$this->_postErrors[] = $this->l('Error while downloading module.', 'modulesamdha').($error?' '.$error['message']:'');
				}
				if (file_exists($file))
					@unlink($file);
			}
			return $result;
		}
		
		public function afterUpdateModule()
		{
			// clean cache, update BDD...
		}
		
		/** usefull functions **/
	
		/**
		 * execute sql file
		 * @return boolean
		 **/
		public function executeSQLFile($file) {
			$path = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR; // @since 1.3.3.0
			if (!file_exists($path.$file)) {
				$path = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR;
			}
			if (!file_exists($path.$file)) {
				$this->_postErrors[] = 'File not found : '.$path.$file;
				$this->_errors[] = 'File not found : '.$path.$file;
				return false;
			}
			if (!($sql = file_get_contents($path.$file))) {
				$this->_postErrors[] = 'File empty : '.$path.$file;
				$this->_errors[] = 'File empty : '.$path.$file;
				return false;
			}
			$sql = preg_split("/;\s*[\r\n]+/", str_replace('PREFIX_', _DB_PREFIX_, $sql));
			$db = Db::getInstance();
			foreach ($sql AS $query) {
				$query = trim($query);
				if($query) {
					if(!$db->Execute($query)) {
						$this->_postErrors[] = $db->getMsgError().' '.$query;
						$this->_errors[] = $db->getMsgError().' '.$query;
						return false;
					}
				}
			}
			return true;
		}
		
		/**
		 * idem than Module::l but with $id_lang
		 * @since 1.1.0.0
		 **/
		public function l2($string, $id_lang, $specific = false)
		{
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				$context = Context::getContext();
				$cookie = $context->cookie;
				$file = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'translations'.DIRECTORY_SEPARATOR.Language::getIsoById($id_lang).'.php';
				if (!file_exists($file))
					$file = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.Language::getIsoById($id_lang).'.php';
			} else {
				global $cookie;				
				$file = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.Language::getIsoById($id_lang).'.php';
			}
			global $_MODULE, $_MODULES;
				
			$_MODULEStmp = $_MODULES;
			$_MODULES = array();
			
			if (method_exists('Tools', 'file_exists_cache'))
				if (Tools::file_exists_cache($file) AND include_once($file))
					$_MODULES = $_MODULE;
			else 
				if (file_exists($file) AND include_once($file))
					$_MODULES = $_MODULE;
			
			$string2 = str_replace('\'', '\\\'', $string);
			if (method_exists('Module', 'findTranslation')) {
				$source = $specific ? $specific : $this->name;
				if (property_exists($this, 'l_cache')) //@since 1.3.1.0
					unset(self::$l_cache[$this->name . '|' . $string2 . '|' . $source]);
				$ret = $this->findTranslation($this->name, $string2, $source);
			} else { // PS 1.3
				$source = Tools::strtolower($specific ? $specific : get_class($this));
				$currentKey = '<{'.$this->name.'}'._THEME_NAME_.'>'.$source.'_'.md5($string2);
				$defaultKey = '<{'.$this->name.'}prestashop>'.$source.'_'.md5($string2);
	
				if (key_exists($currentKey, $_MODULES))
					$ret = stripslashes($_MODULES[$currentKey]);
				elseif (key_exists($defaultKey, $_MODULES))
					$ret = stripslashes($_MODULES[$defaultKey]);
				else
					$ret = $string;
				$ret = str_replace('"', '&quot;', $ret);
			}
			$_MODULES = $_MODULEStmp;
			return $ret;
		}
		
		/**
		 * get current http host
		 * for Prestashop < 1.3
		 *
		 * @param boolean $http @see Tools::getHttpHost
		 * @param boolean $entities @see Tools::getHttpHost
		 * @return string
		 **/
		public function getHttpHost($http = false, $entities = false)
		{
			if (method_exists('Tools', 'getHttpHost'))
				$host = Tools::getHttpHost($http, $entities);
			else {
				$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
				if ($entities)
					$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
				if ($http)
					$host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
			}
			return $host;
		}
		
        /**
         * Performs a jsonRCP request and gets the results as an array
         *
         * @param string $method
         * @param array $params
         * @return array
         */
        public function jsonRPCCall($url, $method,$params) {
            // prepares the request
			$randId = 1 + rand();
            $request = json_encode(array(
										 'method' => $method,
										 'params' => $params,
										 'id' => $randId));
            
            // performs the HTTP POST
            // performs the HTTP POST
            if ($this->cURLcheckBasicFunctions()) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HEADER, 'content-type: text/plain;');
				curl_setopt($ch, CURLOPT_TRANSFERTEXT, 0);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_PROXY, false);

				// decode result
				$response = @curl_exec($ch);
				curl_close($ch);

				$response = json_decode($response);

            } else {
	            $opts = array ('http' => array (
	                                'method'  => 'POST',
	                                'header'  => 'Content-type: application/json',
	                                'content' => $request
	                                ));
	            $context  = stream_context_create($opts);
	            if ($fp = fopen($url, 'r', false, $context)) {
	                $response = '';
	                while($row = fgets($fp)) {
	                    $response.= trim($row)."\n";
	                }
	                $response = json_decode($response);
	            } else {
	                throw new Exception('Unable to connect to '.$url);
	            }
	        }

            // final checks and return
			// check
			if ($response->id != $randId) {
				throw new Exception('Incorrect response id (request id: '.$randId.', response id: '.$response->id.')');
			}
			if (!is_null($response->error)) {
				throw new Exception('Request error: '.$response->error);
			}
			
			return $response->result;
        }

        // curl management
		function cURLcheckBasicFunctions() 
		{ 
			return ( 	function_exists("curl_init") && 
						function_exists("curl_setopt") && 
						function_exists("curl_exec") && 
						function_exists("curl_close") ); 
		} 

		/* 
		 * Returns string status information. 
		 * Can be changed to int or bool return types. 
		 */ 
		function copy_url($url, $file) 
		{ 
			if( $this->cURLcheckBasicFunctions() ) {
				$ch = curl_init(); 
				if($ch) { 
					$fp = fopen($file, "w"); 
					if($fp) { 
						if( !curl_setopt($ch, CURLOPT_URL, $url) ) { 
							fclose($fp); // to match fopen() 
							curl_close($ch); // to match curl_init() 
							throw new Exception('FAIL: curl_setopt(CURLOPT_URL)');
						} 
						if( !curl_setopt($ch, CURLOPT_FILE, $fp) ) throw new Exception('FAIL: curl_setopt(CURLOPT_FILE)'); 
						if( !curl_setopt($ch, CURLOPT_HEADER, 0) ) throw new Exception('FAIL: curl_setopt(CURLOPT_HEADER)'); 
						if( !curl_exec($ch) ) throw new Exception('FAIL: curl_exec()'); 
						curl_close($ch); 
						fclose($fp); 
						return true; 
					} 
					throw new Exception('FAIL: fopen()'); 
				} 
				else throw new Exception('FAIL: curl_init()'); 
			} else if (!copy($url, $file)) {;
				throw new Exception('FAIL: copy()'); 
			} else
				return true;
		} 
	}
}


// from wordpress
// For PHP < 5.2.0
if ( !function_exists('json_encode') || !function_exists('json_decode') ) {
if ( !class_exists( 'Services_JSON' , false) ) :
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Converts to and from JSON format.
 *
 * JSON (JavaScript Object Notation) is a lightweight data-interchange
 * format. It is easy for humans to read and write. It is easy for machines
 * to parse and generate. It is based on a subset of the JavaScript
 * Programming Language, Standard ECMA-262 3rd Edition - December 1999.
 * This feature can also be found in  Python. JSON is a text format that is
 * completely language independent but uses conventions that are familiar
 * to programmers of the C-family of languages, including C, C++, C#, Java,
 * JavaScript, Perl, TCL, and many others. These properties make JSON an
 * ideal data-interchange language.
 *
 * This package provides a simple encoder and decoder for JSON notation. It
 * is intended for use with client-side Javascript applications that make
 * use of HTTPRequest to perform server communication functions - data can
 * be encoded into JSON notation for use in a client-side javascript, or
 * decoded from incoming Javascript requests. JSON format is native to
 * Javascript, and can be directly eval()'ed with no further parsing
 * overhead
 *
 * All strings should be in ASCII or UTF-8 format!
 *
 * LICENSE: Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met: Redistributions of source code must retain the
 * above copyright notice, this list of conditions and the following
 * disclaimer. Redistributions in binary form must reproduce the above
 * copyright notice, this list of conditions and the following disclaimer
 * in the documentation and/or other materials provided with the
 * distribution.
 *
 * THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN
 * NO EVENT SHALL CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
 * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
 * TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @category
 * @package		Services_JSON
 * @author		Michal Migurski <mike-json@teczno.com>
 * @author		Matt Knapp <mdknapp[at]gmail[dot]com>
 * @author		Brett Stimmerman <brettstimmerman[at]gmail[dot]com>
 * @copyright	2005 Michal Migurski
 * @version     CVS: $Id: JSON.php 288200 2009-09-09 15:41:29Z alan_k $
 * @license		http://www.opensource.org/licenses/bsd-license.php
 * @link		http://pear.php.net/pepr/pepr-proposal-show.php?id=198
 */

/**
 * Marker constant for Services_JSON::decode(), used to flag stack state
 */
define('SERVICES_JSON_SLICE', 1);

/**
 * Marker constant for Services_JSON::decode(), used to flag stack state
 */
define('SERVICES_JSON_IN_STR',  2);

/**
 * Marker constant for Services_JSON::decode(), used to flag stack state
 */
define('SERVICES_JSON_IN_ARR',  3);

/**
 * Marker constant for Services_JSON::decode(), used to flag stack state
 */
define('SERVICES_JSON_IN_OBJ',  4);

/**
 * Marker constant for Services_JSON::decode(), used to flag stack state
 */
define('SERVICES_JSON_IN_CMT', 5);

/**
 * Behavior switch for Services_JSON::decode()
 */
define('SERVICES_JSON_LOOSE_TYPE', 16);

/**
 * Behavior switch for Services_JSON::decode()
 */
define('SERVICES_JSON_SUPPRESS_ERRORS', 32);

/**
 * Converts to and from JSON format.
 *
 * Brief example of use:
 *
 * <code>
 * // create a new instance of Services_JSON
 * $json = new Services_JSON();
 *
 * // convert a complexe value to JSON notation, and send it to the browser
 * $value = array('foo', 'bar', array(1, 2, 'baz'), array(3, array(4)));
 * $output = $json->encode($value);
 *
 * print($output);
 * // prints: ["foo","bar",[1,2,"baz"],[3,[4]]]
 *
 * // accept incoming POST data, assumed to be in JSON notation
 * $input = file_get_contents('php://input', 1000000);
 * $value = $json->decode($input);
 * </code>
 */
class Services_JSON
{
 /**
	* constructs a new JSON instance
	*
	* @param int $use object behavior flags; combine with boolean-OR
	*
	*						possible values:
	*						- SERVICES_JSON_LOOSE_TYPE:  loose typing.
	*								"{...}" syntax creates associative arrays
	*								instead of objects in decode().
	*						- SERVICES_JSON_SUPPRESS_ERRORS:  error suppression.
	*								Values which can't be encoded (e.g. resources)
	*								appear as NULL instead of throwing errors.
	*								By default, a deeply-nested resource will
	*								bubble up with an error, so all return values
	*								from encode() should be checked with isError()
	*/
	function Services_JSON($use = 0)
	{
		$this->use = $use;
	}

 /**
	* convert a string from one UTF-16 char to one UTF-8 char
	*
	* Normally should be handled by mb_convert_encoding, but
	* provides a slower PHP-only method for installations
	* that lack the multibye string extension.
	*
	* @param	string  $utf16  UTF-16 character
	* @return string  UTF-8 character
	* @access private
	*/
	function utf162utf8($utf16)
	{
		// oh please oh please oh please oh please oh please
		if(function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
		}

		$bytes = (ord($utf16{0}) << 8) | ord($utf16{1});

		switch(true) {
			case ((0x7F & $bytes) == $bytes):
				// this case should never be reached, because we are in ASCII range
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr(0x7F & $bytes);

			case (0x07FF & $bytes) == $bytes:
				// return a 2-byte UTF-8 character
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr(0xC0 | (($bytes >> 6) & 0x1F))
					. chr(0x80 | ($bytes & 0x3F));

			case (0xFFFF & $bytes) == $bytes:
				// return a 3-byte UTF-8 character
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr(0xE0 | (($bytes >> 12) & 0x0F))
					. chr(0x80 | (($bytes >> 6) & 0x3F))
					. chr(0x80 | ($bytes & 0x3F));
		}

		// ignoring UTF-32 for now, sorry
		return '';
	}

 /**
	* convert a string from one UTF-8 char to one UTF-16 char
	*
	* Normally should be handled by mb_convert_encoding, but
	* provides a slower PHP-only method for installations
	* that lack the multibye string extension.
	*
	* @param	string  $utf8 UTF-8 character
	* @return string  UTF-16 character
	* @access private
	*/
	function utf82utf16($utf8)
	{
		// oh please oh please oh please oh please oh please
		if(function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
		}

		switch(strlen($utf8)) {
			case 1:
				// this case should never be reached, because we are in ASCII range
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return $utf8;

			case 2:
				// return a UTF-16 character from a 2-byte UTF-8 char
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr(0x07 & (ord($utf8{0}) >> 2))
					. chr((0xC0 & (ord($utf8{0}) << 6))
						| (0x3F & ord($utf8{1})));

			case 3:
				// return a UTF-16 character from a 3-byte UTF-8 char
				// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				return chr((0xF0 & (ord($utf8{0}) << 4))
						| (0x0F & (ord($utf8{1}) >> 2)))
					. chr((0xC0 & (ord($utf8{1}) << 6))
						| (0x7F & ord($utf8{2})));
		}

		// ignoring UTF-32 for now, sorry
		return '';
	}

 /**
	* encodes an arbitrary variable into JSON format (and sends JSON Header)
	*
	* @param	mixed $var	any number, boolean, string, array, or object to be encoded.
	*						see argument 1 to Services_JSON() above for array-parsing behavior.
	*						if var is a strng, note that encode() always expects it
	*						to be in ASCII or UTF-8 format!
	*
	* @return mixed JSON string representation of input var or an error if a problem occurs
	* @access public
	*/
	function encode($var)
	{
		header('Content-type: application/json');
		return $this->_encode($var);
	}
	/**
	* encodes an arbitrary variable into JSON format without JSON Header - warning - may allow CSS!!!!)
	*
	* @param	mixed $var	any number, boolean, string, array, or object to be encoded.
	*						see argument 1 to Services_JSON() above for array-parsing behavior.
	*						if var is a strng, note that encode() always expects it
	*						to be in ASCII or UTF-8 format!
	*
	* @return mixed JSON string representation of input var or an error if a problem occurs
	* @access public
	*/
	function encodeUnsafe($var)
	{
		return $this->_encode($var);
	}
	/**
	* PRIVATE CODE that does the work of encodes an arbitrary variable into JSON format
	*
	* @param	mixed $var	any number, boolean, string, array, or object to be encoded.
	*						see argument 1 to Services_JSON() above for array-parsing behavior.
	*						if var is a strng, note that encode() always expects it
	*						to be in ASCII or UTF-8 format!
	*
	* @return mixed JSON string representation of input var or an error if a problem occurs
	* @access public
	*/
	function _encode($var)
	{

		switch (gettype($var)) {
			case 'boolean':
				return $var ? 'true' : 'false';

			case 'NULL':
				return 'null';

			case 'integer':
				return (int) $var;

			case 'double':
			case 'float':
				return (float) $var;

			case 'string':
				// STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
				$ascii = '';
				$strlen_var = strlen($var);

			/*
				* Iterate over every character in the string,
				* escaping with a slash or encoding to UTF-8 where necessary
				*/
				for ($c = 0; $c < $strlen_var; ++$c) {

					$ord_var_c = ord($var{$c});

					switch (true) {
						case $ord_var_c == 0x08:
							$ascii .= '\b';
							break;
						case $ord_var_c == 0x09:
							$ascii .= '\t';
							break;
						case $ord_var_c == 0x0A:
							$ascii .= '\n';
							break;
						case $ord_var_c == 0x0C:
							$ascii .= '\f';
							break;
						case $ord_var_c == 0x0D:
							$ascii .= '\r';
							break;

						case $ord_var_c == 0x22:
						case $ord_var_c == 0x2F:
						case $ord_var_c == 0x5C:
							// double quote, slash, slosh
							$ascii .= '\\'.$var{$c};
							break;

						case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
							// characters U-00000000 - U-0000007F (same as ASCII)
							$ascii .= $var{$c};
							break;

						case (($ord_var_c & 0xE0) == 0xC0):
							// characters U-00000080 - U-000007FF, mask 110XXXXX
							// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
							if ($c+1 >= $strlen_var) {
								$c += 1;
								$ascii .= '?';
								break;
							}

							$char = pack('C*', $ord_var_c, ord($var{$c + 1}));
							$c += 1;
							$utf16 = $this->utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;

						case (($ord_var_c & 0xF0) == 0xE0):
							if ($c+2 >= $strlen_var) {
								$c += 2;
								$ascii .= '?';
								break;
							}
							// characters U-00000800 - U-0000FFFF, mask 1110XXXX
							// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
							$char = pack('C*', $ord_var_c,
										@ord($var{$c + 1}),
										@ord($var{$c + 2}));
							$c += 2;
							$utf16 = $this->utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;

						case (($ord_var_c & 0xF8) == 0xF0):
							if ($c+3 >= $strlen_var) {
								$c += 3;
								$ascii .= '?';
								break;
							}
							// characters U-00010000 - U-001FFFFF, mask 11110XXX
							// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}),
										ord($var{$c + 2}),
										ord($var{$c + 3}));
							$c += 3;
							$utf16 = $this->utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;

						case (($ord_var_c & 0xFC) == 0xF8):
							// characters U-00200000 - U-03FFFFFF, mask 111110XX
							// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
							if ($c+4 >= $strlen_var) {
								$c += 4;
								$ascii .= '?';
								break;
							}
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}),
										ord($var{$c + 2}),
										ord($var{$c + 3}),
										ord($var{$c + 4}));
							$c += 4;
							$utf16 = $this->utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;

						case (($ord_var_c & 0xFE) == 0xFC):
						if ($c+5 >= $strlen_var) {
								$c += 5;
								$ascii .= '?';
								break;
							}
							// characters U-04000000 - U-7FFFFFFF, mask 1111110X
							// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}),
										ord($var{$c + 2}),
										ord($var{$c + 3}),
										ord($var{$c + 4}),
										ord($var{$c + 5}));
							$c += 5;
							$utf16 = $this->utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;
					}
				}
				return  '"'.$ascii.'"';

			case 'array':
			/*
				* As per JSON spec if any array key is not an integer
				* we must treat the the whole array as an object. We
				* also try to catch a sparsely populated associative
				* array with numeric keys here because some JS engines
				* will create an array with empty indexes up to
				* max_index which can cause memory issues and because
				* the keys, which may be relevant, will be remapped
				* otherwise.
				*
				* As per the ECMA and JSON specification an object may
				* have any string as a property. Unfortunately due to
				* a hole in the ECMA specification if the key is a
				* ECMA reserved word or starts with a digit the
				* parameter is only accessible using ECMAScript's
				* bracket notation.
				*/

				// treat as a JSON object
				if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
					$properties = array_map(array($this, 'name_value'),
											array_keys($var),
											array_values($var));

					foreach($properties as $property) {
						if(Services_JSON::isError($property)) {
							return $property;
						}
					}

					return '{' . join(',', $properties) . '}';
				}

				// treat it like a regular array
				$elements = array_map(array($this, '_encode'), $var);

				foreach($elements as $element) {
					if(Services_JSON::isError($element)) {
						return $element;
					}
				}

				return '[' . join(',', $elements) . ']';

			case 'object':
				$vars = get_object_vars($var);

				$properties = array_map(array($this, 'name_value'),
										array_keys($vars),
										array_values($vars));

				foreach($properties as $property) {
					if(Services_JSON::isError($property)) {
						return $property;
					}
				}

				return '{' . join(',', $properties) . '}';

			default:
				return ($this->use & SERVICES_JSON_SUPPRESS_ERRORS)
					? 'null'
					: new Services_JSON_Error(gettype($var)." can not be encoded as JSON string");
		}
	}

 /**
	* array-walking function for use in generating JSON-formatted name-value pairs
	*
	* @param	string  $name name of key to use
	* @param	mixed $value  reference to an array element to be encoded
	*
	* @return string  JSON-formatted name-value pair, like '"name":value'
	* @access private
	*/
	function name_value($name, $value)
	{
		$encoded_value = $this->_encode($value);

		if(Services_JSON::isError($encoded_value)) {
			return $encoded_value;
		}

		return $this->_encode(strval($name)) . ':' . $encoded_value;
	}

 /**
	* reduce a string by removing leading and trailing comments and whitespace
	*
	* @param	$str	string	string value to strip of comments and whitespace
	*
	* @return string  string value stripped of comments and whitespace
	* @access private
	*/
	function reduce_string($str)
	{
		$str = preg_replace(array(

				// eliminate single line comments in '// ...' form
				'#^\s*//(.+)$#m',

				// eliminate multi-line comments in '/* ... */' form, at start of string
				'#^\s*/\*(.+)\*/#Us',

				// eliminate multi-line comments in '/* ... */' form, at end of string
				'#/\*(.+)\*/\s*$#Us'

			), '', $str);

		// eliminate extraneous space
		return trim($str);
	}

 /**
	* decodes a JSON string into appropriate variable
	*
	* @param	string  $str	JSON-formatted string
	*
	* @return mixed number, boolean, string, array, or object
	*				corresponding to given JSON input string.
	*				See argument 1 to Services_JSON() above for object-output behavior.
	*				Note that decode() always returns strings
	*				in ASCII or UTF-8 format!
	* @access public
	*/
	function decode($str)
	{
		$str = $this->reduce_string($str);

		switch (strtolower($str)) {
			case 'true':
				return true;

			case 'false':
				return false;

			case 'null':
				return null;

			default:
				$m = array();

				if (is_numeric($str)) {
					// Lookie-loo, it's a number

					// This would work on its own, but I'm trying to be
					// good about returning integers where appropriate:
					// return (float)$str;

					// Return float or int, as appropriate
					return ((float)$str == (integer)$str)
						? (integer)$str
						: (float)$str;

				} elseif (preg_match('/^("|\').*(\1)$/s', $str, $m) && $m[1] == $m[2]) {
					// STRINGS RETURNED IN UTF-8 FORMAT
					$delim = substr($str, 0, 1);
					$chrs = substr($str, 1, -1);
					$utf8 = '';
					$strlen_chrs = strlen($chrs);

					for ($c = 0; $c < $strlen_chrs; ++$c) {

						$substr_chrs_c_2 = substr($chrs, $c, 2);
						$ord_chrs_c = ord($chrs{$c});

						switch (true) {
							case $substr_chrs_c_2 == '\b':
								$utf8 .= chr(0x08);
								++$c;
								break;
							case $substr_chrs_c_2 == '\t':
								$utf8 .= chr(0x09);
								++$c;
								break;
							case $substr_chrs_c_2 == '\n':
								$utf8 .= chr(0x0A);
								++$c;
								break;
							case $substr_chrs_c_2 == '\f':
								$utf8 .= chr(0x0C);
								++$c;
								break;
							case $substr_chrs_c_2 == '\r':
								$utf8 .= chr(0x0D);
								++$c;
								break;

							case $substr_chrs_c_2 == '\\"':
							case $substr_chrs_c_2 == '\\\'':
							case $substr_chrs_c_2 == '\\\\':
							case $substr_chrs_c_2 == '\\/':
								if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
								($delim == "'" && $substr_chrs_c_2 != '\\"')) {
									$utf8 .= $chrs{++$c};
								}
								break;

							case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
								// single, escaped unicode character
								$utf16 = chr(hexdec(substr($chrs, ($c + 2), 2)))
									. chr(hexdec(substr($chrs, ($c + 4), 2)));
								$utf8 .= $this->utf162utf8($utf16);
								$c += 5;
								break;

							case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
								$utf8 .= $chrs{$c};
								break;

							case ($ord_chrs_c & 0xE0) == 0xC0:
								// characters U-00000080 - U-000007FF, mask 110XXXXX
								//see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 2);
								++$c;
								break;

							case ($ord_chrs_c & 0xF0) == 0xE0:
								// characters U-00000800 - U-0000FFFF, mask 1110XXXX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 3);
								$c += 2;
								break;

							case ($ord_chrs_c & 0xF8) == 0xF0:
								// characters U-00010000 - U-001FFFFF, mask 11110XXX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 4);
								$c += 3;
								break;

							case ($ord_chrs_c & 0xFC) == 0xF8:
								// characters U-00200000 - U-03FFFFFF, mask 111110XX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 5);
								$c += 4;
								break;

							case ($ord_chrs_c & 0xFE) == 0xFC:
								// characters U-04000000 - U-7FFFFFFF, mask 1111110X
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 6);
								$c += 5;
								break;

						}

					}

					return $utf8;

				} elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {
					// array, or object notation

					if ($str{0} == '[') {
						$stk = array(SERVICES_JSON_IN_ARR);
						$arr = array();
					} else {
						if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
							$stk = array(SERVICES_JSON_IN_OBJ);
							$obj = array();
						} else {
							$stk = array(SERVICES_JSON_IN_OBJ);
							$obj = new stdClass();
						}
					}

					array_push($stk, array('what'  => SERVICES_JSON_SLICE,
										'where' => 0,
										'delim' => false));

					$chrs = substr($str, 1, -1);
					$chrs = $this->reduce_string($chrs);

					if ($chrs == '') {
						if (reset($stk) == SERVICES_JSON_IN_ARR) {
							return $arr;

						} else {
							return $obj;

						}
					}

					//print("\nparsing {$chrs}\n");

					$strlen_chrs = strlen($chrs);

					for ($c = 0; $c <= $strlen_chrs; ++$c) {

						$top = end($stk);
						$substr_chrs_c_2 = substr($chrs, $c, 2);

						if (($c == $strlen_chrs) || (($chrs{$c} == ',') && ($top['what'] == SERVICES_JSON_SLICE))) {
							// found a comma that is not inside a string, array, etc.,
							// OR we've reached the end of the character list
							$slice = substr($chrs, $top['where'], ($c - $top['where']));
							array_push($stk, array('what' => SERVICES_JSON_SLICE, 'where' => ($c + 1), 'delim' => false));
							//print("Found split at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

							if (reset($stk) == SERVICES_JSON_IN_ARR) {
								// we are in an array, so just push an element onto the stack
								array_push($arr, $this->decode($slice));

							} elseif (reset($stk) == SERVICES_JSON_IN_OBJ) {
								// we are in an object, so figure
								// out the property name and set an
								// element in an associative array,
								// for now
								$parts = array();

								if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									// "name":value pair
									$key = $this->decode($parts[1]);
									$val = $this->decode($parts[2]);

									if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
										$obj[$key] = $val;
									} else {
										$obj->$key = $val;
									}
								} elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									// name:value pair, where name is unquoted
									$key = $parts[1];
									$val = $this->decode($parts[2]);

									if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
										$obj[$key] = $val;
									} else {
										$obj->$key = $val;
									}
								}

							}

						} elseif ((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != SERVICES_JSON_IN_STR)) {
							// found a quote, and we are not inside a string
							array_push($stk, array('what' => SERVICES_JSON_IN_STR, 'where' => $c, 'delim' => $chrs{$c}));
							//print("Found start of string at {$c}\n");

						} elseif (($chrs{$c} == $top['delim']) &&
								($top['what'] == SERVICES_JSON_IN_STR) &&
								((strlen(substr($chrs, 0, $c)) - strlen(rtrim(substr($chrs, 0, $c), '\\'))) % 2 != 1)) {
							// found a quote, we're in a string, and it's not escaped
							// we know that it's not escaped becase there is _not_ an
							// odd number of backslashes at the end of the string so far
							array_pop($stk);
							//print("Found end of string at {$c}: ".substr($chrs, $top['where'], (1 + 1 + $c - $top['where']))."\n");

						} elseif (($chrs{$c} == '[') &&
								in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
							// found a left-bracket, and we are in an array, object, or slice
							array_push($stk, array('what' => SERVICES_JSON_IN_ARR, 'where' => $c, 'delim' => false));
							//print("Found start of array at {$c}\n");

						} elseif (($chrs{$c} == ']') && ($top['what'] == SERVICES_JSON_IN_ARR)) {
							// found a right-bracket, and we're in an array
							array_pop($stk);
							//print("Found end of array at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

						} elseif (($chrs{$c} == '{') &&
								in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
							// found a left-brace, and we are in an array, object, or slice
							array_push($stk, array('what' => SERVICES_JSON_IN_OBJ, 'where' => $c, 'delim' => false));
							//print("Found start of object at {$c}\n");

						} elseif (($chrs{$c} == '}') && ($top['what'] == SERVICES_JSON_IN_OBJ)) {
							// found a right-brace, and we're in an object
							array_pop($stk);
							//print("Found end of object at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

						} elseif (($substr_chrs_c_2 == '/*') &&
								in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
							// found a comment start, and we are in an array, object, or slice
							array_push($stk, array('what' => SERVICES_JSON_IN_CMT, 'where' => $c, 'delim' => false));
							$c++;
							//print("Found start of comment at {$c}\n");

						} elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == SERVICES_JSON_IN_CMT)) {
							// found a comment end, and we're in one now
							array_pop($stk);
							$c++;

							for ($i = $top['where']; $i <= $c; ++$i)
								$chrs = substr_replace($chrs, ' ', $i, 1);

							//print("Found end of comment at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

						}

					}

					if (reset($stk) == SERVICES_JSON_IN_ARR) {
						return $arr;

					} elseif (reset($stk) == SERVICES_JSON_IN_OBJ) {
						return $obj;

					}

				}
		}
	}

	/**
	* @todo Ultimately, this should just call PEAR::isError()
	*/
	function isError($data, $code = null)
	{
		if (class_exists('pear', false)) {
			return PEAR::isError($data, $code);
		} elseif (is_object($data) && (get_class($data) == 'services_json_error' ||
								is_subclass_of($data, 'services_json_error'))) {
			return true;
		}

		return false;
	}
}

if (class_exists('PEAR_Error', false)) {

	class Services_JSON_Error extends PEAR_Error
	{
		function Services_JSON_Error($message = 'unknown error', $code = null,
									$mode = null, $options = null, $userinfo = null)
		{
			parent::PEAR_Error($message, $code, $mode, $options, $userinfo);
		}
	}

} else {

	/**
	* @todo Ultimately, this class shall be descended from PEAR_Error
	*/
	class Services_JSON_Error
	{
		function Services_JSON_Error($message = 'unknown error', $code = null,
									$mode = null, $options = null, $userinfo = null)
		{

		}
	}

}
endif;

if ( !function_exists('json_encode') ) {
	function json_encode( $string ) {
		$wp_json = new Services_JSON();
		return $wp_json->encodeUnsafe( $string );
	}
}
if ( !function_exists('json_decode') ) {
	function json_decode( $string ) {
		$wp_json = new Services_JSON();
		return $wp_json->decode( $string );
	}
}
}

?>
