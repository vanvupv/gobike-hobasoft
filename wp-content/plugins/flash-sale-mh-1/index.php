<?php // Silence is golden

function getFlashSalePrice($product_id){
		global $wpdb;
		$sql = "SELECT flash_sale_price, sale_price, product_id from fsmh_products as t1 INNER JOIN fsmh_campaigns as t2 on t2.id = t1.campaign_id WHERE NOW() BETWEEN t2.time_begin AND t2.time_end AND t1.product_id = $product_id;";
		$record = $wpdb->get_row($sql);
		if($record)
			return $record->flash_sale_price ? $record->flash_sale_price : $record->sale_price;

		return false;
}