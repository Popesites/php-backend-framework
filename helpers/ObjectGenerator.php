<?php

class ObjectGenerator {
	public $name = [];
	public $columns = [];

	function __construct ($name, $columns = null) {
		// set name variations
		$this->name['singular'] = $name;
		$this->name['plural'] = Inflect\Inflect::pluralize($this->name['singular']);
		$this->name['class'] = str_replace(' ', '', $this->name['singular']);
		$this->name['variable'] = str_replace(' ', '_', strtolower($this->name['singular']));
		
		// set columns, always have id auto_increment first
		$this->columns['id'] = 'ai';

		if ($columns != null) {
			// columns are set, parse them
			$columns = explode(',', $columns);

			foreach ($columns as $column) {
				// set columns name => type
				$column = explode(':', $column);
				$this->columns[$column[0]] = $column[1];
			}
		}
		else {
			// columns not set, use default (name)
			$this->columns['name'] = 'varchar';
		}
	}
	
	function object_exists () {
		return class_exists($this->name['class']);
	}

	function create_db_table () {
		// create new db table for object
		$query_columns = [];
		
		foreach ($this->columns as $name => $type) {
			// set column query string by type
			if ($type == 'ai') {
				$query_columns[] = '`'.$name.'` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY';
			}
			else if ($type == 'int') {
				$query_columns[] = '`'.$name.'` INT(11) NOT NULL';
			}
			else if ($type == 'varchar') {
				$query_columns[] = '`'.$name.'` VARCHAR(255) NOT NULL';
			}
			else if ($type == 'text') {
				$query_columns[] = '`'.$name.'` TEXT NOT NULL';
			}
		}

		// execute query
		$stm = DB::$pdo->prepare("CREATE TABLE `".$this->name['variable']."` (".implode(',', $query_columns).") ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		$stm->execute();
	}

	static function insert_before ($file_name, $hook, $content) {
		// insert content in file before hook
		$old_file_content = file_get_contents($file_name);
		$new_file_content = str_replace($hook, $content.$hook, $old_file_content);

		file_put_contents($file_name, $new_file_content);
	}

	function insert_permissions () {
		// insert permissions line in config file
		$file_name = 'config.php';
		$content = '	\''.$this->name['class'].'\' => [\'create\', \'read\', \'update\', \'delete\'],'.PHP_EOL;

		$this->insert_before($file_name, '	// generator user permission hook', $content);
	}

	function insert_menu_item () {
		// insert menu item lines in app_header file
		$file_name = 'app/views/app_header.php';
		$content = '					if (AppUser::has_permission(\''.$this->name['class'].'_read\')) {'.PHP_EOL;
		$content .= '						echo \'<li\'.(($this->page_title == \''.$this->name['plural'].'\') ? \' class="active"\' : \'\').\'>'.
					'<a href="\'.$this->url.\'/'.$this->name['variable'].'"><i class="fa fa-link"></i> <span>'.$this->name['plural'].'</span></a></li>\';'.PHP_EOL;
		$content .= '					}'.PHP_EOL;

		$this->insert_before($file_name, '					// generator header menu hook', $content);
	}

	function replace_names ($content) {
		// replace all names within content
		$replace_array = [
			'Generated Objects' => $this->name['plural'],
			'Generated Object' => $this->name['singular'],
			'GeneratedObject' => $this->name['class'],
			'generated_object' => $this->name['variable'],
		];

		foreach ($replace_array as $search => $replace) {
			$content = str_replace($search, $replace, $content);
		}

		return $content;
	}

	function create_model () {
		// create model file and insert/replace content
		$file_content = file_get_contents('generator/GeneratedObject.php');

		// replace names
		$file_content = $this->replace_names($file_content);

		// replace class properties
		$content = '';
		foreach ($this->columns as $name => $type) {
			$content .= '	public $'.$name.';'.PHP_EOL;
		}
		$file_content = str_replace('	// generator property hook', rtrim($content, PHP_EOL), $file_content);

		// replace binded parameters
		$content = '';
		foreach ($this->columns as $name => $type) {
			if ($type != 'ai') {
				$content .= '		$stm->bindParam(\':'.$name.'\', $this->'.$name.');'.PHP_EOL;
			}
		}
		$file_content = str_replace('		// generator bind hook', rtrim($content, PHP_EOL), $file_content);

		// replace query insert columns
		$content = [];
		foreach ($this->columns as $name => $type) {
			if ($type != 'ai') {
				$content[] = '`'.$name.'`';
			}
		}
		$file_content = str_replace('{$generator_insert_columns}', implode(', ', $content), $file_content);

		// replace query insert values
		$content = [];
		foreach ($this->columns as $name => $type) {
			if ($type != 'ai') {
				$content[] = ':'.$name;
			}
		}
		$file_content = str_replace('{$generator_insert_values}', implode(', ', $content), $file_content);

		// replace query update values
		$content = [];
		foreach ($this->columns as $name => $type) {
			if ($type != 'ai') {
				$content[] = '`'.$name.'`=:'.$name;
			}
		}
		$file_content = str_replace('{$generator_update_values}', implode(', ', $content), $file_content);

		// create new class file with content
		file_put_contents('app/models/'.$this->name['class'].'.php', $file_content);
	}

	function create_controller () {
		// create controller file and insert/replace content
		$file_content = file_get_contents('generator/GeneratedObjectController.php');

		// replace names
		$file_content = $this->replace_names($file_content);

		// replace datatable display & search columns
		$content = [];
		foreach ($this->columns as $name => $type) {
			$content[] = "'$name'";
		}
		$file_content = str_replace('// generator display columns', '$this->datatable->display_columns = ['.implode(', ', $content).'];', $file_content);
		$file_content = str_replace('// generator search columns', '$this->datatable->search_columns = ['.implode(', ', $content).'];', $file_content);

		// replace validator rules
		$content = '';
		foreach ($this->columns as $name => $type) {
			if ($type != 'ai') {
				$content .= '		$validator->rules[] = [\''.ucfirst(str_replace('_', ' ', $name)).'\', \''.$name.'\', \'required\'];'.PHP_EOL;
			}
		}
		$file_content = str_replace('		// generator rules', rtrim($content, PHP_EOL), $file_content);

		// create new class file with content
		file_put_contents('app/controllers/'.$this->name['class'].'Controller.php', $file_content);
	}

	function create_views () {
		// create view files and insert/replace content
		$form_fields = '';

		foreach ($this->columns as $name => $type) {
			// set form fields for create/update
			if ($type != 'ai') {
				$form_fields .= '			<div class="form-group">'.PHP_EOL;
				$form_fields .= '				<label for="'.$name.'" class="control-label">'.ucfirst(str_replace('_', ' ', $name)).'</label>'.PHP_EOL;
			}
			
			if ($type == 'int') {
				$form_fields .= '				<input name="'.$name.'" id="'.$name.'" type="number" class="form-control" value="<?php echo $this->sanitize($this->model->'.$name.'); ?>">'.PHP_EOL;
			}
			else if ($type == 'varchar') {
				$form_fields .= '				<input name="'.$name.'" id="'.$name.'" type="text" class="form-control" value="<?php echo $this->sanitize($this->model->'.$name.'); ?>">'.PHP_EOL;
			}
			else if ($type == 'text') {
				$form_fields .= '				<textarea name="'.$name.'" id="'.$name.'" rows="5" class="form-control"><?php echo $this->sanitize($this->model->'.$name.'); ?></textarea>'.PHP_EOL;
			}

			if ($type != 'ai') {
				$form_fields .= '			</div>'.PHP_EOL;
			}
		}

		// create & update
		$actions = ['create', 'update'];
		foreach ($actions as $action) {
			$file_content = file_get_contents('generator/generated_object_'.$action.'.php');
			$file_content = $this->replace_names($file_content);
			$file_content = str_replace('			<!-- generator form fields -->', rtrim($form_fields, PHP_EOL), $file_content);
			file_put_contents('app/views/'.$this->name['variable'].'_'.$action.'.php', $file_content);

		}
		
		// index
		$file_content = file_get_contents('generator/generated_object_index.php');
		$file_content = $this->replace_names($file_content);
		$content = '';
		foreach ($this->columns as $name => $type) {
			if ($name == 'id') {
				$th = 'ID';
			}
			else {
				$th = ucfirst(str_replace('_', ' ', $name));
			}

			$content .= '					<th>'.$th.'</th>'.PHP_EOL;
		}
		$file_content = str_replace('					<!-- generator table headings -->', rtrim($content, PHP_EOL), $file_content);
		file_put_contents('app/views/'.$this->name['variable'].'_index.php', $file_content);

		// datatable
		$file_content = file_get_contents('generator/generated_object_datatable.php');
		$file_content = $this->replace_names($file_content);
		$content = '';
		foreach ($this->columns as $name => $type) {
			$content .= '		$row[\''.$name.'\'],'.PHP_EOL;
		}
		$file_content = str_replace('		// generator datatable columns', rtrim($content, PHP_EOL), $file_content);
		file_put_contents('app/views/'.$this->name['variable'].'_datatable.php', $file_content);
	}

	function drop_db_table () {
		// drop the database table
		$stm = DB::$pdo->prepare("DROP TABLE `".$this->name['variable']."`");
		$stm->execute();
	}

	function delete_permissions () {
		// delete permissions line in config file
		$file_name = 'config.php';
		$file_content = file_get_contents($file_name);
		$hook = '	\''.$this->name['class'].'\' => [\'create\', \'read\', \'update\', \'delete\'],'.PHP_EOL;

		file_put_contents($file_name, str_replace($hook, '', $file_content));
	}

	function delete_menu_item () {
		// delete menu item line in app_header file
		$file_name = 'app/views/app_header.php';
		$file_content = file_get_contents($file_name);
		$hook = '					if (AppUser::has_permission(\''.$this->name['class'].'_read\')) {'.PHP_EOL;
		$hook .= '						echo \'<li\'.(($this->page_title == \''.$this->name['plural'].'\') ? \' class="active"\' : \'\').\'>'.
			'<a href="\'.$this->url.\'/'.$this->name['variable'].'"><i class="fa fa-link"></i> <span>'.$this->name['plural'].'</span></a></li>\';'.PHP_EOL;
		$hook .= '					}'.PHP_EOL;
		
		file_put_contents($file_name, str_replace($hook, '', $file_content));
	}

	function delete_files () {
		// delete object mvc files
		unlink('app/controllers/'.$this->name['class'].'Controller.php');
		unlink('app/models/'.$this->name['class'].'.php');
		unlink('app/views/'.$this->name['variable'].'_create.php');
		unlink('app/views/'.$this->name['variable'].'_datatable.php');
		unlink('app/views/'.$this->name['variable'].'_index.php');
		unlink('app/views/'.$this->name['variable'].'_update.php');
	}
}