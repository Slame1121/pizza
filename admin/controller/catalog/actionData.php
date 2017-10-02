<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 02.10.2017
 * Time: 17:07
 */

class ControllerCatalogActionData extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('catalog/questions');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/questions');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/questions');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/questions');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_questions->addQuestions($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/questions');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/questions');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_questions->editQuestions($this->request->get['questions_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id.title';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('catalog/questions/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('catalog/questions/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['questions'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $questions_total = $this->model_catalog_questions->getTotalQuestions();

        $results = $this->model_catalog_questions->getQuestions($filter_data);

        foreach ($results as $result) {
            $data['questions'][] = array(
                'questions_id' => $result['questions_id'],
                'title'          => $result['title'],
                'sort_order'     => $result['sort_order'],
                'edit'           => $this->url->link('catalog/questions/edit', 'user_token=' . $this->session->data['user_token'] . '&questions_id=' . $result['questions_id'] . $url, true)
            );
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_title'] = $this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . '&sort=id.title' . $url, true);
        $data['sort_sort_order'] = $this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $questions_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($questions_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($questions_total - $this->config->get('config_limit_admin'))) ? $questions_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $questions_total, ceil($questions_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/questions_list', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['questions_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }

        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = array();
        }

//        if (isset($this->error['meta_title'])) {
//            $data['error_meta_title'] = $this->error['meta_title'];
//        } else {
//            $data['error_meta_title'] = array();
//        }
//
//        if (isset($this->error['keyword'])) {
//            $data['error_keyword'] = $this->error['keyword'];
//        } else {
//            $data['error_keyword'] = '';
//        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['questions_id'])) {
            $data['action'] = $this->url->link('catalog/questions/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('catalog/questions/edit', 'user_token=' . $this->session->data['user_token'] . '&questions_id=' . $this->request->get['questions_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('catalog/questions', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['questions_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $questions_info = $this->model_catalog_questions->getQuestion($this->request->get['questions_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['questions_description'])) {
            $data['questions_description'] = $this->request->post['questions_description'];
        } elseif (isset($this->request->get['questions_id'])) {
            $data['questions_description'] = $this->model_catalog_questions->getQuestionsDescriptions($this->request->get['questions_id']);
        } else {
            $data['questions_description'] = array();
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        if (isset($this->request->post['questions_store'])) {
            $data['questions_store'] = $this->request->post['questions_store'];
        } elseif (isset($this->request->get['questions_id'])) {
            $data['questions_store'] = $this->model_catalog_questions->getQuestionsStores($this->request->get['questions_id']);
        } else {
            $data['questions_store'] = array(0);
        }

        if (isset($this->request->post['bottom'])) {
            $data['bottom'] = $this->request->post['bottom'];
        } elseif (!empty($questions_info) && isset($questions_info['bottom'])) {
            $data['bottom'] = $questions_info['bottom'];
        } else {
            $data['bottom'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($questions_info) && isset($questions_info['status'])) {
            $data['status'] = $questions_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($questions_info) && isset($questions_info['sort_order'])) {
            $data['sort_order'] = $questions_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

//        if (isset($this->request->post['questions_seo_url'])) {
//            $data['questions_seo_url'] = $this->request->post['questions_seo_url'];
//        } elseif (isset($this->request->get['questions_id'])) {
//            $data['questions_seo_url'] = $this->model_catalog_questions->getQuestionsSeoUrls($this->request->get['questions_id']);
//        } else {
//            $data['questions_seo_url'] = array();
//        }

//        if (isset($this->request->post['questions_layout'])) {
//            $data['questions_layout'] = $this->request->post['questions_layout'];
//        } elseif (isset($this->request->get['questions_id'])) {
//            $data['questions_layout'] = $this->model_catalog_questions->getQuestionsLayouts($this->request->get['questions_id']);
//        } else {
//            $data['questions_layout'] = array();
//        }

//        $this->load->model('design/layout');
//
//        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/questions_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/questions')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['questions_description'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 64)) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }

            if (utf8_strlen($value['description']) < 3) {
                $this->error['description'][$language_id] = $this->language->get('error_description');
            }

//            if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
//                $this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
//            }
        }

//        if ($this->request->post['questions_seo_url']) {
//            $this->load->model('design/seo_url');
//
//            foreach ($this->request->post['questions_seo_url'] as $store_id => $language) {
//                foreach ($language as $language_id => $keyword) {
//                    if (!empty($keyword)) {
//                        if (count(array_keys($language, $keyword)) > 1) {
//                            $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
//                        }
//
//                        $seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
//
//                        foreach ($seo_urls as $seo_url) {
//                            if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['questions_id']) || ($seo_url['query'] != 'questions_id=' . $this->request->get['questions_id']))) {
//                                $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
//                            }
//                        }
//                    }
//                }
//            }
//        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'catalog/questions')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('setting/store');

        foreach ($this->request->post['selected'] as $questions_id) {
            if ($this->config->get('config_account_id') == $questions_id) {
                $this->error['warning'] = $this->language->get('error_account');
            }

            if ($this->config->get('config_checkout_id') == $questions_id) {
                $this->error['warning'] = $this->language->get('error_checkout');
            }

            if ($this->config->get('config_affiliate_id') == $questions_id) {
                $this->error['warning'] = $this->language->get('error_affiliate');
            }

            if ($this->config->get('config_return_id') == $questions_id) {
                $this->error['warning'] = $this->language->get('error_return');
            }

            $store_total = $this->model_setting_store->getTotalStoresByQuestionsId($questions_id);

            if ($store_total) {
                $this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
            }
        }

        return !$this->error;
    }
}