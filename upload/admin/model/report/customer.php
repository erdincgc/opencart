<?php
class ModelReportCustomer extends Model {
	public function getOrders($data = array()) { 
		$sql = "SELECT r.customer_id, r.customer, r.email, r.customer_group, r.status, COUNT(r.order_id) AS orders, SUM(r.products) AS products, SUM(r.total) AS total FROM (SELECT o.order_id, c.customer_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, o.email, cg.name AS customer_group, c.status, (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, o.total FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "customer` c ON (o.customer_id > '0' AND o.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id)";
		
		if (isset($data['filter_order_status_id']) && $data['filter_order_status_id']) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
				
		if (isset($data['filter_date_start']) && $data['filter_date_start']) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (isset($data['filter_date_end']) && $data['filter_date_end']) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		$sql .= ") r GROUP BY r.customer_id ORDER BY total DESC";
				
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

	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(DISTINCT o.customer_id) AS total FROM `" . DB_PREFIX . "order` o WHERE o.customer_id > '0'";
		
		if (isset($data['filter_order_status_id']) && $data['filter_order_status_id']) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}
						
		if (isset($data['filter_date_start']) && $data['filter_date_start']) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (isset($data['filter_date_end']) && $data['filter_date_end']) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
						
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function getRewardPoints($data = array()) { 
		$sql = "SELECT cr.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cg.name AS customer_group, c.status, SUM(cr.points) AS points, COUNT(o.order_id) AS orders, SUM(o.total) AS total FROM " . DB_PREFIX . "customer_reward cr LEFT JOIN `" . DB_PREFIX . "customer` c ON (cr.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (cr.order_id = o.order_id)";
		
		$implode = array();
		
		if (isset($data['filter_date_start']) && $data['filter_date_start']) {
			$implode[] = "DATE(cr.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (isset($data['filter_date_end']) && $data['filter_date_end']) {
			$implode[] = "DATE(cr.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
				
		$sql .= " GROUP BY cr.customer_id ORDER BY points DESC";
				
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

	public function getTotalRewardPoints() {
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_reward`";
		
		$implode = array();
		
		if (isset($data['filter_date_start']) && $data['filter_date_start']) {
			$implode[] = "DATE(cr.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (isset($data['filter_date_end']) && $data['filter_date_end']) {
			$implode[] = "DATE(cr.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
				
		$sql .= " GROUP BY customer_id";
				
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
		
		return $query->row['total'];
	}
	
	public function getCredit($data = array()) { 
		$sql = "SELECT cr.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cg.name AS customer_group, c.status, SUM(cr.points) AS points, COUNT(o.order_id) AS orders, SUM(o.total) AS total FROM " . DB_PREFIX . "customer_reward cr LEFT JOIN `" . DB_PREFIX . "customer` c ON (cr.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group cg ON (c.customer_group_id = cg.customer_group_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (cr.order_id = o.order_id)";
		
		$implode = array();
		
		if (isset($data['filter_date_start']) && $data['filter_date_start']) {
			$implode[] = "DATE(cr.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (isset($data['filter_date_end']) && $data['filter_date_end']) {
			$implode[] = "DATE(cr.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
				
		$sql .= " GROUP BY cr.customer_id ORDER BY points DESC";
				
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

	public function getTotalCredit() {
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_reward`";
		
		$implode = array();
		
		if (isset($data['filter_date_start']) && $data['filter_date_start']) {
			$implode[] = "DATE(cr.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (isset($data['filter_date_end']) && $data['filter_date_end']) {
			$implode[] = "DATE(cr.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
				
		$sql .= " GROUP BY customer_id";
				
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
		
		return $query->row['total'];
	}
}
?>