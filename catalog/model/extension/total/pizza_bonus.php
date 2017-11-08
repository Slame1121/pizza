<?php
class ModelExtensionTotalPizzaBonus extends Model {
	public function getTotal($totals) {

		if(isset($this->session->data['guest']['used_points']) && $this->session->data['guest']['used_points'] > 0){
			$totals['totals'][] = [
				'code' => 'pizza_bonus',
				'value' => -$this->session->data['guest']['used_points'],
				'sort_order' => $this->config->get('total_pizza_bonus_sort_order'),
				'title' => 'Используемые бонусы'
			];
			$totals['total'] -= $this->session->data['guest']['used_points'];
		}
	}


}