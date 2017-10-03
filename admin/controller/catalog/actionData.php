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
        $this->load->language('catalog/actData');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addStyle('/catalog/view/theme/default/stylesheet/bootstrap-datepicker3.min.css');
       // $this->document->addScript('/catalog/view/javascript/jquery-migrate-3.0.1.min.js');
        $this->document->addScript('/catalog/view/javascript/bootstrap-datepicker.js');
        $data['langs'] = 'ru';
        if($this->language->data["code"] == "uk"){
            $data['langs'] = 'uk';
            $this->document->addScript('/catalog/view/javascript/locales/bootstrap-datepicker.uk.js');
        }else{
            $this->document->addScript('/catalog/view/javascript/locales/bootstrap-datepicker.ru.js');
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/actionData', 'user_token=' . $this->session->data['user_token'], true)
        );

        $this->load->model('catalog/actionData');

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

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_catalog_actionData->editData($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('catalog/actionData', 'user_token=' . $this->session->data['user_token'], true));
        }
        $data['actData'] = $this->model_catalog_actionData->getActData();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/actData', $data));
    }


}