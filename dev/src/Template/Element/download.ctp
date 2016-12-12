<?php
	// Si es descargar automática
	header('Pragma: public'); 	// required
	header('Expires: 0');		// no cache
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_real_path)).' GMT');
	header('Cache-Control: private',false);
	header('Content-type: ' . $file->type);
	header('Content-Disposition: attachment; filename="'.basename($file->filename).'"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($file_real_path));	// provide file size
	header('Connection: close');
	readfile($file_real_path);		// push it out
	exit();

?>