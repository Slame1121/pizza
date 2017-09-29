<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function _index($setting) {

		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {

				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
                        'prod_type'   => (int)$product_info['prod_type'],
                        'label_text'  => $this->language->get('text_hit'.(int)$product_info['prod_type']),
						'tax'         => $tax,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}

	public function index($setting){
        $this->load->language('product/category');
        $this->load->language('extension/module/featured');

        $this->load->model('catalog/product');
        $this->load->model('catalog/catalog');
        $this->load->model('tool/image');
        $cat = isset($this->request->get['path'])?$this->request->get['path']:0;
        $tek_product_id = isset($this->request->get['product_id'])?$this->request->get['product_id']:0;

        $data['products'] = array();
        $data['title'] = $setting['name'];
        if (!$setting['limit']) {
            $setting['limit'] = 4;
        }
        $products = $this->model_catalog_product->getRandProducts((int)$cat, (int)$setting['limit'],(int)$tek_product_id);
        //var_dump($products);
        if ($products) {
            foreach ($products as $product) {
                $product_info = $this->model_catalog_product->getProduct($product);
                //var_dump($products);die;
                if ($product_info) {
                    if ($product_info['image']) {
                        $image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
                    } else {
                        $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                    }

                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $price = false;
                    }

                    if ((float)$product_info['special']) {
                        $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                        $special = false;
                    }

                    if ($this->config->get('config_tax')) {
                        $tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
                    } else {
                        $tax = false;
                    }

                    if ($this->config->get('config_review_status')) {
                        $rating = $product_info['rating'];
                    } else {
                        $rating = false;
                    }
                    $options = [];
                    $opt_val=[];
                    foreach ($this->model_catalog_product->getProductOptions($product_info['product_id']) as $option) {
                        $product_option_value_data = array();

                        foreach ($option['product_option_value'] as $option_value) {
                            if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                                    $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                                } else {
                                    $price = false;
                                }

                                $product_option_value_data[] = array(
                                    'product_option_value_id' => $option_value['product_option_value_id'],
                                    'option_value_id'         => $option_value['option_value_id'],
                                    'name'                    => $option_value['name'],
                                    'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                    'price'                   => $price,
                                    'price_prefix'            => $option_value['price_prefix']
                                );
                                $opt_val[] = array('prod_val'=>$option_value['product_option_value_id'],'opt_val'=>$option['product_option_id']);
                            }
                        }

                        $options[] = array(
                            'product_option_id'    => $option['product_option_id'],
                            'product_option_value' => $product_option_value_data,
                            'option_id'            => $option['option_id'],
                            'name'                 => $option['name'],
                            'type'                 => $option['type'],
                            'value'                => $option['value'],
                            'required'             => $option['required']
                        );
                    }

                    $indigrients_groups = $this->model_catalog_product->getProductAttributes($product_info['product_id']);
                    $sostav = '';
                    if($indigrients_groups){
                        foreach ($indigrients_groups as $s){
                            if(isset($s["attribute"])){
                                foreach ($s["attribute"] as $it){
                                    if($sostav == ''){
                                        $sostav = $it["name"];
                                    }else{
                                        $sostav .= ', '.$it["name"];
                                    }
                                }
                            }

                        }
                    }

                    $data['products'][] = array(
                        'product_id'  => $product_info['product_id'],
                        'thumb'       => $image,
                        'options'     => $options,
                        'optV'        => $opt_val,
                        'indegrients' => $indigrients_groups,
                        'sostav'      => $sostav,
                        'name'        => $product_info['name'],
                        'prod_type'   => (int)$product_info['prod_type'],
                        'label_text'  => $this->language->get('text_hit'.(int)$product_info['prod_type']),
                        'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                        'price'       => $price,
                        'special'     => $special,
                        'tax'         => $tax,
                        'rating'      => $rating,
                        'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
                    );
                }
            }
        }
        $data['cont'] = false;
        //var_dump($_SERVER);die;
       if(@$this->request->get['route'] == "common/home" || $_SERVER["REQUEST_URI"] == '/'){
           $data['cont'] = true;
       }
        //var_dump($data['products']);die;
        if ($data['products']) {
            return $this->load->view('product/smal_product', $data);
        }
    }
}