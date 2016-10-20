<?php
/**
 * Created by PhpStorm.
 * User: Боря
 * Date: 16.09.2016
 * Time: 16:32
 */
class BlockLayeredOverride extends BlockLayered
{
    private $products;

    private $page = 1;

    private $nbr_products;

    public function hookDisplayTopFilter($params)
    {
        return $this->generateFiltersBlock($this->getSelectedFilters(),true);
    }

    public function hookLeftColumn($params)
    {
        return $this->generateFiltersBlock($this->getSelectedFilters(),false);
    }

    public function ajaxCall()
    {
        global $smarty, $cookie;

        $selected_filters = $this->getSelectedFilters();
        $filter_block = $this->getFilterBlock($selected_filters);
        $this->getProducts($selected_filters, $products, $nb_products, $p, $n, $pages_nb, $start, $stop, $range);

        // Add pagination variable
        $nArray = (int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10 ? array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 50) : array(10, 20, 50);
        // Clean duplicate values
        $nArray = array_unique($nArray);
        asort($nArray);

        Hook::exec(
            'actionProductListModifier',
            array(
                'nb_products' => &$nb_products,
                'cat_products' => &$products,
            )
        );

        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true)
            $this->context->controller->addColorsToProductList($products);

        $category = new Category(Tools::getValue('id_category_layered', Configuration::get('PS_HOME_CATEGORY')), (int)$cookie->id_lang);

        // Generate meta title and meta description
        $category_title = (empty($category->meta_title) ? $category->name : $category->meta_title);
        $category_metas = Meta::getMetaTags((int)$cookie->id_lang, 'category');
        $title = '';
        $keywords = '';

        if (is_array($filter_block['title_values']))
            foreach ($filter_block['title_values'] as $key => $val)
            {
                $title .= ' > '.$key.' '.implode('/', $val);
                $keywords .= $key.' '.implode('/', $val).', ';
            }

        $title = $category_title.$title;

        if (!empty($title))
            $meta_title = $title;
        else
            $meta_title = $category_metas['meta_title'];

        $meta_description = $category_metas['meta_description'];

        $keywords = Tools::substr(Tools::strtolower($keywords), 0, 1000);
        if (!empty($keywords))
            $meta_keywords = rtrim($category_title.', '.$keywords.', '.$category_metas['meta_keywords'], ', ');

        $smarty->assign(
            array(
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
                'nb_products' => $nb_products,
                'category' => $category,
                'pages_nb' => (int)$pages_nb,
                'p' => (int)$p,
                'n' => (int)$n,
                'range' => (int)$range,
                'start' => (int)$start,
                'stop' => (int)$stop,
                'n_array' => ((int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10) ? array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 50) : array(10, 20, 50),
                'comparator_max_item' => (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')),
                'products' => $products,
                'products_per_page' => (int)Configuration::get('PS_PRODUCTS_PER_PAGE'),
                'static_token' => Tools::getToken(false),
                'page_name' => 'category',
                'nArray' => $nArray,
                'compareProducts' => CompareProduct::getCompareProducts((int)$this->context->cookie->id_compare)
            )
        );

        // Prevent bug with old template where category.tpl contain the title of the category and category-count.tpl do not exists
        if (file_exists(_PS_THEME_DIR_.'category-count.tpl'))
            $category_count = $smarty->fetch(_PS_THEME_DIR_.'category-count.tpl');
        else
            $category_count = '';

        if ($nb_products == 0)
            $product_list = $this->display(__FILE__, 'blocklayered-no-products.tpl');
        else
            $product_list = $smarty->fetch(_PS_THEME_DIR_.'product-list.tpl');

        $vars = array(
            'pages_nb' =>(int)$pages_nb,
            'filtersBlock' => utf8_encode($this->generateFiltersBlock($selected_filters)),
            'filtersBlockTop' => utf8_encode($this->generateFiltersBlock($selected_filters,true)),
            'productList' => utf8_encode($product_list),
            'pagination' => $smarty->fetch(_PS_THEME_DIR_.'pagination.tpl'),
            'categoryCount' => $category_count,
            'meta_title' => $meta_title.' - '.Configuration::get('PS_SHOP_NAME'),
            'heading' => $meta_title,
            'meta_keywords' => isset($meta_keywords) ? $meta_keywords : null,
            'meta_description' => $meta_description,
//            'current_friendly_url' => '#'.$filter_block['current_friendly_url'],
            'current_friendly_url' => ((int)$n == (int)$nb_products) ? '#/show-all': '#'.$filter_block['current_friendly_url'],
            'filters' => $filter_block['filters'],
            'nbRenderedProducts' => (int)$nb_products,
            'nbAskedProducts' => (int)$n
        );

        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true)
            $vars = array_merge($vars, array('pagination_bottom' => $smarty->assign('paginationId', 'bottom')
                ->fetch(_PS_THEME_DIR_.'pagination.tpl')));
        /* We are sending an array in jSon to the .js controller, it will update both the filters and the products zones */
        return Tools::jsonEncode($vars);
    }

    private function getSelectedFilters()
    {
        $home_category = Configuration::get('PS_HOME_CATEGORY');
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));
        if ($id_parent == $home_category)
            return;

        // Force attributes selection (by url '.../2-mycategory/color-blue' or by get parameter 'selected_filters')
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'blocklayered-ajax.php') === false || Tools::getValue('selected_filters') !== false)
        {
            if (Tools::getValue('selected_filters'))
                $url = Tools::getValue('selected_filters');
            else
                $url = preg_replace('/\/(?:\w*)\/(?:[0-9]+[-\w]*)([^\?]*)\??.*/', '$1', Tools::safeOutput($_SERVER['REQUEST_URI'], true));

            $url_attributes = explode('/', ltrim($url, '/'));
            $selected_filters = array('category' => array());
            if (!empty($url_attributes))
            {
                foreach ($url_attributes as $url_attribute)
                {
                    /* Pagination uses - as separator, can be different from $this->getAnchor()*/
                    if (strpos($url_attribute, 'page-') === 0)
                        $url_attribute = str_replace('-', $this->getAnchor(), $url_attribute);
                    $url_parameters = explode($this->getAnchor(), $url_attribute);
                    $attribute_name  = array_shift($url_parameters);
                    if ($attribute_name == 'page')
                        $this->page = (int)$url_parameters[0];
                    else if (in_array($attribute_name, array('price', 'weight','year','miles')))
                        $selected_filters[$attribute_name] = array($this->filterVar($url_parameters[0]), $this->filterVar($url_parameters[1]));
                    else
                    {
                        foreach ($url_parameters as $url_parameter)
                        {
                            $data = Db::getInstance()->getValue('SELECT data FROM `'._DB_PREFIX_.'layered_friendly_url` WHERE `url_key` = \''.md5('/'.$attribute_name.$this->getAnchor().$url_parameter).'\'');
                            if ($data)
                                foreach (Tools::unSerialize($data) as $key_params => $params)
                                {
                                    if (!isset($selected_filters[$key_params]))
                                        $selected_filters[$key_params] = array();
                                    foreach ($params as $key_param => $param)
                                    {
                                        if (!isset($selected_filters[$key_params][$key_param]))
                                            $selected_filters[$key_params][$key_param] = array();
                                        $selected_filters[$key_params][$key_param] = $this->filterVar($param);
                                    }
                                }
                        }
                    }
                }
                return $selected_filters;
            }
        }
        /* Analyze all the filters selected by the user and store them into a tab */
        $selected_filters = array('category' => array(), 'manufacturer' => array(), 'quantity' => array(), 'condition' => array());
        $this->page = isset($_GET['p']) ? $_GET['p'] : 1 ;
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 8) == 'layered_') {
                preg_match('/^(.*)_([0-9]+|new|used|refurbished|slider)$/', substr($key, 8, strlen($key) - 8), $res);
                if (isset($res[1])) {

                    $tmp_tab = explode('_', $this->filterVar($value));
                    $value = $this->filterVar($tmp_tab[0]);
                    $id_key = false;
                    if (isset($tmp_tab[1]))
                        $id_key = $tmp_tab[1];
                    if ($res[1] == 'condition' && in_array($value, array('new', 'used', 'refurbished')))
                        $selected_filters['condition'][] = $value;
                    else if ($res[1] == 'quantity' && (!$value || $value == 1))
                        $selected_filters['quantity'][] = $value;
                    else if (in_array($res[1], array('category', 'manufacturer'))) {
                        if (!isset($selected_filters[$res[1] . ($id_key ? '_' . $id_key : '')]))
                            $selected_filters[$res[1] . ($id_key ? '_' . $id_key : '')] = array();
                        $selected_filters[$res[1] . ($id_key ? '_' . $id_key : '')][] = (int)$value;
                    } else if (in_array($res[1], array('id_attribute_group', 'id_feature'))) {
                        if (!isset($selected_filters[$res[1]]))
                            $selected_filters[$res[1]] = array();
                        $selected_filters[$res[1]][(int)$value] = $id_key . '_' . (int)$value;
                    } else if ($res[1] == 'weight')
                        $selected_filters[$res[1]] = $tmp_tab;
                    else if ($res[1] == 'price')
                        $selected_filters[$res[1]] = $tmp_tab;
                    else if (isset($res[2]) && $res[2] == 'slider' && !in_array($res[1], array('weight', 'price'))) {
                        foreach ($tmp_tab as $k => $v)
                            $selected_filters[$res[1]][$k] = $v;
                    }
                }
            }
            elseif(substr($key, 0, 22) == 'layered-top_id_feature'){
                $data = explode('_',$value);
                $data[0] = ceil($data[0]);
                $data[1] = ceil($data[1]);
                $key = str_replace('layered-top_id_feature', '', $key);
                $key = str_replace('_slider', '', $key);
                $key_array = explode('_',$key);
                if(count($key_array) == 2){
                    $key = str_replace('_','',$key);
                    $selected_filters['id_feature'][$key] = $data[1].'_'.$data[0];
                }else
                    $selected_filters['id_feature_slider'][$key] = $data;

            }elseif (substr($key, 0,23) == 'layered-top_year_slider'){
                $data = explode('_',$value);
                $data[0] = ceil($data[0]);
                $data[1] = ceil($data[1]);
                $selected_filters['year'] = $data;
            }elseif (substr($key, 0,24) == 'layered-top_miles_slider'){
                $data = explode('_',$value);
                $data[0] = ceil($data[0]);
                $data[1] = ceil($data[1]);
                $selected_filters['miles'] = $data;
            }
        }
        return $selected_filters;
    }


    public function hookFeatureForm($params)
    {
        $values = array();
        $is_indexable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `indexable`
			FROM '._DB_PREFIX_.'layered_indexable_feature
			WHERE `id_feature` = '.(int)$params['id_feature']
        );

        if ($is_indexable === false)
            $is_indexable = true;
        if(isset($params['id_feature'])) {
            $data_feature = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT is_slider,filter_position FROM ' . _DB_PREFIX_ . 'feature WHERE id_feature=' . $params['id_feature']);
        }else{
            $data_feature['is_slider'] = 0;
            $data_feature['filter_position'] = 'left';
        }

        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `url_name`, `meta_title`, `id_lang` FROM '._DB_PREFIX_.'layered_indexable_feature_lang_value
			WHERE `id_feature` = '.(int)$params['id_feature']
        ))
            foreach ($result as $data)
                $values[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);

        $this->context->smarty->assign(array(
            'is_slider' => $data_feature['is_slider'],
            'filter_position' => $data_feature['filter_position'],
            'languages' => Language::getLanguages(false),
            'default_form_language' => (int)$this->context->controller->default_form_language,
            'values' => $values,
            'is_indexable' =>(bool)$is_indexable,
        ));

        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true)
            return $this->display(__FILE__, 'feature_form_1.6.tpl');
        else
            return $this->display(__FILE__, 'feature_form.tpl');
    }

    public function generateFiltersBlock($selected_filters, $top = false)
    {
        global $smarty;
        if ($filter_block = $this->getFilterBlock($selected_filters))
        {
            if ($filter_block['nbr_filterBlocks'] == 0)
                return false;

            $translate = array();
            $translate['price'] = $this->l('price');
            $translate['weight'] = $this->l('weight');

            $smarty->assign($filter_block);
            $smarty->assign(array(
                'hide_0_values' => Configuration::get('PS_LAYERED_HIDE_0_VALUES'),
                'blocklayeredSliderName' => $translate,
                'col_img_dir' => _PS_COL_IMG_DIR_
            ));
            return $top ? $this->display(__FILE__, 'blocklayered_top.tpl') : $this->display(__FILE__, 'blocklayered.tpl');
        }
        else
            return false;
    }

    //FEATURES
    public function hookAfterSaveFeature($params)
    {
        if($params['id_feature']) {
            $is_slider = !empty($_POST['layered_is_slider']) ? $_POST['layered_is_slider'] : 0;
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'feature SET 
			`is_slider`=' . $is_slider . ', 
			`filter_position`="' . $_POST['filter_position'] . '"
			WHERE id_feature=' . $params['id_feature'];
            Db::getInstance()->execute($sql);
        }

        if (!$params['id_feature'] || Tools::getValue('layered_indexable') === false)
            return;

        Db::getInstance()->execute(
            'DELETE FROM '._DB_PREFIX_.'layered_indexable_feature
			WHERE `id_feature` = '.(int)$params['id_feature']
        );
        Db::getInstance()->execute(
            'DELETE FROM '._DB_PREFIX_.'layered_indexable_feature_lang_value
			WHERE `id_feature` = '.(int)$params['id_feature']
        );
        
        foreach (Language::getLanguages(false) as $language)
        {
            $seo_url = Tools::getValue('url_name_'.(int)$language['id_lang']);

            if(empty($seo_url))
                $seo_url = Tools::getValue('name_'.(int)$language['id_lang']);

            Db::getInstance()->execute(
                'INSERT INTO '._DB_PREFIX_.'layered_indexable_feature_lang_value
				(`id_feature`, `id_lang`, `url_name`, `meta_title`)
				VALUES (
					'.(int)$params['id_feature'].', '.(int)$language['id_lang'].',
					\''.pSQL(Tools::link_rewrite($seo_url)).'\',
					\''.pSQL(Tools::getValue('meta_title_'.(int)$language['id_lang']), true).'\'
				)'
            );
        }
    }

    public function getFilterBlock($selected_filters = array())
    {
        global $cookie;
        static $cache = null;

        $context = Context::getContext();

        $id_lang = $context->language->id;
        $currency = $context->currency;
        $id_shop = (int) $context->shop->id;
        $alias = 'product_shop';

        if (is_array($cache))
            return $cache;

        $home_category = Configuration::get('PS_HOME_CATEGORY');
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));
        if ($id_parent == $home_category)
            return;

        $parent = new Category((int)$id_parent, $id_lang);

        /* Get the filters for the current category */
        $filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT type, id_value, filter_show_limit, filter_type FROM '._DB_PREFIX_.'layered_category
			WHERE id_category = '.(int)$id_parent.'
				AND id_shop = '.$id_shop.'
			GROUP BY `type`, id_value ORDER BY position ASC'
        );
        $filters[] = array(
            'type' => 'year',
            'id_value' => '',
            'filter_show_limit' => 0,
            'filter_type' => 0
        );
        $filters[] = array(
            'type' => 'miles',
            'id_value' => '',
            'filter_show_limit' => 0,
            'filter_type' => 0
        );


        /* Create the table which contains all the id_product in a cat or a tree */

        Db::getInstance()->execute('DROP TEMPORARY TABLE IF EXISTS '._DB_PREFIX_.'cat_restriction', false);
        Db::getInstance()->execute('CREATE TEMPORARY TABLE '._DB_PREFIX_.'cat_restriction ENGINE=MEMORY
													SELECT DISTINCT cp.id_product, p.id_manufacturer, product_shop.condition, p.weight,p.year,p.miles FROM '._DB_PREFIX_.'category_product cp
													INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND
													'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
													AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
													AND c.active = 1)
													INNER JOIN '._DB_PREFIX_.'product_shop product_shop ON (product_shop.id_product = cp.id_product
													AND product_shop.id_shop = '.(int)$context->shop->id.')
													INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product=cp.id_product)
													WHERE product_shop.`active` = 1 AND product_shop.`visibility` IN ("both", "catalog")', false);

                Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'cat_restriction ADD PRIMARY KEY (id_product),
													ADD KEY `id_manufacturer` (`id_manufacturer`,`id_product`) USING BTREE,
													ADD KEY `condition` (`condition`,`id_product`) USING BTREE,
													ADD KEY `weight` (`weight`,`id_product`) USING BTREE
													', false);
//        Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'cat_restriction ADD PRIMARY KEY (id_product),
//													ADD KEY `id_manufacturer` (`id_manufacturer`,`id_product`) USING BTREE,
//													ADD KEY `condition` (`condition`,`id_product`) USING BTREE,
//													ADD KEY `weight` (`weight`,`id_product`) USING BTREE
//													ADD KEY `year` (`year`,`id_product`) USING BTREE
//													ADD KEY `miles` (`miles`,`id_product`) USING BTREE
//													', false);

        // Remove all empty selected filters
        foreach ($selected_filters as $key => $value)
            switch ($key)
            {
                case 'price':
                case 'weight':
                    if ($value[0] === '' && $value[1] === '')
                        unset($selected_filters[$key]);
                    break;
                default:
                    if ($value == '')
                        unset($selected_filters[$key]);
                    break;
            }

        $filter_blocks = array();
        foreach ($filters as $filter)
        {
            $sql_query = array('select' => '', 'from' => '', 'join' => '', 'where' => '', 'group' => '', 'second_query' => '');
            switch ($filter['type'])
            {
                case 'price':
                    $sql_query['select'] = 'SELECT p.`id_product`, psi.price_min, psi.price_max ';
                    // price slider is not filter dependent
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'cat_restriction p';
                    $sql_query['join'] = 'INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi
								ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$context->currency->id.' AND psi.id_shop='.(int)$context->shop->id.')';
                    $sql_query['where'] = 'WHERE 1';
                    break;
                case 'weight':
                    $sql_query['select'] = 'SELECT p.`id_product`, p.`weight` ';
                    // price slider is not filter dependent
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'cat_restriction p';
                    $sql_query['where'] = 'WHERE 1';
                    break;
                case 'year':
                    $sql_query['select'] = 'SELECT p.`id_product`, p.`year` ';
                    // price slider is not filter dependent
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'cat_restriction p';
                    $sql_query['where'] = 'WHERE 1';
                    break;
                case 'miles':
                    $sql_query['select'] = 'SELECT p.`id_product`, p.`miles` ';
                    // price slider is not filter dependent
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'cat_restriction p';
                    $sql_query['where'] = 'WHERE 1';
                    break;
                case 'condition':
                    $sql_query['select'] = 'SELECT p.`id_product`, product_shop.`condition` ';
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'cat_restriction p';
                    $sql_query['where'] = 'WHERE 1';
                    $sql_query['from'] .= Shop::addSqlAssociation('product', 'p');
                    break;
                case 'quantity':
                    $sql_query['select'] = 'SELECT p.`id_product`, sa.`quantity`, sa.`out_of_stock` ';

                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'cat_restriction p';

                    $sql_query['join'] .= 'LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
						ON (sa.id_product = p.id_product AND sa.id_product_attribute=0 '.StockAvailable::addSqlShopRestriction(null, null,  'sa').') ';
                    $sql_query['where'] = 'WHERE 1';
                    break;

                case 'manufacturer':
                    $sql_query['select'] = 'SELECT COUNT(DISTINCT p.id_product) nbr, m.id_manufacturer, m.name ';
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'cat_restriction p
					INNER JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) ';
                    $sql_query['where'] = 'WHERE 1';
                    $sql_query['group'] = ' GROUP BY p.id_manufacturer ORDER BY m.name';

                    if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
                    {
                        $sql_query['second_query'] = '
							SELECT m.name, 0 nbr, m.id_manufacturer

							FROM '._DB_PREFIX_.'cat_restriction p
							INNER JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
							WHERE 1
							GROUP BY p.id_manufacturer ORDER BY m.name';
                    }

                    break;
                case 'id_attribute_group':// attribute group
                    $sql_query['select'] = '
					SELECT COUNT(DISTINCT lpa.id_product) nbr, lpa.id_attribute_group,
					a.color, al.name attribute_name, agl.public_name attribute_group_name , lpa.id_attribute, ag.is_color_group,
					liagl.url_name name_url_name, liagl.meta_title name_meta_title, lial.url_name value_url_name, lial.meta_title value_meta_title';
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'layered_product_attribute lpa
					INNER JOIN '._DB_PREFIX_.'attribute a
					ON a.id_attribute = lpa.id_attribute
					INNER JOIN '._DB_PREFIX_.'attribute_lang al
					ON al.id_attribute = a.id_attribute
					AND al.id_lang = '.(int)$id_lang.'
					INNER JOIN '._DB_PREFIX_.'cat_restriction p
					ON p.id_product = lpa.id_product
					INNER JOIN '._DB_PREFIX_.'attribute_group ag
					ON ag.id_attribute_group = lpa.id_attribute_group
					INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl
					ON agl.id_attribute_group = lpa.id_attribute_group
					AND agl.id_lang = '.(int)$id_lang.'
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value liagl
					ON (liagl.id_attribute_group = lpa.id_attribute_group AND liagl.id_lang = '.(int)$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_lang_value lial
					ON (lial.id_attribute = lpa.id_attribute AND lial.id_lang = '.(int)$id_lang.') ';

                    $sql_query['where'] = 'WHERE lpa.id_attribute_group = '.(int)$filter['id_value'];
                    $sql_query['where'] .= ' AND lpa.`id_shop` = '.(int)$context->shop->id;
                    $sql_query['group'] = '
					GROUP BY lpa.id_attribute
					ORDER BY ag.`position` ASC, a.`position` ASC';

                    if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
                    {
                        $sql_query['second_query'] = '
							SELECT 0 nbr, lpa.id_attribute_group,
								a.color, al.name attribute_name, agl.public_name attribute_group_name , lpa.id_attribute, ag.is_color_group,
								liagl.url_name name_url_name, liagl.meta_title name_meta_title, lial.url_name value_url_name, lial.meta_title value_meta_title
							FROM '._DB_PREFIX_.'layered_product_attribute lpa'.
                            Shop::addSqlAssociation('product', 'lpa').'
							INNER JOIN '._DB_PREFIX_.'attribute a
								ON a.id_attribute = lpa.id_attribute
							INNER JOIN '._DB_PREFIX_.'attribute_lang al
								ON al.id_attribute = a.id_attribute AND al.id_lang = '.(int)$id_lang.'
							INNER JOIN '._DB_PREFIX_.'product as p
								ON p.id_product = lpa.id_product
							INNER JOIN '._DB_PREFIX_.'attribute_group ag
								ON ag.id_attribute_group = lpa.id_attribute_group
							INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl
								ON agl.id_attribute_group = lpa.id_attribute_group
							AND agl.id_lang = '.(int)$id_lang.'
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value liagl
								ON (liagl.id_attribute_group = lpa.id_attribute_group AND liagl.id_lang = '.(int)$id_lang.')
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_lang_value lial
								ON (lial.id_attribute = lpa.id_attribute AND lial.id_lang = '.(int)$id_lang.')
							WHERE lpa.id_attribute_group = '.(int)$filter['id_value'].'
							AND lpa.`id_shop` = '.(int)$context->shop->id.'
							GROUP BY lpa.id_attribute
							ORDER BY id_attribute_group, id_attribute';
                    }
                    break;

                case 'id_feature':
                    $sql_query['select'] = 'SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
					COUNT(DISTINCT p.id_product) nbr,
					lifl.url_name name_url_name, lifl.meta_title name_meta_title, lifvl.url_name value_url_name, lifvl.meta_title value_meta_title ';
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'feature_product fp
					INNER JOIN '._DB_PREFIX_.'cat_restriction p
					ON p.id_product = fp.id_product
					LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = fp.id_feature AND fl.id_lang = '.$id_lang.')
					INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
					LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = '.$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_lang_value lifl
					ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = '.$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_value_lang_value lifvl
					ON (lifvl.id_feature_value = fp.id_feature_value AND lifvl.id_lang = '.$id_lang.') ';
                    $sql_query['where'] = 'WHERE fp.id_feature = '.(int)$filter['id_value'];
                    $sql_query['group'] = 'GROUP BY fv.id_feature_value ';

                    if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
                    {
                        $sql_query['second_query'] = '
							SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
							0 nbr,
							lifl.url_name name_url_name, lifl.meta_title name_meta_title, lifvl.url_name value_url_name, lifvl.meta_title value_meta_title

							FROM '._DB_PREFIX_.'feature_product fp'.
                            Shop::addSqlAssociation('product', 'fp').'
							INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
							LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = fp.id_feature AND fl.id_lang = '.(int)$id_lang.')
							INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
							LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = '.(int)$id_lang.')
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_lang_value lifl
								ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = '.(int)$id_lang.')
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_value_lang_value lifvl
								ON (lifvl.id_feature_value = fp.id_feature_value AND lifvl.id_lang = '.(int)$id_lang.')
							WHERE fp.id_feature = '.(int)$filter['id_value'].'
							GROUP BY fv.id_feature_value';
                    }

                    break;

                case 'category':
                    if (Group::isFeatureActive())
                        $this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));

                    $depth = Configuration::get('PS_LAYERED_FILTER_CATEGORY_DEPTH');
                    if ($depth === false)
                        $depth = 1;

                    $sql_query['select'] = '
					SELECT c.id_category, c.id_parent, cl.name, (SELECT count(DISTINCT p.id_product) # ';
                    $sql_query['from'] = '
					FROM '._DB_PREFIX_.'category_product cp
					LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product) ';
                    $sql_query['where'] = '
					WHERE cp.id_category = c.id_category
					AND '.$alias.'.active = 1 AND '.$alias.'.`visibility` IN ("both", "catalog")';
                    $sql_query['group'] = ') count_products
					FROM '._DB_PREFIX_.'category c
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category AND cl.`id_shop` = '.(int)Context::getContext()->shop->id.' and cl.id_lang = '.(int)$id_lang.') ';

                    if (Group::isFeatureActive())
                        $sql_query['group'] .= 'RIGHT JOIN '._DB_PREFIX_.'category_group cg ON (cg.id_category = c.id_category AND cg.`id_group` IN ('.implode(', ', $this->user_groups).')) ';

                    $sql_query['group'] .= 'WHERE c.nleft > '.(int)$parent->nleft.'
					AND c.nright < '.(int)$parent->nright.'
					'.($depth ? 'AND c.level_depth <= '.($parent->level_depth+(int)$depth) : '').'
					AND c.active = 1
					GROUP BY c.id_category ORDER BY c.nleft, c.position';

                    $sql_query['from'] .= Shop::addSqlAssociation('product', 'p');
            }

            foreach ($filters as $filter_tmp)
            {
                $method_name = 'get'.ucfirst($filter_tmp['type']).'FilterSubQuery';
                if (method_exists('BlockLayered', $method_name) &&
                    ($filter['type'] != 'price' && $filter['type'] != 'weight' && $filter['type'] != $filter_tmp['type'] || $filter['type'] == $filter_tmp['type']))
                {
                    if ($filter['type'] == $filter_tmp['type'] && $filter['id_value'] == $filter_tmp['id_value'])
                        $sub_query_filter = self::$method_name(array(), true);
                    else
                    {
                        if (!is_null($filter_tmp['id_value']))
                            $selected_filters_cleaned = $this->cleanFilterByIdValue(@$selected_filters[$filter_tmp['type']], $filter_tmp['id_value']);
                        else
                            $selected_filters_cleaned = @$selected_filters[$filter_tmp['type']];
                        $sub_query_filter = self::$method_name($selected_filters_cleaned, $filter['type'] == $filter_tmp['type']);
                    }
                    foreach ($sub_query_filter as $key => $value)
                        $sql_query[$key] .= $value;
                }
            }

            $products = false;
            if (!empty($sql_query['from']))
            {
                $products = Db::getInstance()->executeS($sql_query['select']."\n".$sql_query['from']."\n".$sql_query['join']."\n".$sql_query['where']."\n".$sql_query['group']);
            }

            // price & weight have slidebar, so it's ok to not complete recompute the product list
            if (!empty($selected_filters['price']) && $filter['type'] != 'price' && $filter['type'] != 'weight') {
                $products = self::filterProductsByPrice(@$selected_filters['price'], $products);
            }

            if (!empty($sql_query['second_query']))
            {
                $res = Db::getInstance()->executeS($sql_query['second_query']);
                if ($res)
                    $products = array_merge($products, $res);
            }

            switch ($filter['type'])
            {
                case 'year':
                    $year_array = array(
                        'type_lite' => 'year',
                        'type' => 'year',
                        'id_key' => 0,
                        'name' => $this->l('Year'),
                        'slider' => true,
                        'max' => '0',
                        'min' => null,
                        'values' => array ('1' => 0),
                        'unit' => '',
                        'format' => 5, // Ex: xxxxx kg
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );
                    if (isset($products) && $products)
                        foreach ($products as $product)
                        {
                            if (is_null($year_array['min']))
                            {
                                $year_array['min'] = $product['year'];
                                $year_array['values'][0] = $product['year'];
                            }
                            else if ($year_array['min'] > $product['year'])
                            {
                                $year_array['min'] = $product['year'];
                                $year_array['values'][0] = $product['year'];
                            }

                            if ($year_array['max'] < $product['year'])
                            {
                                $year_array['max'] = $product['year'];
                                $year_array['values'][1] = $product['year'];
                            }
                        }
                    if ($year_array['max'] != $year_array['min'] && $year_array['min'] != null)
                    {
                        if (isset($selected_filters['year']) && isset($selected_filters['year'][0])
                            && isset($selected_filters['year'][1]))
                        {
                            $year_array['values'][0] = $selected_filters['year'][0];
                            $year_array['values'][1] = $selected_filters['year'][1];
                        }
                        $filter_blocks[] = $year_array;
                    }
                    break;
                case 'miles':
                    $miles_array = array(
                        'type_lite' => 'miles',
                        'type' => 'miles',
                        'id_key' => 0,
                        'name' => $this->l('Miles'),
                        'slider' => true,
                        'max' => '0',
                        'min' => null,
                        'values' => array ('1' => 0),
                        'unit' => '',
                        'format' => 5, // Ex: xxxxx kg
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );
                    if (isset($products) && $products)
                        foreach ($products as $product)
                        {
                            if (is_null($miles_array['min']))
                            {
                                $miles_array['min'] = $product['miles'];
                                $miles_array['values'][0] = $product['miles'];
                            }
                            else if ($miles_array['min'] > $product['miles'])
                            {
                                $miles_array['min'] = $product['miles'];
                                $miles_array['values'][0] = $product['miles'];
                            }

                            if ($miles_array['max'] < $product['miles'])
                            {
                                $miles_array['max'] = $product['miles'];
                                $miles_array['values'][1] = $product['miles'];
                            }
                        }
                    if ($miles_array['max'] != $miles_array['min'] && $miles_array['min'] != null)
                    {
                        if (isset($selected_filters['miles']) && isset($selected_filters['miles'][0])
                            && isset($selected_filters['miles'][1]))
                        {
                            $miles_array['values'][0] = $selected_filters['miles'][0];
                            $miles_array['values'][1] = $selected_filters['miles'][1];
                        }
                        $filter_blocks[] = $miles_array;
                    }
                    break;
                case 'price':
                    if ($this->showPriceFilter()) {
                        $price_array = array(
                            'type_lite' => 'price',
                            'type' => 'price',
                            'id_key' => 0,
                            'name' => $this->l('Price'),
                            'slider' => true,
                            'max' => '0',
                            'min' => null,
                            'values' => array ('1' => 0),
                            'unit' => $currency->sign,
                            'format' => $currency->format,
                            'filter_show_limit' => $filter['filter_show_limit'],
                            'filter_type' => $filter['filter_type']
                        );
                        if (isset($products) && $products)
                            foreach ($products as $product)
                            {
                                if (is_null($price_array['min']))
                                {
                                    $price_array['min'] = $product['price_min'];
                                    $price_array['values'][0] = $product['price_min'];
                                }
                                else if ($price_array['min'] > $product['price_min'])
                                {
                                    $price_array['min'] = $product['price_min'];
                                    $price_array['values'][0] = $product['price_min'];
                                }

                                if ($price_array['max'] < $product['price_max'])
                                {
                                    $price_array['max'] = $product['price_max'];
                                    $price_array['values'][1] = $product['price_max'];
                                }
                            }

                        if ($price_array['max'] != $price_array['min'] && $price_array['min'] != null)
                        {
                            if ($filter['filter_type'] == 2)
                            {
                                $price_array['list_of_values'] = array();
                                $nbr_of_value = $filter['filter_show_limit'];
                                if ($nbr_of_value < 2)
                                    $nbr_of_value = 4;
                                $delta = ($price_array['max'] - $price_array['min']) / $nbr_of_value;
                                $current_step = $price_array['min'];
                                for ($i = 0; $i < $nbr_of_value; $i++)
                                    $price_array['list_of_values'][] = array(
                                        (int)($price_array['min'] + $i * $delta),
                                        (int)($price_array['min'] + ($i + 1) * $delta)
                                    );
                            }
                            if (isset($selected_filters['price']) && isset($selected_filters['price'][0])
                                && isset($selected_filters['price'][1]))
                            {
                                $price_array['values'][0] = $selected_filters['price'][0];
                                $price_array['values'][1] = $selected_filters['price'][1];
                            }
                            $filter_blocks[] = $price_array;
                        }
                    }
                    break;

                case 'weight':
                    $weight_array = array(
                        'type_lite' => 'weight',
                        'type' => 'weight',
                        'id_key' => 0,
                        'name' => $this->l('Weight'),
                        'slider' => true,
                        'max' => '0',
                        'min' => null,
                        'values' => array ('1' => 0),
                        'unit' => Configuration::get('PS_WEIGHT_UNIT'),
                        'format' => 5, // Ex: xxxxx kg
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );
                    if (isset($products) && $products)
                        foreach ($products as $product)
                        {
                            if (is_null($weight_array['min']))
                            {
                                $weight_array['min'] = $product['weight'];
                                $weight_array['values'][0] = $product['weight'];
                            }
                            else if ($weight_array['min'] > $product['weight'])
                            {
                                $weight_array['min'] = $product['weight'];
                                $weight_array['values'][0] = $product['weight'];
                            }

                            if ($weight_array['max'] < $product['weight'])
                            {
                                $weight_array['max'] = $product['weight'];
                                $weight_array['values'][1] = $product['weight'];
                            }
                        }
                    if ($weight_array['max'] != $weight_array['min'] && $weight_array['min'] != null)
                    {
                        if (isset($selected_filters['weight']) && isset($selected_filters['weight'][0])
                            && isset($selected_filters['weight'][1]))
                        {
                            $weight_array['values'][0] = $selected_filters['weight'][0];
                            $weight_array['values'][1] = $selected_filters['weight'][1];
                        }
                        $filter_blocks[] = $weight_array;
                    }
                    break;

                case 'condition':
                    $condition_array = array(
                        'new' => array('name' => $this->l('New'),'nbr' => 0),
                        'used' => array('name' => $this->l('Used'), 'nbr' => 0),
                        'refurbished' => array('name' => $this->l('Refurbished'),
                            'nbr' => 0)
                    );
                    if (isset($products) && $products)
                        foreach ($products as $product)
                            if (isset($selected_filters['condition']) && in_array($product['condition'], $selected_filters['condition']))
                                $condition_array[$product['condition']]['checked'] = true;
                    foreach ($condition_array as $key => $condition)
                        if (isset($selected_filters['condition']) && in_array($key, $selected_filters['condition']))
                            $condition_array[$key]['checked'] = true;
                    if (isset($products) && $products)
                        foreach ($products as $product)
                            if (isset($condition_array[$product['condition']]))
                                $condition_array[$product['condition']]['nbr']++;
                    $filter_blocks[] = array(
                        'type_lite' => 'condition',
                        'type' => 'condition',
                        'id_key' => 0,
                        'name' => $this->l('Condition'),
                        'values' => $condition_array,
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );
                    break;

                case 'quantity':
                    $quantity_array = array (
                        0 => array('name' => $this->l('Not available'), 'nbr' => 0),
                        1 => array('name' => $this->l('In stock'), 'nbr' => 0)
                    );
                    foreach ($quantity_array as $key => $quantity)
                        if (isset($selected_filters['quantity']) && in_array($key, $selected_filters['quantity']))
                            $quantity_array[$key]['checked'] = true;
                    if (isset($products) && $products)
                        foreach ($products as $product)
                        {
                            //If oosp move all not available quantity to available quantity
                            if ((int)$product['quantity'] > 0 || Product::isAvailableWhenOutOfStock($product['out_of_stock']))
                                $quantity_array[1]['nbr']++;
                            else
                                $quantity_array[0]['nbr']++;
                        }

                    $filter_blocks[] = array(
                        'type_lite' => 'quantity',
                        'type' => 'quantity',
                        'id_key' => 0,
                        'name' => $this->l('Availability'),
                        'values' => $quantity_array,
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );

                    break;

                case 'manufacturer':
                    if (isset($products) && $products)
                    {
                        $manufaturers_array = array();
                        foreach ($products as $manufacturer)
                        {
                            if (!isset($manufaturers_array[$manufacturer['id_manufacturer']]))
                                $manufaturers_array[$manufacturer['id_manufacturer']] = array('name' => $manufacturer['name'], 'nbr' => $manufacturer['nbr']);
                            if (isset($selected_filters['manufacturer']) && in_array((int)$manufacturer['id_manufacturer'], $selected_filters['manufacturer']))
                                $manufaturers_array[$manufacturer['id_manufacturer']]['checked'] = true;
                        }
                        $filter_blocks[] = array(
                            'type_lite' => 'manufacturer',
                            'type' => 'manufacturer',
                            'id_key' => 0,
                            'name' => $this->l('Manufacturer'),
                            'values' => $manufaturers_array,
                            'filter_show_limit' => $filter['filter_show_limit'],
                            'filter_type' => $filter['filter_type']
                        );
                    }
                    break;

                case 'id_attribute_group':
                    $attributes_array = array();
                    if (isset($products) && $products)
                    {
                        foreach ($products as $attributes)
                        {
                            if (!isset($attributes_array[$attributes['id_attribute_group']]))
                                $attributes_array[$attributes['id_attribute_group']] = array (
                                    'type_lite' => 'id_attribute_group',
                                    'type' => 'id_attribute_group',
                                    'id_key' => (int)$attributes['id_attribute_group'],
                                    'name' =>  $attributes['attribute_group_name'],
                                    'is_color_group' => (bool)$attributes['is_color_group'],
                                    'values' => array(),
                                    'url_name' => $attributes['name_url_name'],
                                    'meta_title' => $attributes['name_meta_title'],
                                    'filter_show_limit' => $filter['filter_show_limit'],
                                    'filter_type' => $filter['filter_type']
                                );

                            if (!isset($attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']]))
                                $attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']] = array(
                                    'color' => $attributes['color'],
                                    'name' => $attributes['attribute_name'],
                                    'nbr' => (int)$attributes['nbr'],
                                    'url_name' => $attributes['value_url_name'],
                                    'meta_title' => $attributes['value_meta_title']
                                );

                            if (isset($selected_filters['id_attribute_group'][$attributes['id_attribute']]))
                                $attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']]['checked'] = true;
                        }

                        $filter_blocks = array_merge($filter_blocks, $attributes_array);
                    }
                    break;
                case 'id_feature':

                    $feature_array = array();
                    if (isset($products) && $products)
                    {
                        foreach ($products as $feature)
                        {
                            if (!isset($feature_array[$feature['id_feature']]))
                                $feature_array[$feature['id_feature']] = array(
                                    'type_lite' => 'id_feature',
                                    'type' => 'id_feature',
                                    'id_key' => (int)$feature['id_feature'],
                                    'values' => array(),
                                    'name' => $feature['feature_name'],
                                    'url_name' => $feature['name_url_name'],
                                    'meta_title' => $feature['name_meta_title'],
                                    'filter_show_limit' => $filter['filter_show_limit'],
                                    'filter_type' => $filter['filter_type']
                                );

                            if (!isset($feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']]))
                                $feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']] = array(
                                    'nbr' => (int)$feature['nbr'],
                                    'name' => $feature['value'],
                                    'url_name' => $feature['value_url_name'],
                                    'meta_title' => $feature['value_meta_title']
                                );

                            if (isset($selected_filters['id_feature'][$feature['id_feature_value']]))
                                $feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']]['checked'] = true;
                        }

                        //Natural sort
                        foreach ($feature_array as $key => $value)
                        {
                            $temp = array();
                            foreach ($feature_array[$key]['values'] as $keyint => $valueint)
                                $temp[$keyint] = $valueint['name'];

                            natcasesort($temp);
                            $temp2 = array();

                            foreach ($temp as $keytemp => $valuetemp)
                                $temp2[$keytemp] = $feature_array[$key]['values'][$keytemp];

                            $feature_array[$key]['values'] = $temp2;
                        }

                        $filter_blocks = array_merge($filter_blocks, $feature_array);
                    }
                    break;

                case 'category':
                    $tmp_array = array();
                    if (isset($products) && $products)
                    {
                        $categories_with_products_count = 0;
                        foreach ($products as $category)
                        {
                            $tmp_array[$category['id_category']] = array(
                                'name' => $category['name'],
                                'nbr' => (int)$category['count_products']
                            );

                            if ((int)$category['count_products'])
                                $categories_with_products_count++;

                            if (isset($selected_filters['category']) && in_array($category['id_category'], $selected_filters['category']))
                                $tmp_array[$category['id_category']]['checked'] = true;
                        }
                        if ($categories_with_products_count || !Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
                            $filter_blocks[] = array (
                                'type_lite' => 'category',
                                'type' => 'category',
                                'id_key' => 0, 'name' => $this->l('Categories'),
                                'values' => $tmp_array,
                                'filter_show_limit' => $filter['filter_show_limit'],
                                'filter_type' => $filter['filter_type']
                            );
                    }
                    break;
            }
        }

        // All non indexable attribute and feature
        $non_indexable = array();

        // Get all non indexable attribute groups
        foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT public_name
		FROM `'._DB_PREFIX_.'attribute_group_lang` agl
		LEFT JOIN `'._DB_PREFIX_.'layered_indexable_attribute_group` liag
		ON liag.id_attribute_group = agl.id_attribute_group
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = '.(int)$id_lang) as $attribute)
            $non_indexable[] = Tools::link_rewrite($attribute['public_name']);

        // Get all non indexable features
        foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT name
		FROM `'._DB_PREFIX_.'feature_lang` fl
		LEFT JOIN  `'._DB_PREFIX_.'layered_indexable_feature` lif
		ON lif.id_feature = fl.id_feature
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = '.(int)$id_lang) as $attribute)
            $non_indexable[] = Tools::link_rewrite($attribute['name']);

        //generate SEO link
        $param_selected = '';
        $param_product_url = '';
        $option_checked_array = array();
        $param_group_selected_array = array();
        $title_values = array();
        $meta_values = array();

        //get filters checked by group

        foreach ($filter_blocks as $type_filter)
        {
            $filter_name = (!empty($type_filter['url_name']) ? $type_filter['url_name'] : $type_filter['name']);
            $filter_meta = (!empty($type_filter['meta_title']) ? $type_filter['meta_title'] : $type_filter['name']);
            $attr_key = $type_filter['type'].'_'.$type_filter['id_key'];

            $param_group_selected = '';
            $lower_filter = strtolower($type_filter['type']);
            $filter_name_rewritten = Tools::link_rewrite($filter_name);
//            if (($lower_filter == 'price' || $lower_filter == 'weight' || $lower_filter == 'year' || $lower_filter == 'miles'))
            if (($lower_filter == 'price' || $lower_filter == 'weight' || $lower_filter == 'year' || $lower_filter == 'miles')
                && (float)$type_filter['values'][0] > (float)$type_filter['min']
                && (float)$type_filter['values'][1] > (float)$type_filter['max'])
            {

                $param_group_selected .= $this->getAnchor().str_replace($this->getAnchor(), '_', $type_filter['values'][0])
                    .$this->getAnchor().str_replace($this->getAnchor(), '_', $type_filter['values'][1]);
                $param_group_selected_array[$filter_name_rewritten][] = $filter_name_rewritten;

                if (!isset($title_values[$filter_meta]))
                    $title_values[$filter_meta] = array();
                $title_values[$filter_meta][] = $filter_meta;
                if (!isset($meta_values[$attr_key]))
                    $meta_values[$attr_key] = array('title' => $filter_meta, 'values' => array());
                $meta_values[$attr_key]['values'][] = $filter_meta;
            }
            else
            {
                foreach ($type_filter['values'] as $key => $value)
                {
                    if (is_array($value) && array_key_exists('checked', $value ))
                    {
                        $value_name = !empty($value['url_name']) ? $value['url_name'] : $value['name'];
                        $value_meta = !empty($value['meta_title']) ? $value['meta_title'] : $value['name'];
                        $param_group_selected .= $this->getAnchor().str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name));
                        $param_group_selected_array[$filter_name_rewritten][] = Tools::link_rewrite($value_name);

                        if (!isset($title_values[$filter_meta]))
                            $title_values[$filter_meta] = array();
                        $title_values[$filter_meta][] = $value_name;
                        if (!isset($meta_values[$attr_key]))
                            $meta_values[$attr_key] = array('title' => $filter_meta, 'values' => array());
                        $meta_values[$attr_key]['values'][] = $value_meta;
                    }
                    else
                        $param_group_selected_array[$filter_name_rewritten][] = array();
                }
            }

            if (!empty($param_group_selected))
            {
                $param_selected .= '/'.str_replace($this->getAnchor(), '_', $filter_name_rewritten).$param_group_selected;
                $option_checked_array[$filter_name_rewritten] = $param_group_selected;
            }
            // select only attribute and group attribute to display an unique product combination link
            if (!empty($param_group_selected) && $type_filter['type'] == 'id_attribute_group')
                $param_product_url .= '/'.str_replace($this->getAnchor(), '_', $filter_name_rewritten).$param_group_selected;

        }

        if ($this->page > 1)
            $param_selected .= '/page-'.$this->page;

        $blacklist = array('weight', 'price');

        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CDT'))
            $blacklist[] = 'condition';
        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_QTY'))
            $blacklist[] = 'quantity';
        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_MNF'))
            $blacklist[] = 'manufacturer';
        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CAT'))
            $blacklist[] = 'category';

        $global_nofollow = false;
        $categorie_link = Context::getContext()->link->getCategoryLink($parent, null, null);

        foreach ($filter_blocks as &$type_filter)
        {
            $filter_name = (!empty($type_filter['url_name']) ? $type_filter['url_name'] : $type_filter['name']);
            $filter_link_rewrite = Tools::link_rewrite($filter_name);

            if (count($type_filter) > 0 && !isset($type_filter['slider']))
            {
                foreach ($type_filter['values'] as $key => $values)
                {
                    $nofollow = false;
                    if (!empty($values['checked']) && in_array($type_filter['type'], $blacklist))
                        $global_nofollow = true;

                    $option_checked_clone_array = $option_checked_array;

                    // If not filters checked, add parameter
                    $value_name = !empty($values['url_name']) ? $values['url_name'] : $values['name'];

                    if (!in_array(Tools::link_rewrite($value_name), $param_group_selected_array[$filter_link_rewrite]))
                    {
                        // Update parameter filter checked before
                        if (array_key_exists($filter_link_rewrite, $option_checked_array))
                        {
                            $option_checked_clone_array[$filter_link_rewrite] = $option_checked_clone_array[$filter_link_rewrite].$this->getAnchor().str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name));

                            if (in_array($type_filter['type'], $blacklist))
                                $nofollow = true;
                        }
                        else
                            $option_checked_clone_array[$filter_link_rewrite] = $this->getAnchor().str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name));
                    }
                    else
                    {
                        // Remove selected parameters
                        $option_checked_clone_array[$filter_link_rewrite] = str_replace($this->getAnchor().str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name)), '', $option_checked_clone_array[$filter_link_rewrite]);
                        if (empty($option_checked_clone_array[$filter_link_rewrite]))
                            unset($option_checked_clone_array[$filter_link_rewrite]);
                    }
                    $parameters = '';
                    ksort($option_checked_clone_array); // Order parameters
                    foreach ($option_checked_clone_array as $key_group => $value_group)
                        $parameters .= '/'.str_replace($this->getAnchor(), '_', $key_group).$value_group;

                    // Add nofollow if any blacklisted filters ins in parameters
                    foreach ($filter_blocks as $filter)
                    {
                        $name = Tools::link_rewrite((!empty($filter['url_name']) ? $filter['url_name'] : $filter['name']));
                        if (in_array($filter['type'], $blacklist) && strpos($parameters, $name.'-') !== false)
                            $nofollow = true;
                    }

                    // Check if there is an non indexable attribute or feature in the url
                    foreach ($non_indexable as $value)
                        if (strpos($parameters, '/'.$value) !== false)
                            $nofollow = true;

                    $type_filter['values'][$key]['link'] = $categorie_link.'#'.ltrim($parameters, '/');
                    $type_filter['values'][$key]['rel'] = ($nofollow) ? 'nofollow' : '';
                }
            }
        }

        $n_filters = 0;

        if (isset($selected_filters['price']))
            if ($price_array['min'] == $selected_filters['price'][0] && $price_array['max'] == $selected_filters['price'][1])
                unset($selected_filters['price']);
        if (isset($selected_filters['weight']))
            if ($weight_array['min'] == $selected_filters['weight'][0] && $weight_array['max'] == $selected_filters['weight'][1])
                unset($selected_filters['weight']);

        foreach ($selected_filters as $filters)
            $n_filters += count($filters);

        $filter_blocks_top = array();
        $filter_blocks_left = array();



        foreach ($filter_blocks as &$block){
            if(in_array($block['type_lite'],array('price','year','miles'))){
                $filter_blocks_top[] = $block;
            }
            elseif($block['type_lite'] == 'id_feature'){
                $sql = "SELECT id_feature,is_slider,filter_position FROM "._DB_PREFIX_."feature WHERE id_feature = ".$block['id_key'];
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

                if($result['is_slider'] == 1){
                    if(count($block['values']) > 1) {
                        $block['slider'] = true;
                        $block['min'] = $this->getFeatrureRange($block['values'], 'min');
                        $block['max'] = $this->getFeatrureRange($block['values'], 'max');
                        $block['values'] = $this->getFeatrureValues($block, $selected_filters);
                        $block['format'] = 1;
                        $block['unit'] = "";
                        $block['type'] .= $block['id_key'];
                    }
                }

                if($result['filter_position'] == 'top'){
                    $filter_blocks_top[] = $block;
                }else{
                    $filter_blocks_left[] = $block;
                }
            }else{
                $filter_blocks_left[] = $block;

            }
        }

        $cache = array(
            'layered_show_qties' => (int)Configuration::get('PS_LAYERED_SHOW_QTIES'),
            'id_category_layered' => (int)$id_parent,
            'selected_filters' => $selected_filters,
            'n_filters' => (int)$n_filters,
            'nbr_filterBlocks' => count($filter_blocks),
            'nbr_filterBlocksTop' => count($filter_blocks_top),
            'nbr_filterBlocksLeft' => count($filter_blocks_left),
            'filters' => $filter_blocks,
            'filters_top' => $filter_blocks_top,
            'filters_left' => $filter_blocks_left,
            'title_values' => $title_values,
            'meta_values' => $meta_values,
            'current_friendly_url' => $param_selected,
            'param_product_url' => $param_product_url,
            'no_follow' => (!empty($param_selected) || $global_nofollow)
        );

        return $cache;
    }

    public function getFeatrureValues($data,$selected_filters){
        if(isset($selected_filters['id_feature_slider'][$data['id_key']]))
            return $selected_filters['id_feature_slider'][$data['id_key']];
        return array($data['min'],$data['max']);
    }

    public function getFeatrureRange($data,$mod){
        $first = current($data);
        if($mod == 'min'){
            $min = $first['name'];;
            foreach ($data as $part){
                if($part['name'] < $min)
                    $min = $part['name'];
            }
            return $min;
        }else{
            $max = $first['name'];
            foreach ($data as $part){
                if($part['name'] > $max)
                    $max = $part['name'];
            }
            return $max;
        }
    }

    public function getProductByFilters($selected_filters = array())
    {

        global $cookie;

        if (!empty($this->products))
            return $this->products;

        $home_category = Configuration::get('PS_HOME_CATEGORY');
        /* If the current category isn't defined or if it's homepage, we have nothing to display */
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));
        if ($id_parent == $home_category)
            return false;

        $alias_where = 'p';
        if (version_compare(_PS_VERSION_,'1.5','>'))
            $alias_where = 'product_shop';

        $query_filters_where = ' AND '.$alias_where.'.`active` = 1 AND '.$alias_where.'.`visibility` IN ("both", "catalog")';
        $query_filters_from = '';

        $parent = new Category((int)$id_parent);

        $context = Context::getContext();
        $id_lang = $context->language->id;

        foreach ($selected_filters as $key => $filter_values)
        {
            if (!count($filter_values))
                continue;

            preg_match('/^(.*[^_0-9])/', $key, $res);
            $key = $res[1];


            switch ($key)
            {
                case 'id_feature_slider':
                    foreach ($filter_values as $filter_key=>$filter_value) {
                        $sql = "SELECT f.id_feature,fv.id_feature_value,fvl.value FROM " . _DB_PREFIX_ . "feature f
                        LEFT JOIN " . _DB_PREFIX_ . "feature_value fv ON (f.id_feature = fv.id_feature)
                        LEFT JOIN " . _DB_PREFIX_ . "feature_value_lang fvl ON (fv.id_feature_value = fvl.id_feature_value AND id_lang={$id_lang})
                        WHERE f.id_feature={$filter_key} AND fvl.value >= $filter_value[0] AND  fvl.value <= $filter_value[1]";

                        $filter_values_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        $id_feature_value_where = array();

                        foreach ($filter_values_result as $data)
                            if(isset($data['id_feature_value']))
                                $id_feature_value_where[] = (int)$data['id_feature_value'];

                        $query_filters_where .= ' AND p.id_product IN (SELECT `id_product` FROM `'._DB_PREFIX_.'feature_product` fp WHERE ';
                        $query_filters_where .= 'fp.`id_feature_value` IN ('.implode(',',$id_feature_value_where)."))";
                    }
                    break;
                case 'id_feature':
                    $sub_queries = array();
                    foreach ($filter_values as $filter_value)
                    {
                        $filter_value_array = explode('_', $filter_value);
                        if (!isset($sub_queries[$filter_value_array[0]]))
                            $sub_queries[$filter_value_array[0]] = array();
                        $sub_queries[$filter_value_array[0]][] = 'fp.`id_feature_value` = '.(int)$filter_value_array[1];
                    }
                    foreach ($sub_queries as $sub_query)
                    {
                        $query_filters_where .= ' AND p.id_product IN (SELECT `id_product` FROM `'._DB_PREFIX_.'feature_product` fp WHERE ';
                        $query_filters_where .= implode(' OR ', $sub_query).') ';
                    }
                    break;

                case 'id_attribute_group':
                    $sub_queries = array();


                    foreach ($filter_values as $filter_value)
                    {
                        $filter_value_array = explode('_', $filter_value);
                        if (!isset($sub_queries[$filter_value_array[0]]))
                            $sub_queries[$filter_value_array[0]] = array();
                        $sub_queries[$filter_value_array[0]][] = 'pac.`id_attribute` = '.(int)$filter_value_array[1];
                    }
                    foreach ($sub_queries as $sub_query)
                    {
                        $query_filters_where .= ' AND p.id_product IN (SELECT pa.`id_product`
						FROM `'._DB_PREFIX_.'product_attribute_combination` pac
						LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
						ON (pa.`id_product_attribute` = pac.`id_product_attribute`)'.
                            Shop::addSqlAssociation('product_attribute', 'pa').'
						WHERE '.implode(' OR ', $sub_query).') ';
                    }
                    break;

                case 'category':
                    $query_filters_where .= ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE ';
                    foreach ($selected_filters['category'] as $id_category)
                        $query_filters_where .= 'cp.`id_category` = '.(int)$id_category.' OR ';
                    $query_filters_where = rtrim($query_filters_where, 'OR ').')';
                    break;

                case 'quantity':
                    if (count($selected_filters['quantity']) == 2)
                        break;

                    $query_filters_where .= ' AND sa.quantity '.(!$selected_filters['quantity'][0] ? '<=' : '>').' 0 ';
                    $query_filters_from .= 'LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (sa.id_product = p.id_product '.StockAvailable::addSqlShopRestriction(null, null,  'sa').') ';
                    break;

                case 'manufacturer':
                    $query_filters_where .= ' AND p.id_manufacturer IN ('.implode($selected_filters['manufacturer'], ',').')';
                    break;

                case 'condition':
                    if (count($selected_filters['condition']) == 3)
                        break;
                    $query_filters_where .= ' AND '.$alias_where.'.condition IN (';
                    foreach ($selected_filters['condition'] as $cond)
                        $query_filters_where .= '\''.pSQL($cond).'\',';
                    $query_filters_where = rtrim($query_filters_where, ',').')';
                    break;

                case 'weight':
                    if ($selected_filters['weight'][0] != 0 || $selected_filters['weight'][1] != 0)
                        $query_filters_where .= ' AND p.`weight` BETWEEN '.(float)($selected_filters['weight'][0] - 0.001).' AND '.(float)($selected_filters['weight'][1] + 0.001);
                    break;

                case 'price':
                    if (isset($selected_filters['price']))
                    {
                        if ($selected_filters['price'][0] !== '' || $selected_filters['price'][1] !== '')
                        {
                            $price_filter = array();
                            $price_filter['min'] = (float)($selected_filters['price'][0]);
                            $price_filter['max'] = (float)($selected_filters['price'][1]);
                        }
                    }
                    else
                        $price_filter = false;
                    break;
                case 'year':
                    if ($selected_filters['year'][0] != 0 || $selected_filters['year'][1] != 0)
                        $query_filters_where .= ' AND p.`year` BETWEEN ' . (int)($selected_filters['year'][0]) . ' AND ' . (int)($selected_filters['year'][1]);
                    break;
                case 'miles':
                    if ($selected_filters['miles'][0] != 0 || $selected_filters['miles'][1] != 0)
                        $query_filters_where .= ' AND p.`miles` BETWEEN ' . (int)($selected_filters['miles'][0]) . ' AND ' . (int)($selected_filters['miles'][1]);
                    break;
            }
        }


        $context = Context::getContext();
        $id_currency = (int)$context->currency->id;

        $price_filter_query_in = ''; // All products with price range between price filters limits
        $price_filter_query_out = ''; // All products with a price filters limit on it price range
        if (isset($price_filter) && $price_filter)
        {
            $price_filter_query_in = 'INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi
			ON
			(
				psi.price_min <= '.(int)$price_filter['max'].'
				AND psi.price_max >= '.(int)$price_filter['min'].'
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_shop` = '.(int)$context->shop->id.'
				AND psi.`id_currency` = '.$id_currency.'
			)';

            $price_filter_query_out = 'INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi
			ON
				((psi.price_min < '.(int)$price_filter['min'].' AND psi.price_max > '.(int)$price_filter['min'].')
				OR
				(psi.price_max > '.(int)$price_filter['max'].' AND psi.price_min < '.(int)$price_filter['max'].'))
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_shop` = '.(int)$context->shop->id.'
				AND psi.`id_currency` = '.$id_currency;
        }

        $query_filters_from .= Shop::addSqlAssociation('product', 'p');

        Db::getInstance()->execute('DROP TEMPORARY TABLE IF EXISTS '._DB_PREFIX_.'cat_filter_restriction', false);
        if (empty($selected_filters['category']))
        {
            /* Create the table which contains all the id_product in a cat or a tree */
            Db::getInstance()->execute('CREATE TEMPORARY TABLE '._DB_PREFIX_.'cat_filter_restriction ENGINE=MEMORY
														SELECT cp.id_product, MIN(cp.position) position FROM '._DB_PREFIX_.'category_product cp
														INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND
														'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
														AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
														AND c.active = 1)
														JOIN `'._DB_PREFIX_.'product` p USING (id_product)
														'.$price_filter_query_in.'
														'.$query_filters_from.'
														WHERE 1 '.$query_filters_where.'
														GROUP BY cp.id_product ORDER BY position, id_product', false);
        } else {
            $categories = array_map('intval', $selected_filters['category']);

            Db::getInstance()->execute('CREATE TEMPORARY TABLE '._DB_PREFIX_.'cat_filter_restriction ENGINE=MEMORY
														SELECT cp.id_product, MIN(cp.position) position FROM '._DB_PREFIX_.'category_product cp
														JOIN `'._DB_PREFIX_.'product` p USING (id_product)
														'.$price_filter_query_in.'
														'.$query_filters_from.'
														WHERE cp.`id_category` IN ('.implode(',', $categories).') '.$query_filters_where.'
														GROUP BY cp.id_product ORDER BY position, id_product', false);
        }
        Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'cat_filter_restriction ADD PRIMARY KEY (id_product), ADD KEY (position, id_product) USING BTREE', false);

        if (isset($price_filter) && $price_filter) {
            static $ps_layered_filter_price_usetax = null;
            static $ps_layered_filter_price_rounding = null;

            if ($ps_layered_filter_price_usetax === null) {
                $ps_layered_filter_price_usetax = Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX');
            }

            if ($ps_layered_filter_price_rounding === null) {
                $ps_layered_filter_price_rounding = Configuration::get('PS_LAYERED_FILTER_PRICE_ROUNDING');
            }

            if (empty($selected_filters['category'])) {
                $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.`id_product` id_product
				FROM `'._DB_PREFIX_.'product` p JOIN '._DB_PREFIX_.'category_product cp USING (id_product)
				INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND
					'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
					AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
					AND c.active = 1)
				'.$price_filter_query_out.'
				'.$query_filters_from.'
				WHERE 1 '.$query_filters_where.' GROUP BY cp.id_product');
            } else {
                $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.`id_product` id_product
				FROM `'._DB_PREFIX_.'product` p JOIN '._DB_PREFIX_.'category_product cp USING (id_product)
				'.$price_filter_query_out.'
				'.$query_filters_from.'
				WHERE cp.`id_category` IN ('.implode(',', $categories).') '.$query_filters_where.' GROUP BY cp.id_product');
            }

            /* for this case, price could be out of range, so we need to compute the real price */
            foreach($all_products_out as $product) {
                $price = Product::getPriceStatic($product['id_product'], $ps_layered_filter_price_usetax);
                if ($ps_layered_filter_price_rounding) {
                    $price = (int)$price;
                }
                if ($price < $price_filter['min'] || $price > $price_filter['max']) {
                    // out of range price, exclude the product
                    $product_id_delete_list[] = (int)$product['id_product'];
                }
            }
            if (!empty($product_id_delete_list)) {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cat_filter_restriction WHERE id_product IN ('.implode(',', $product_id_delete_list).')');
            }
        }
        $this->nbr_products = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'cat_filter_restriction');

        if ($this->nbr_products == 0)
            $this->products = array();
        else
        {
            $product_per_page = isset($this->context->cookie->nb_item_per_page) ? (int)$this->context->cookie->nb_item_per_page : Configuration::get('PS_PRODUCTS_PER_PAGE');
            $default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
            $n = $default_products_per_page;
            if (isset($this->context->cookie->nb_item_per_page)) {
                $n = (int)$this->context->cookie->nb_item_per_page;
            }
            if ((int)Tools::getValue('n')) {
                $n = (int)Tools::getValue('n');
            }
            $nb_day_new_product = (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20);

            if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true)
            {
//                echo '
//				SELECT
//					p.*,
//					'.($alias_where == 'p' ? '' : 'product_shop.*,' ).'
//					'.$alias_where.'.id_category_default,
//					pl.*,
//					image_shop.`id_image` id_image,
//					il.legend,
//					m.name manufacturer_name,
//					'.(Combination::isFeatureActive() ? 'product_attribute_shop.id_product_attribute id_product_attribute,' : '').'
//					DATEDIFF('.$alias_where.'.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00", INTERVAL '.(int)$nb_day_new_product.' DAY)) > 0 AS new,
//					stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'.(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '').'
//				FROM '._DB_PREFIX_.'cat_filter_restriction cp
//				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
//				'.Shop::addSqlAssociation('product', 'p').
//                    (Combination::isFeatureActive() ?
//                        ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
//					ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
//				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').' AND pl.id_lang = '.(int)$cookie->id_lang.')
//				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
//					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
//				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$cookie->id_lang.')
//				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
//				'.Product::sqlStock('p', 0).'
//				WHERE '.$alias_where.'.`active` = 1 AND '.$alias_where.'.`visibility` IN ("both", "catalog")
//				ORDER BY '.Tools::getProductsOrder('by', Tools::getValue('orderby'), true).' '.Tools::getProductsOrder('way', Tools::getValue('orderway')).' , cp.id_product'.
//                    ' LIMIT '.(((int)$this->page - 1) * $n.','.$n);
//                die();
                $this->products = Db::getInstance()->executeS('
				SELECT
					p.*,
					'.($alias_where == 'p' ? '' : 'product_shop.*,' ).'
					'.$alias_where.'.id_category_default,
					pl.*,
					image_shop.`id_image` id_image,
					il.legend,
					m.name manufacturer_name,
					'.(Combination::isFeatureActive() ? 'product_attribute_shop.id_product_attribute id_product_attribute,' : '').'
					DATEDIFF('.$alias_where.'.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00", INTERVAL '.(int)$nb_day_new_product.' DAY)) > 0 AS new,
					stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'.(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '').'
				FROM '._DB_PREFIX_.'cat_filter_restriction cp
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
                    (Combination::isFeatureActive() ?
                        ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
					ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').' AND pl.id_lang = '.(int)$cookie->id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$cookie->id_lang.')
				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				'.Product::sqlStock('p', 0).'
				WHERE '.$alias_where.'.`active` = 1 AND '.$alias_where.'.`visibility` IN ("both", "catalog")
				ORDER BY '.Tools::getProductsOrder('by', Tools::getValue('orderby'), true).' '.Tools::getProductsOrder('way', Tools::getValue('orderway')).' , cp.id_product'.
                    ' LIMIT '.(((int)$this->page - 1) * $n.','.$n));
            }
            else
            {
                $this->products = Db::getInstance()->executeS('
				SELECT
					p.*,
					'.($alias_where == 'p' ? '' : 'product_shop.*,' ).'
					'.$alias_where.'.id_category_default,
					pl.*,
					MAX(image_shop.`id_image`) id_image,
					il.legend,
					m.name manufacturer_name,
					'.(Combination::isFeatureActive() ? 'MAX(product_attribute_shop.id_product_attribute) id_product_attribute,' : '').'
					DATEDIFF('.$alias_where.'.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00", INTERVAL '.(int)$nb_day_new_product.' DAY)) > 0 AS new,
					stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'.(Combination::isFeatureActive() ? ', MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity' : '').'
				FROM '._DB_PREFIX_.'cat_filter_restriction cp
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
                    (Combination::isFeatureActive() ?
                        'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id):'').'
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').' AND pl.id_lang = '.(int)$cookie->id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'image` i  ON (i.`id_product` = p.`id_product`)'.
                    Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$cookie->id_lang.')
				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				'.Product::sqlStock('p', 0).'
				WHERE '.$alias_where.'.`active` = 1 AND '.$alias_where.'.`visibility` IN ("both", "catalog")
				GROUP BY product_shop.id_product
				ORDER BY '.Tools::getProductsOrder('by', Tools::getValue('orderby'), true).' '.Tools::getProductsOrder('way', Tools::getValue('orderway')).' , cp.id_product'.
                    ' LIMIT '.(((int)$this->page - 1) * $n.','.$n));
            }
        }

        if (Tools::getProductsOrder('by', Tools::getValue('orderby'), true) == 'p.price')
            Tools::orderbyPrice($this->products, Tools::getProductsOrder('way', Tools::getValue('orderway')));

        return $this->products;
    }

    public function getProducts($selected_filters, &$products, &$nb_products, &$p, &$n, &$pages_nb, &$start, &$stop, &$range)
    {
        global $cookie;

        $products = $this->getProductByFilters($selected_filters);
        $products = Product::getProductsProperties((int)$cookie->id_lang, $products);
        $nb_products = $this->nbr_products;
        $range = 2; /* how many pages around page selected */

        $product_per_page = isset($this->context->cookie->nb_item_per_page) ? (int)$this->context->cookie->nb_item_per_page : Configuration::get('PS_PRODUCTS_PER_PAGE');
        $n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
        if(!$n)
            $n = Configuration::get('PS_PRODUCTS_PER_PAGE');
        if ($n <= 0)
            $n = 1;

        $p = $this->page;

        if ($p < 0)
            $p = 0;

        if ($p > ($nb_products / $n))
            $p = ceil($nb_products / $n);
        $pages_nb = ceil($nb_products / (int)($n));

        $start = (int)($p - $range);
        if ($start < 1)
            $start = 1;

        $stop = (int)($p + $range);
        if ($stop > $pages_nb)
            $stop = (int)($pages_nb);

        foreach ($products as &$product)
        {
            if ($product['id_product_attribute'] && isset($product['product_attribute_minimal_quantity']))
                $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
        }
    }


    private static function getCategoryFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value))
            return array();
        $query_filters_where = ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'category_product cp WHERE id_product = p.id_product AND ';
        foreach ($filter_value as $id_category)
            $query_filters_where .= 'cp.`id_category` = '.(int)$id_category.' OR ';
        $query_filters_where = rtrim($query_filters_where, 'OR ').') ';

        return array('where' => $query_filters_where);
    }

    private static function getQuantityFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (count($filter_value) == 2 || empty($filter_value))
            return array();

        $query_filters_join = '';

        $query_filters = ' AND sav.quantity '.(!$filter_value[0] ? '<=' : '>').' 0 ';
        $query_filters_join = 'LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.id_product = p.id_product AND sav.id_shop = '.(int)Context::getContext()->shop->id.') ';

        return array('where' => $query_filters, 'join' => $query_filters_join);
    }

    private static function getManufacturerFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value))
            $query_filters = '';
        else
        {
            array_walk($filter_value, create_function('&$id_manufacturer', '$id_manufacturer = (int)$id_manufacturer;'));
            $query_filters = ' AND p.id_manufacturer IN ('.implode($filter_value, ',').')';
        }
        if ($ignore_join)
            return array('where' => $query_filters);
        else
            return array('where' => $query_filters, 'join' => 'LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.id_manufacturer = p.id_manufacturer) ');
    }

    private static function getConditionFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (count($filter_value) == 3 || empty($filter_value))
            return array();

        $query_filters = ' AND p.condition IN (';

        foreach ($filter_value as $cond)
            $query_filters .= '\''.$cond.'\',';
        $query_filters = rtrim($query_filters, ',').') ';

        return array('where' => $query_filters);
    }

    private static function getWeightFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (isset($filter_value) && $filter_value) {
            if ($filter_value[0] != 0 || $filter_value[1] != 0) {
                return array('where' => ' AND p.`weight` BETWEEN '.(float)($filter_value[0] - 0.001).' AND '.(float)($filter_value[1] + 0.001).' ');
            }
        }

        return array();
    }

    private static function getPriceFilterSubQuery($filter_value, $ignore_join = false)
    {
        $id_currency = (int)Context::getContext()->currency->id;

        if (isset($filter_value) && $filter_value)
        {
            $price_filter_query = '
			INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$id_currency.'
			AND psi.price_min <= '.(int)$filter_value[1].' AND psi.price_max >= '.(int)$filter_value[0].' AND psi.id_shop='.(int)Context::getContext()->shop->id.') ';
            return array('join' => $price_filter_query);
        }
        return array();
    }

    private static function getId_featureFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value))
            return array();
        $query_filters = ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'feature_product fp WHERE fp.id_product = p.id_product AND ';
        foreach ($filter_value as $filter_val)
            $query_filters .= 'fp.`id_feature_value` = '.(int)$filter_val.' OR ';
        $query_filters = rtrim($query_filters, 'OR ').') ';

        return array('where' => $query_filters);
    }

    private static function getId_attribute_groupFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value))
            return array();
        $query_filters = '
		AND EXISTS (SELECT *
		FROM `'._DB_PREFIX_.'product_attribute_combination` pac
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		WHERE pa.id_product = p.id_product AND ';

        foreach ($filter_value as $filter_val)
            $query_filters .= 'pac.`id_attribute` = '.(int)$filter_val.' OR ';
        $query_filters = rtrim($query_filters, 'OR ').') ';

        return array('where' => $query_filters);
    }

    private static function filterProductsByPrice($filter_value, $product_collection)
    {
        static $ps_layered_filter_price_usetax = null;
        static $ps_layered_filter_price_rounding = null;

        if (empty($filter_value))
            return $product_collection;

        if ($ps_layered_filter_price_usetax === null) {
            $ps_layered_filter_price_usetax = Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX');
        }

        if ($ps_layered_filter_price_rounding === null) {
            $ps_layered_filter_price_rounding = Configuration::get('PS_LAYERED_FILTER_PRICE_ROUNDING');
        }

        foreach ($product_collection as $key => $product)
        {
            if (isset($filter_value) && $filter_value && isset($product['price_min']) && isset($product['id_product'])
                && (($product['price_min'] < (int)$filter_value[0] && $product['price_max'] > (int)$filter_value[0])
                    || ($product['price_max'] > (int)$filter_value[1] && $product['price_min'] < (int)$filter_value[1])))
            {
                $price = Product::getPriceStatic($product['id_product'], $ps_layered_filter_price_usetax);
                if ($ps_layered_filter_price_rounding) {
                    $price = (int)$price;
                }
                if ($price < $filter_value[0] || $price > $filter_value[1]) {
                    unset($product_collection[$key]);
                }
            }
        }
        return $product_collection;
    }
    
    
}