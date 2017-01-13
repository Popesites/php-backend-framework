<?php
$this->view('app_header');
?>

<div class="box">
	<?php if (AppUser::has_permission('User_create')) { ?>
		<div class="box-header with-border">
			<a href="<?php echo $this->url; ?>/user/create" class="btn btn-primary btn-flat">Create</a>
		</div>
	<?php } ?>

	<div class="box-body">
		<div class="dataTables_wrapper form-inline dt-bootstrap">
			<table data-datatable="<?php echo $this->url; ?>/user/datatable" data-datatable-state="full" class="table table-bordered table-hover dataTable" width="100%">
				<thead>
				<tr>
					<th>Username</th>
					<th>Email Address</th>
					<th>Timezone</th>
					<th>Level</th>
					<th class="actions-column"></th>
				</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<?php
$this->view('app_footer');