if (!file_exists("/tmp/DOCKERACORNCFG")) {
	touch("/tmp/DOCKERACORNCFG");
	header("Location: /config.php");
}
