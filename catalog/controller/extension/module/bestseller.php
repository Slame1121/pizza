<?php
class ControllerExtensionModuleBestSeller extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/bestseller');
		$this->load->language('product/product');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();
        if (!$setting['limit']) {
            $setting['limit'] = 3;
        }
		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}


                $options = [];
                $opt_val=[];
                foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
                    $product_option_value_data = array();

                    foreach ($option['product_option_value'] as $option_value) {
                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                            if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                                $price = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                            } else {
                                $price = false;
                            }

                            $product_option_value_data[] = array(
                                'product_option_value_id' => $option_value['product_option_value_id'],
                                'option_value_id'         => $option_value['option_value_id'],
                                'name'                    => $option_value['name'],
                                'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                'price'                   => $price,
								'weight'                   => $option_value['weight'],
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

                $indigrients_groups = $this->model_catalog_product->getProductAttributes($result['product_id']);
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
                    'product_id'  => $result['product_id'],
                    'thumb'       => $image,
                    'options'     => $options,
                    'optV'        => $opt_val,
                    'indegrients' => $indigrients_groups,
                    'sostav'      => $sostav,
                    'name'        => $result['name'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                    'price'       => $price,
                    'special'     => $special,
                    'tax'         => $tax,
                    'rating'      => $rating,
                    'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                );
			}
            $data['text_hit'] = $this->language->get('text_hit');
            $data['cont'] = false;
            //var_dump($_SERVER);die;
            if(@$this->request->get['route'] == "common/home" || $_SERVER["REQUEST_URI"] == '/'){
                $data['cont'] = true;
            }
            //var_dump($data['products']);die;
            if ($data['products']) {
                return $this->load->view('product/smal_product', $data);
            }
			//return $this->load->view('extension/module/bestseller', $data);
		}
	}
}
