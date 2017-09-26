<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 26.09.2017
 * Time: 15:35
 */
class ModelCatalogQuestions extends Model {
    public function addQuestions($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "questions SET 
                        sort_order = '" . (int)$data['sort_order'] . "', 
                        bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', 
                        status = '" . (int)$data['status'] . "'");

        $questions_id = $this->db->getLastId();

        foreach ($data['questions_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "questions_description SET 
                                questions_id = '" . (int)$questions_id . "', 
                                language_id = '" . (int)$language_id . "', 
                                title = '" . $this->db->escape($value['title']) . "', 
                                description = '" . $this->db->escape($value['description']) . "'");
        }

        if (isset($data['questions_store'])) {
            foreach ($data['questions_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "questions_to_store SET questions_id = '" . (int)$questions_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->cache->delete('questions');

        return $questions_id;
    }

    public function editQuestions($questions_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "questions SET 
                            sort_order = '" . (int)$data['sort_order'] . "', 
                            bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', 
                            status = '" . (int)$data['status'] . "' 
                            WHERE questions_id = '" . (int)$questions_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "questions_description WHERE questions_id = '" . (int)$questions_id . "'");

        foreach ($data['questions_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "questions_description SET 
                                questions_id = '" . (int)$questions_id . "', 
                                language_id = '" . (int)$language_id . "', 
                                title = '" . $this->db->escape($value['title']) . "', 
                                description = '" . $this->db->escape($value['description']) . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "questions_to_store WHERE questions_id = '" . (int)$questions_id . "'");

        if (isset($data['questions_store'])) {
            foreach ($data['questions_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "questions_to_store SET questions_id = '" . (int)$questions_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->cache->delete('questions');
    }

    public function deleteQuestions($questions_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "questions` WHERE questions_id = '" . (int)$questions_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "questions_description` WHERE questions_id = '" . (int)$questions_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "questions_to_store` WHERE questions_id = '" . (int)$questions_id . "'");
        $this->cache->delete('questions');
    }

    public function getQuestion($questions_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "questions WHERE questions_id = '" . (int)$questions_id . "'");

        return $query->row;
    }

    public function getQuestions($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "questions i 
                    LEFT JOIN " . DB_PREFIX . "questions_description id ON (i.questions_id = id.questions_id) 
                    WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

            $sort_data = array(
                'id.title',
                'i.sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY id.title";
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

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $questions_data = $this->cache->get('questions.' . (int)$this->config->get('config_language_id'));

            if (!$questions_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "questions i LEFT JOIN " . DB_PREFIX . "questions_description id ON (i.questions_id = id.questions_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");

                $questions_data = $query->rows;

                $this->cache->set('questions.' . (int)$this->config->get('config_language_id'), $questions_data);
            }

            return $questions_data;
        }
    }

    public function getQuestionsDescriptions($questions_id) {
        $questions_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "questions_description WHERE questions_id = '" . (int)$questions_id . "'");

        foreach ($query->rows as $result) {
            $questions_description_data[$result['language_id']] = array(
                'title'            => $result['title'],
                'description'      => $result['description']
            );
        }

        return $questions_description_data;
    }

    public function getQuestionsStores($questions_id) {
        $questions_store_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "questions_to_store WHERE questions_id = '" . (int)$questions_id . "'");

        foreach ($query->rows as $result) {
            $questions_store_data[] = $result['store_id'];
        }

        return $questions_store_data;
    }

    public function getTotalQuestions() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "questions");

        return $query->row['total'];
    }

    public function getTotalQuestionsByLayoutId($layout_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "questions_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

        return $query->row['total'];
    }
}