<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 03.10.2017
 * Time: 10:00
 */
class ModelCatalogActionData extends Model {

    public function editData($data) {
        if(isset($data["dataAct"])){
            $this->db->query("DELETE FROM `" . DB_PREFIX . "action_data`");
            foreach ($data["dataAct"] as $n){
                $this->db->query("INSERT INTO " . DB_PREFIX . "action_data SET dataAct = '" . strtotime($n) . "'");
            }

            $this->cache->delete('actionData');

            return true;
        }
        return false;
    }

    public function getActData() {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "action_data WHERE 1");
        $ret = false;
        if($query->rows){
            foreach ($query->rows as $r){
                $ret[] =  date( "d.m.Y", $r["dataAct"] );
            }
        }
        return $ret;
    }

    public function findActData($find) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "action_data WHERE dataAct = '" . strtotime($find) . "'");
        if(isset($query->row["dataAct"])){
            return true;
        }
        return false;
    }

}