<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 05.09.2017
 * Time: 12:08
 */

class  ControllerProductReview extends Controller
{
    private $data;
    private $error = array();

    public function index()
    {
        if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
        } else {
            $product_id = 0;
        }
        if($product_id){
            return $this->product($product_id);
        }else{
            return $this->category();
        }

    }

    public function category()
    {
        $this->load->language('product/review');
        $this->load->language('product/product');

        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
            $category_id = (int)array_pop($parts);
        } else {
            $category_id = 0;
        }
        $this->data['review_status'] = $this->config->get('config_review_status');
        $this->data['text_titl'] = $this->language->get('text_title_review');
        $this->data['text_no_reviews'] = $this->language->get('text_no_reviews');
        //Otziv
        $this->load->model('catalog/review');
        $res_review = $this->model_catalog_review->getReviewsByCategoryId($category_id, 0, 4);
        foreach ($res_review as $result) {
            $this->data['reviews'][] = array(
                'product_id' => $result['product_id'],
                'product_name' => $result['name'],
                'product_price' => $result['price'],
                'product_image' => $result['image'],
                'author' => $result['author'],
                'text' => nl2br($result['text']),
                'rating' => (int)$result['rating'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
            );
        }

        return $this->load->view('product/reviews', $this->data);
    }
    public function product($product_id)
    {
        $this->load->language('product/review');
        $this->load->language('product/product');
        $this->data['review_status'] = $this->config->get('config_review_status');
        $this->data['text_titl'] = $this->language->get('text_title_review');
        $this->data['text_no_reviews'] = $this->language->get('text_no_reviews');
        //Otziv
        $this->load->model('catalog/review');
        $res_review = $this->model_catalog_review->getReviewsByProductId($product_id, 0, 4);
        foreach ($res_review as $result) {
            $this->data['reviews'][] = array(
                'product_id' => $result['product_id'],
                'product_name' => $result['name'],
                'product_price' => $result['price'],
                'product_image' => $result['image'],
                'author' => $result['author'],
                'text' => nl2br($result['text']),
                'rating' => (int)$result['rating'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
            );
        }

        return $this->load->view('product/reviews', $this->data);
    }

    public function add(){
       //$this->document->addScript('jquery.maskedinput.min.js');
        $this->document->addScript('/catalog/view/javascript/reviews.js');
        $this->load->language('product/review');
        $this->load->language('product/product');
        if (isset($this->request->get['product_id'])) {
            $this->data['product_id'] = (int)$this->request->get['product_id'];
        } else {
            $this->data['product_id'] = 0;
        }
        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
            $this->data['category_id'] = (int)array_pop($parts);
        } else {
            $this->data['category_id'] = 0;
        }

        //$token = (isset($this->session->data['user_token']))?$this->session->data['user_token']:'';
        //$this->data['action'] = $this->url->link('product/review/write', 'user_token=' . $token . '&type=module', true);
        $this->data['action'] = $this->url->link('product/review/write');

        $this->data['review_status'] = $this->config->get('config_review_status');
        $this->data['text_title_review_add'] = $this->language->get('text_title_review_add');
        $this->data['text_tip_name'] = $this->language->get('text_tip_name');
        $this->data['text_tip_tel'] = $this->language->get('text_tip_tel');
        $this->data['text_tip_email'] = $this->language->get('text_tip_email');
        $this->data['text_tip_area'] = $this->language->get('text_tip_area');
        $this->data['text_btn_name'] = $this->language->get('text_btn_name');

        return $this->load->view('product/reviews_add', $this->data);
    }
    public function write() {
        $this->load->language('product/product');

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
                $json['error'][] = ['inp' => "input[name='name']" , 'text' => $this->language->get('error_name')];
            }

            if ((utf8_strlen($this->request->post['phone']) < 5) || (utf8_strlen($this->request->post['phone']) > 15)) {
                $json['error'][] = ['inp' => "input[name='phone']" , 'text' => $this->language->get('error_name')];

            }

            if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
                $json['error'][] = ['inp' => "textarea[name='text']" , 'text' => $this->language->get('error_text')];
            }

            if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
                $json['error'][] = ['inp' => ".card-reviews__stars" , 'text' => $this->language->get('error_rating')];
            }

            // Captcha
            if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
                $captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

                if ($captcha) {
                    $json['error'][] = ['inp' => '' , 'text' => $captcha];
                }
            }

            if (!isset($json['error'])) {
                $this->load->model('catalog/review');

                $this->model_catalog_review->addReview($this->request->post['product_id'], $this->request->post);
                $data['return'] = true;
                $data['msg'] = $this->language->get('text_success');
                $data['review'] = $this->product($this->request->post['product_id']);
                $json['success'] = $data;
            }else{
                $data['return'] = true;
                $json['success'] = false;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}