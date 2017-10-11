<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 11.10.2017
 * Time: 15:42
 */

class ControllerCatalogActionPages extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('catalog/actPages');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/actionPages');
        $this->getList();
    }
    public function add() {
        $this->load->language('catalog/actPages');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/actionPages');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_actionPages->addActPages($this->request->post);

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

            $this->response->redirect($this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }
    public function edit() {
        $this->load->language('catalog/actPages');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/actionPages');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_actionPages->editActPages($this->request->get['pages_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }
    public function delete() {
        $this->load->language('catalog/actPages');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/actionPages');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $category_id) {
                $this->model_catalog_actionPages->deleteActPages($category_id);
            }

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

            $this->response->redirect($this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }


    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
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
            'href' => $this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['add'] = $this->url->link('catalog/actionPages/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('catalog/actionPages/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['actionPages'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $category_total = $this->model_catalog_actionPages->getTotalActPages();

        $results = $this->model_catalog_actionPages->getActPages($filter_data);

        foreach ($results as $result) {
            $data['actionPages'][] = array(
                'pages_id' => $result['pages_id'],
                'name'        => $result['name'],
                'edit'        => $this->url->link('catalog/actionPages/edit', 'user_token=' . $this->session->data['user_token'] . '&pages_id=' . $result['pages_id'] . $url, true),
                'delete'      => $this->url->link('catalog/actionPages/delete', 'user_token=' . $this->session->data['user_token'] . '&pages_id=' . $result['pages_id'] . $url, true)
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

//        if ($order == 'ASC') {
//            $url .= '&order=DESC';
//        } else {
//            $url .= '&order=ASC';
//        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_sort_order'] = $this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $category_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/action_pages_list', $data));
    }
    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['pages_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
        }

        if (isset($this->error['link'])) {
            $data['error_link'] = $this->error['link'];
        } else {
            $data['error_link'] = '';
        }
        $data['entry_link'] = $this->language->get('entry_link');
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
            'href' => $this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['pages_id'])) {
            $data['action'] = $this->url->link('catalog/actionPages/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('catalog/actionPages/edit', 'user_token=' . $this->session->data['user_token'] . '&pages_id=' . $this->request->get['pages_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('catalog/actionPages', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['pages_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $category_info = $this->model_catalog_actionPages->getActPage($this->request->get['pages_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['link'])) {
            $data['link'] = $this->request->post['link'];
        } elseif (!empty($category_info)) {
            $data['link'] = $category_info['link'];
        } else {
            $data['link'] = 0;
        }

        if (isset($this->request->post['category_description'])) {
            $data['category_description'] = $this->request->post['category_description'];
        } elseif (isset($this->request->get['pages_id'])) {
            $data['category_description'] = $this->model_catalog_actionPages->getActPagesDescriptions($this->request->get['pages_id']);
        } else {
            $data['category_description'] = array();
        }



        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/action_pages_form', $data));
    }
    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/actionPages')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['category_description'] as $language_id => $value) {
            if($value != 'link'){
                if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
                    $this->error['name'][$language_id] = $this->language->get('error_name');
                }

                if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
                    $this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
                }
            }

        }

        if ((utf8_strlen($this->request->post['link']) < 1) || (utf8_strlen($this->request->post['link']) > 255)) {
            $this->error['link'] = $this->language->get('error_link');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'catalog/actionPages')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}