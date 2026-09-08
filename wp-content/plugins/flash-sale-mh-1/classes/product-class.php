<?php 

	class FSMH_Product extends FSMH_APP {

	    static $table = 'fsmh_products';

	    static function getFlashSaleInfoOfProduct($filters){
            $db = self::$db;
            if($filters){
            	if(isset($filters['product_id']))
		    		$db->where('product_id', $filters['product_id']);
		    	if(isset($filters['campaign_id']))
		    		$db->where('campaign_id', $filters['campaign_id']);
		    		
		    	return $db->getOne(self::$table);
            }
            return;
	    }

        static function getWooProductById( $id ) {
            global $wpdb;
            $id          = sanitize_text_field( $id );
            $posts_table = $wpdb->prefix . 'posts';

            $sql = $wpdb->prepare(
                "SELECT p.ID FROM {$posts_table} AS p
                 INNER JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id
                 WHERE p.post_parent = 0
                   AND ( ( m.meta_key = '_sku' AND m.meta_value = %s ) OR p.ID = %d )
                 LIMIT 1",
                $id,
                intval( $id )
            );
            $result = $wpdb->get_row( $sql, ARRAY_A );
            if ( $result ) {
                $product = MH_Get_Product_Array( $result['ID'] );
                if ( $product ) return $product;
            }
            return false;
        }

		static function getProduct($filters){
			$db = self::$db;
			if(isset($filters['campaign_id']))
				$db->where('campaign_id', $filters['campaign_id']);
			if(isset($filters['product_id']))
				$db->where('product_id', $filters['product_id']);
			if(isset($filters['id']))
				$db->where('id', $filters['id']);
			return $db->getOne(self::$table);
		}

		static function update($id, $data){
            $db = self::$db;
			$db->where('id', $id);
			$product = $db->getOne(self::$table);
			if($product){
				$db->where('id', $id);
				$result = $db->update(self::$table, $data);
				return MH_Hanlde_Result_DB($result, $db);
			}
		}

	    static function searchWooProducts($filters){
            global $wpdb;

            $search      = isset( $filters['search'] ) ? sanitize_text_field( $filters['search'] ) : '';
            $search_like = '%' . $wpdb->esc_like( strtolower( $search ) ) . '%';
            $post_table  = $wpdb->prefix . 'posts';
            $hbfs_p      = $wpdb->prefix . 'hbfs_products';
            $hbfs_c      = $wpdb->prefix . 'hbfs_campaigns';

            // Giới hạn 30 kết quả — đủ cho dropdown tìm kiếm trong admin
            $ids = $wpdb->get_results( $wpdb->prepare(
                "SELECT ID FROM {$post_table}
                 WHERE post_type = 'product'
                   AND post_parent = 0
                   AND post_status = 'publish'
                   AND LOWER(post_title) LIKE %s
                 ORDER BY post_title ASC
                 LIMIT 30",
                $search_like
            ), ARRAY_A );

            // Lấy danh sách campaign đang dùng từng product_id (JOIN 1 lần, group theo product_id)
            $all_ids = array_column( (array) $ids, 'ID' );
            $campaign_map = [];
            if ( ! empty( $all_ids ) ) {
                $placeholders = implode( ',', array_fill( 0, count( $all_ids ), '%d' ) );
                $rows = $wpdb->get_results( $wpdb->prepare(
                    "SELECT hp.product_id, hc.id AS campaign_id, hc.name AS campaign_name, hc.time_begin, hc.time_end
                     FROM {$hbfs_p} hp
                     INNER JOIN {$hbfs_c} hc ON hc.id = hp.campaign_id
                     WHERE hp.product_id IN ({$placeholders})
                       AND hp.parent_id = 0
                     ORDER BY hc.time_begin ASC",
                    ...$all_ids
                ), ARRAY_A );
                foreach ( (array) $rows as $r ) {
                    $campaign_map[ $r['product_id'] ][] = [
                        'id'         => (int) $r['campaign_id'],
                        'name'       => $r['campaign_name'],
                        'time_begin' => $r['time_begin'],
                        'time_end'   => $r['time_end'],
                    ];
                }
            }

            $products = [];
            foreach ( (array) $ids as $row ) {
                $product = MH_Get_Product_Array( $row['ID'] );
                if ( $product ) {
                    $product['campaigns_using'] = $campaign_map[ $row['ID'] ] ?? [];
                    $products[] = $product;
                }
            }
            return [
                'data'       => $products,
                'pagination' => [ 'total' => count( $products ) ],
            ];
	    }


	    static function getConfig($config_name){
	    	global $wpdb;
	    	// Fix: dùng self::$table thay vì biến $table chưa được định nghĩa trong scope này
	    	// Fix: get_rows() không tồn tại trong $wpdb, dùng get_results() đúng
	    	$table = $wpdb->prefix . ltrim( self::$table, $wpdb->prefix );
	    	$sql   = $wpdb->prepare( "SELECT * FROM {$table} WHERE config_name = %s", $config_name );
	    	$row   = $wpdb->get_results( $sql );
	    	return $row;
	    }

	    static function setConfig($config_name, $config_value){
			$db = self::$db; 
			if(!$config_name)
				return;
			$db->where('config_name', $config_name);
			$config = $db->getOne(self::$table);
			$data = [
					'config_name' => $config_name,
					'config_value' => $config_value,
			];
			if($config) {
				$db->where('config_name', $config_name);
				$db->update(self::$table, $data);
			}
			else{
				$db->insert(self::$table, $data);
			
			}
		
	    }


	    


	}

// debug(FSMH_Campaign::getCampaigns())
?>
