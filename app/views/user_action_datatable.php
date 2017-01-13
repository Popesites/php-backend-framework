<?php

$this->datatable->array['data'] = [];

foreach ($this->datatable->array['rows'] as $row) {
	// sanitize output
	$row = $this->sanitize($row);

	// set action controls
	$action_controls = [];
	if ($row['data'] != null) {
		$action_controls[] = '<button class="btn btn-primary btn-flat" data-modal-target="#user_action_data" data-modal-href="'.$this->url.'/user/action_data/'.$row['id'].'" '.
			'data-toggle="tooltip" title="Data"><i class="fa fa-fw fa-database"></i></button>';
	}

	// set data rows
	$this->datatable->array['data'][] = [
		$row['action'],
		date(Config::$value['date_format'], $row['date_acted']),
		implode(' ', $action_controls),
	];
}

// unset sql rows & show json object
unset($this->datatable->array['rows']);
echo json_encode($this->datatable->array);