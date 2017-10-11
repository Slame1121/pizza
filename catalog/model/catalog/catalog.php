<?php

class ModelCatalogCatalog extends Model
{

	public function getAttributes($data = array())
	{
		$sql = "SELECT a.*,ad.name, ag.filter_name as group_filter_name,
					(SELECT agd.name  FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group
					 FROM " . DB_PREFIX . "attribute a
					 INNER JOIN " . DB_PREFIX . "attribute_group ag ON ag.attribute_group_id = a.attribute_group_id
					 LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id)

					 WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		//$sql = "SELECT *, (SELECT agd.name FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		//print_r($sql);
		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_attribute_group_id'])) {
			$sql .= " AND a.attribute_group_id = '" . $this->db->escape($data['filter_attribute_group_id']) . "'";
		}

		$sort_data = array(
			'ad.name',
			'attribute_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY attribute_group, ad.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$result = $this->db->query($sql)->rows;

		$attributes = [];
		foreach($result as $attr){
			if(!isset($attributes[$attr['attribute_group_id']])){
				$attributes[$attr['attribute_group_id']] = [
					'name' => $attr['attribute_group'],
					'id' => $attr['attribute_group_id'],
					'group_filter_name' => $attr['group_filter_name'],
					'attr' => []
				];
			}
			$attributes[$attr['attribute_group_id']]['attr'][] = $attr;

		}

		return $attributes;
	}


	public function getAllAtributes(){
		$sql = "SELECT *, (SELECT agd.name FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$result = $this->db->query($sql)->rows;

		$attributes = [];
		foreach($result as $attr){

			$attributes[$attr['attribute_id']] = $attr;
		}

		return $attributes;
	}
}