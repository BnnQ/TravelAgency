<?php

require_once 'Startup.php';
require_once 'Models/Route.php';
require_once 'DependencyContainer.php';
require_once 'vendor/autoload.php';

use Services\UserManagerBase;
use Utils\Router;


class Outlet {
    public function __construct(public readonly Startup $startup, public readonly UserManagerBase $userManager)
    {
    }
}
$component = DependencyContainer::getContainer()->get(Outlet::class);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>1</title>
    <?php require_once "CommonStylesheets.php" ?>
</head>
<body>
<header>
    <?php
    echo Router::getNavigationMenuMarkup(routes: Router::getRoutes(), isUserAuthenticated: $component->userManager->isCurrentUserAuthenticated());
    ?>
</header>
<script src="/TravelAgency/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="/TravelAgency/node_modules/jquery/dist/jquery.min.js"></script>
<?php
$routes = Router::getRoutes();

$currentPageKey = $_GET["page"] ?? "tours";
if (str_contains($currentPageKey, '?'))
    $currentPageKey = explode('?', $currentPageKey)[0];

include_once $routes[strtolower($currentPageKey)];
?>
</body>
</html>