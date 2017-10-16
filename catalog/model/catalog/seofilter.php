<?php
class ModelCatalogSeofilter extends Model {
	public function getSeFilter($filter) {
		$sql = 'SELECT ap.link, apd.* FROM '.DB_PREFIX.'action_pages ap
				INNER JOIN '.DB_PREFIX.'action_pages_desc apd ON apd.pages_id = ap.pages_id
				WHERE ap.link = "'. $filter .'"';
		$query = $this->db->query($sql);
		return $query->row;
	}
}
?>