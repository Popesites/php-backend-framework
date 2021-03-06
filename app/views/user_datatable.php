<?php

$this->datatable->array['data'] = [];

foreach ($this->datatable->array['rows'] as $row) {
	// sanitize output
	$row = $this->sanitize($row);

	// set action controls
	$action_controls = [];
	$action_controls[] = '<a href="'.$this->url.'/user/action/'.$row['id'].'" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Actions">'.
		'<i class="fa fa-fw fa-bolt"></i></a>';
	if (AppUser::has_permission('User_update')) {
		$action_controls[] = '<a href="'.$this->url.'/user/update/'.$row['id'].'" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Update">'.
			'<i class="fa fa-fw fa-pencil"></i></a>';
	}
	if (AppUser::has_permission('User_delete')) {
		$action_controls[] = '<a href="'.$this->url.'/user/delete/'.$row['id'].'" class="btn btn-danger btn-flat" data-confirm="Delete this?" data-toggle="tooltip" title="Delete">'.
			'<i class="fa fa-fw fa-trash"></i></a>';
	}

	// set data rows
	$this->datatable->array['data'][] = [
		$row['username'],
		$row['email_address'],
		$row['timezone'],
		$row['level'],
		implode(' ', $action_controls),
	];
}

// unset sql rows & show json object
unset($this->datatable->array['rows']);
echo json_encode($this->datatable->array);