<?php

require_once ('clientmove.class.php');

if ($_GET ['mode'] === 'makecenters') {
	global $dPconfig;
	$fkey = $_POST ['ekey'];
	$centers = buildCFile ();
	require_once $AppUI->getFileInModule ( $m, 'doexfile' );
	$ltpath = uniqid ();
	foreach ( $centers as $clin_id => $clients ) {
		exportProceed ( $clin_id, $clients, $ltpath );
	}
	$today = date ( "d-m-y" );
	$dpath = $baseDir . '/files/tmp/' . $ltpath . '/';
	$zipname = 'LTPcenters-' . $today . '.zip';
	$zfile = new ZipArchive ();
	$rfile = $zfile->open ( $dpath . $zipname, ZIPARCHIVE::CREATE );
	$kill_list=array();
	if ($rfile === TRUE) {
		$dir = opendir ( $dpath );
		while ( $file = readdir ( $dir ) ) {
			if ($file == "." || $file == "..") {
			} elseif (preg_match ( '/\.tbn$/', $file ) && is_readable ( $dpath . $file )) {
				if (! $zfile->addFile ( $dpath . $file, $file )) {
					//print $file . "was not added!<br />";
				}else{
					$kill_list[]=$dpath . $file ;
				}
			}
		}
		$zfile->close ();
	}

	//system ( $dPconfig ['zip_path'] . ' -j -m -q "' . $dpath . '"' . $zipname . ' "' . $dpath . '"*.bin' );

	if(file_exists($dpath . $zipname) && filesize($dpath . $zipname) > 0){
		printForSave ( '', 'application/zip', $zipname, true, false, $dpath );
		$fh = fopen ( $dpath . $zipname, 'rb' );
		flush_buffers ();
		rewind ( $fh );
		fpassthru ( $fh );
		fclose ( $fh );
		@unlink ( $dpath . $zipname );
		foreach ($kill_list as $kf) {
			@unlink($kf);
		}
		@rmdir ( $dpath );
	}
	$fl = fopen ( $baseDir . '/modules/manager/eflag/' . $fkey, 'a+' );
	fclose ( $fl );


} else {
	echo buildList ();
}

?>