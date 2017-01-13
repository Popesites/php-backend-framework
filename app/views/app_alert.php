<div class="alert alert-<?php echo $this->alert['class']; ?> alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<?php

	if (is_array($this->alert['message'])) {
		// alert message is an array, show ul with each item
		echo '<ul class="list-unstyled">';

		foreach ($this->alert['message'] as $item) {
			echo '<li>'.$item.'</li>';
		}

		echo '</ul>';
	}
	else {
		// message is a string, show it directly
		echo $this->alert['message'];
	}

	?>
</div>