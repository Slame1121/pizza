<?php
class ModelDesignMenu extends Model {

	public function getMenuItems($tag){

		$lang = $this->config->get('config_language_id');


		$query = $this->db->query("SELECT mi.*
		 FROM `" . DB_PREFIX . "menu_items` as mi
		 INNER JOIN `" . DB_PREFIX . "menu` as m ON mi.menu_id = m.id
		 WHERE m.tag = '" . $tag."' and mi.language_id=".$lang );

		return $query->rows;
	}
}
