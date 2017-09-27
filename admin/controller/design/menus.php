<?php
class ControllerDesignMenus extends Controller
{


	public function index()
	{
		$this->document->setTitle('Меню на сайте');

		$this->load->model('design/menu');

		$this->getList();
	}

	protected function getList() {
		$data = [];

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
			'text' => 'Меню',
			'href' => $this->url->link('design/menus', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		$menus = $this->model_design_menu->getMenus();

		foreach($menus as $key => $menu){
			$menus[$key]['edit'] = $this->url->link('design/menus/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id='.$menu['id'], true);
		}

		$data['menus'] = $menus;

     	$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');


		$this->response->setOutput($this->load->view('design/menu', $data));
	}


    public function cmp($a, $b) {
		if ($a['sort']== $b['sort'] )
			return 0;
		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
	}

	public function edit(){
		$this->load->model('design/menu');
		$this->load->model('localisation/language');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$languages = $this->model_localisation_language->getLanguages();
			$menu = [];
			$menu_id = 0;
			if (isset($this->request->get['menu_id'])) {
				$menu_id = (int)$this->request->get['menu_id'];
			}
			foreach($this->request->post['link'] as $key => $link){
				foreach($languages as $langs){
					$menu[] = [
						'id' => $key,
						'link' => $link,
						'title' => $this->request->post['titles'][$langs['language_id']][$key],
						'sort' => $this->request->post['sort'][$key],
						'language_id' => $langs['language_id']
					];
				}

			}

			$this->model_design_menu->addItems($menu_id, $menu);
		}
		$this->document->setTitle('Редактировать меню');



		$this->getForm();
	}


	public function getForm(){
		$data= [];



		$data['languages'] = $this->model_localisation_language->getLanguages();

		$menu_id = 0;
		if (isset($this->request->get['menu_id'])) {
			$menu_id = (int)$this->request->get['menu_id'];
		}


		$data['action'] = $this->url->link('design/menus/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id='.$menu_id, true);

		$menu_items = $this->model_design_menu->getMenuItems($menu_id);
		$data['menu_items'] = [];
		foreach($menu_items as $item){
			if(!isset($data['menu_items'][$item['menu_unit_id']])){
				$data['menu_items'][$item['menu_unit_id']] = [
					'menu_unit_id' => $item['menu_unit_id'],
					'titles' => [
						$item['language_id'] => $item['title']
					],
					'sort' => $item['sort'],
					'link' => $item['link'],
				];
			}else{
				$data['menu_items'][$item['menu_unit_id']]['titles'][$item['language_id']] =  $item['title'];
			}

		}
		uasort($data['menu_items'], ['\ControllerDesignMenus','cmp']);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/menu_edit', $data));
	}
}