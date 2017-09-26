<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 26.09.2017
 * Time: 14:22
 */

class ControllerCommonPopularQuestions extends Controller {
    public function index() {
        $this->load->language('information/questions');
        $this->load->model('catalog/questions');
        $questions_info = $this->model_catalog_questions->getQuestions();
        $data['questions_info'] = false;
        if($questions_info){
            foreach ($questions_info as $q){
                $data['questions_info'][] = [
                    'questions_id' => $q['questions_id'],
                    'bottom' => $q['bottom'],
                    'sort_order' => $q['sort_order'],
                    'status' => $q['status'],
                    'language_id' => $q['language_id'],
                    'title' => $q['title'],
                    'description' => html_entity_decode($q['description'], ENT_QUOTES, 'UTF-8'),
                    'store_id' => $q['store_id']
                ];
            }
        }

        $data['text_h1'] = $this->language->get('text_h1');
        $data['not_questions'] = $this->language->get('not_questions');

        return $this->load->view('common/questions', $data);
    }

}