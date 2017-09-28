<?php
class ModelDesignMenu extends Model {

	public function getMenus(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "menu`");

		return $query->rows;
	}

	public function getMenuItems($menu_id){


		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "menu_items` WHERE menu_id = " . (int) $menu_id );
		return $query->rows;
	}

	public function addItems($menu_id, $items){
		$values = [];
		$sql = 'DELETE FROM `' . DB_PREFIX . 'menu_items` WHERE menu_id = '. $menu_id;

		//remove query
		$this->db->query($sql);
		foreach($items as $item){
			$values[] = '("'.$item['link'].'", "'.$item['title'].'", '.(int)$item['sort'].', '.$item['language_id'].', '.$menu_id.', '.$item['id'].')';
		}

		if($values){
			$sql = 'INSERT INTO `' . DB_PREFIX . 'menu_items` (link, title, sort, language_id, menu_id, menu_unit_id) VALUES '. implode(', ', $values);

			$this->db->query($sql);

			$sql = 'DELETE FROM `' . DB_PREFIX . 'menu_items` WHERE menu_id = '. $menu_id. ' and menu_unit_id not IN ('.implode(',', array_unique(array_column($items, 'id'))).')';

		}



	}
}