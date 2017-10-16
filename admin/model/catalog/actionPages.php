<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 11.10.2017
 * Time: 15:45
 */

class ModelCatalogActionPages extends Model {

    public function addActPages($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "action_pages SET 
        link = '" . $this->db->escape($data['link']). "',
        no_index = " . (int)$data['no_index']. " 
        date_added = NOW()");

        $pages_id = $this->db->getLastId();

        foreach ($data['category_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "action_pages_desc SET 
            pages_id = '" . (int)$pages_id . "', 
            language_id = '" . (int)$language_id . "', 
            name = '" . $this->db->escape($value['name']) . "', 
            description = '" . $this->db->escape($value['description']) . "', 
            meta_title = '" . $this->db->escape($value['meta_title']) . "', 
            meta_description = '" . $this->db->escape($value['meta_description']) . "', 
            meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }

        $this->cache->delete('actionPages');

        return $pages_id;
    }

    public function editActPages($pages_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "action_pages SET 
        link = '" . $this->db->escape($data['link']). "',
        no_index = " . (int)$data['no_index']. "
        WHERE pages_id = '" . (int)$pages_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "action_pages_desc WHERE pages_id = '" . (int)$pages_id . "'");

        foreach ($data['category_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "action_pages_desc SET 
            pages_id = '" . (int)$pages_id . "', 
            language_id = '" . (int)$language_id . "', 
            name = '" . $this->db->escape($value['name']) . "', 
            description = '" . $this->db->escape($value['description']) . "', 
            meta_title = '" . $this->db->escape($value['meta_title']) . "', 
            meta_description = '" . $this->db->escape($value['meta_description']) . "', 
            meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
        }
        $this->cache->delete('actionPages');
    }

    public function getActPages($data) {
        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "action_pages ap
           LEFT JOIN " . DB_PREFIX . "action_pages_desc apd ON (ap.pages_id = apd.pages_id ) 
           WHERE apd.language_id = '" . (int)$this->config->get('config_language_id')."'";
        if (!empty($data['filter_name'])) {
            $sql .= " AND apd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
//        $sort_data = array(
//            'name',
//            'sort_order'
//        );
//
//        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
//            $sql .= " ORDER BY " . $data['sort'];
//        } else {
//            $sql .= " ORDER BY name";
//        }
//
//        if (isset($data['order']) && ($data['order'] == 'DESC')) {
//            $sql .= " DESC";
//        } else {
//            $sql .= " ASC";
//        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getActPage($pages_id) {
        $sql = "SELECT DISTINCT * FROM " . DB_PREFIX . "action_pages ap
           LEFT JOIN " . DB_PREFIX . "action_pages_desc apd ON (ap.pages_id = apd.pages_id ) 
           WHERE ap.pages_id = '" . (int)$pages_id . "' AND apd.language_id = '" . (int)$this->config->get('config_language_id')."'";

        $query = $this->db->query($sql);

        return $query->row;
    }
    public function deleteActPages($pages_id) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "action_pages WHERE pages_id = '" . (int)$pages_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "action_pages_desc WHERE pages_id = '" . (int)$pages_id . "'");

        $this->cache->delete('actionPages');
    }

    public function getActPagesDescriptions($pages_id) {
        $action_pages_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "action_pages_desc WHERE pages_id = '" . (int)$pages_id . "'");

        foreach ($query->rows as $result) {
            $action_pages_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'meta_title'       => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword'],
                'description'      => $result['description']
            );
        }

        return $action_pages_description_data;
    }
    public function getTotalActPages() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "action_pages");

        return $query->row['total'];
    }
}