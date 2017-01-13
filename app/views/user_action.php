<?php
$this->view('app_header');
?>

<div class="box">
	<div class="box-body">
		<div class="dataTables_wrapper form-inline dt-bootstrap">
			<table data-datatable="<?php echo $this->url; ?>/user/action_datatable/<?php echo $this->model->id; ?>" data-datatable-state="partial"
				   class="table table-bordered table-hover dataTable" width="100%">
				<thead>
				<tr>
					<th>Action</th>
					<th>Date Acted</th>
					<th class="actions-column"></th>
				</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<div id="user_action_data" class="modal" data-backdrop="static" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Data</h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php
$this->view('app_footer');