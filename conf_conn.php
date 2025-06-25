<?php
	function getDatetimeNow($f) {
		$tz_object = new DateTimeZone('Asia/Jakarta');
		$datetime = new DateTime();
		$datetime->setTimezone($tz_object);
		return $datetime->format($f);
	}

	 $host = 'localhost:3306';
	 $uname = 'admspher';
	 $pwd = 'Sphere154.com!2019';
	 $db = 'dbgsk_spg_md';
	 $pathurl = 'gsk_spg';

?>