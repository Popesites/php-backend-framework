<?php

$this->data = json_decode($this->data, true);
$this->data = json_encode($this->data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

echo '<pre>'.print_r($this->sanitize($this->data), true).'</pre>';