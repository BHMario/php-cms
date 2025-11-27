<?php
// Test admin routes rendering via Router
session_start();
$_SESSION['user_id'] = 1; // assume admin exists in DB for actual browser test

require_once __DIR__ . '/app/Router.php';
$router = new Router();

// Try admin posts index
ob_start();
$router->route('/admin/posts');
$out = ob_get_clean();
echo "--- /admin/posts output (first 800 chars) ---\n";
echo substr($out,0,800) . "\n\n";

// Try admin categories index
ob_start();
$router->route('/admin/categories');
$out = ob_get_clean();
echo "--- /admin/categories output (first 800 chars) ---\n";
echo substr($out,0,800) . "\n\n";

// Try admin users index
ob_start();
$router->route('/admin/users');
$out = ob_get_clean();
echo "--- /admin/users output (first 800 chars) ---\n";
echo substr($out,0,800) . "\n\n";

?>