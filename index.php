<?php
// Lightweight redirect so visiting the project root opens the SiteAcademia app.
// This helps when the webserver's document root is set to the repository root.
$target = '/SiteAcademia/index.html';
// If you run the PHP built-in server from this folder, the leading slash
// maps to the server root. Use absolute-style redirect.
header('Location: ' . $target);
exit;
