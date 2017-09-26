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

		$this->response->setOutput($this->load->view('design/menus', $data));
	}
}