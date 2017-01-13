<?php

$this->datatable->array['data'] = [];

foreach ($this->datatable->array['rows'] as $row) {
	// sanitize output
	$row = $this->sanitize($row);

	// set action controls
	$action_controls = [];
	if (AppUser::has_permission('GeneratedObject_update')) {
		$action_controls[] = '<a href="'.$this->url.'/generated_object/update/'.$row['id'].'" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Update">'.
			'<i class="fa fa-fw fa-pencil"></i></a>';
	}
	if (AppUser::has_permission('GeneratedObject_delete')) {
		$action_controls[] = '<a href="'.$this->url.'/generated_object/delete/'.$row['id'].'" class="btn btn-danger btn-flat" data-confirm="Delete this?" data-toggle="tooltip" title="Delete">'.
			'<i class="fa fa-fw fa-trash"></i></a>';
	}

	// set data rows
	$this->datatable->array['data'][] = [
		// generator datatable columns
		implode(' ', $action_controls),
	];
}

// unset sql rows & show json object
unset($this->datatable->array['rows']);
echo json_encode($this->datatable->array);