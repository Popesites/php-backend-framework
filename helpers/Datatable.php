<?php

class Datatable {
	public $request;
	public $display_columns = [];
	public $search_columns = [];
	public $query;
	public $where;
	public $search_where;
	public $group_by;
	public $array = [];
	
	function __construct () {
		$this->request = $_POST;
	}

	function set_search_where () {
		$search_where_array = [];

		if (!empty($this->where)) {
			// query contains where statement already
			$prepend = 'and';
		}
		else {
			// query does not contain where statement
			$prepend = 'where';
		}

		if (!empty($this->request['search']['value'])) {
			// search not empty, set query via columns
			foreach ($this->search_columns as $search_column) {
				$search_where_array[] = "$search_column like concat('%', :search, '%')";
			}

			$this->search_where = $prepend.' ('.implode(' or ', $search_where_array).')';
		}
	}

	function set_array () {
		// set where for queries
		if (!empty($this->search_columns)) {
			$this->set_search_where();
		}

		// set datatable array values
		$this->array['draw'] = $this->request['draw'];
		$this->array['recordsTotal'] = $this->fetch_total_count();
		$this->array['recordsFiltered'] = $this->fetch_filtered_count();
		$this->array['rows'] = $this->fetch_rows();
	}

	function fetch_total_count () {
		// fetch total object row count
		$stm = DB::$pdo->prepare("{$this->query} {$this->where} {$this->group_by}");
		$stm->execute();
		$res = $stm->rowCount();

		return $res;
	}

	function fetch_filtered_count () {
		// fetch object row count where
		$stm = DB::$pdo->prepare("{$this->query} {$this->where} {$this->search_where} {$this->group_by}");
		if (!empty($this->request['search']['value'])) {
			// bind search for where query
			$stm->bindParam(':search', $this->request['search']['value']);
		}
		$stm->execute();
		$res = $stm->rowCount();

		return $res;
	}

	function fetch_rows () {
		// fetch object rows with filters
		$stm = DB::$pdo->prepare("{$this->query} {$this->where} {$this->search_where} {$this->group_by}
								  order by {$this->display_columns[$this->request['order'][0]['column']]} {$this->request['order'][0]['dir']}
								  limit {$this->request['start']}, {$this->request['length']}");
		if (!empty($this->request['search']['value'])) {
			// bind search for where query
			$stm->bindParam(':search', $this->request['search']['value']);
		}
		$stm->execute();
		$res = $stm->fetchAll();

		return $res;
	}
}