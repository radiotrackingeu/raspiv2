<?php 
defined("CONFIGFILES_PATH")
	or define ("CONFIGFILES_PATH", realpath(dirname(__FILE__)));
	
defined("RESOURCES_PATH")
	or define ("RESOURCES_PATH", realpath(CONFIGFILES_PATH."/../resources"));
	
defined("MENU_PATH")
	or define ("MENU_PATH", realpath(RESOURCES_PATH."/weblib"));
	
defined("CONFIGLITE_PATH")
	or define ("CONFIGLITE_PATH", realpath(RESOURCES_PATH."/Config"));
	
defined("SHELLSCRIPT_PATH")
	or define ("SHELLSCRIPT_PATH", realpath(RESOURCES_PATH."/../sh_script"));
defined("GITREPO_PATH")
	or define ("GITREPO_PATH", "/home/pi/gitrep/raspiv2");
?>