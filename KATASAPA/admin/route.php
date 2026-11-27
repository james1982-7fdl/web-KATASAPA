<?php

$p=$_GET['page'];

switch ($p) {
	case 'pengumuman':
		require_once 'pengumuman.php';
		break;

	case 'tambah-pengumuman':
		require_once 'form-pengumuman.php';
		break;

	case 'hapus-pengumuman':
		require_once 'del-pengumuman.php';
		break;
	
	default:
		require_once 'dashboard.php';
		break;
}