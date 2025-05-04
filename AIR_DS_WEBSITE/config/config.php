<?php 
// This file contains 


// echo $_SERVER["DOCUMENT_ROOT"];

// define a constant to get the root folder of the project
// BASE_PATH = AIR_DS_WEBSITE folder
define('BASE_PATH', __DIR__ . '/../');

/**
 * A url starts from the server root (for xampp /htdocs)
 * By using BASE_URL every url start from a folder/file in AIR_DS_WEBSITE 
 * every URL is built:
 *  url = <server_root>/ path of the file
 * using BASE_URL every URL is built:
 *  url = <server_root>BASE_URL<relative_path_under_AIR_DS_WEBSITE
 */
define('BASE_URL', '/WEB_ZITONOULIS_DIMITRIOS_E22054/AIR_DS_WEBSITE/');
?>