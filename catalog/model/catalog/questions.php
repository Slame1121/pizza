<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 26.09.2017
 * Time: 14:55
 */

class ModelCatalogQuestions extends Model {

    public function getQuestion($questions_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

        return $query->row;
    }

    public function getQuestions() {
        $sql = "SELECT * FROM " . DB_PREFIX . "questions q 
                LEFT JOIN " . DB_PREFIX . "questions_description qd ON (q.questions_id = qd.questions_id) 
                LEFT JOIN " . DB_PREFIX . "questions_to_store q2s ON (q.questions_id = q2s.questions_id) 
                WHERE qd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND 
                q2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND 
                q.status = '1' 
                ORDER BY q.sort_order, LCASE(qd.title) ASC";
        $query = $this->db->query($sql);

        return $query->rows;
    }

}