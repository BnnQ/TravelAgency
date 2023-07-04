<?php

namespace Utils;
require_once "Utils\RouteConstants.php";

use Models\Route;

class Router
{
    private static array $routes = [];

    public static function registerRoute(Route $route): void {
        self::$routes[$route->key] = $route->pagePath;
    }

    public static function getRoutes(): array {
        return self::$routes;
    }

    public static function redirectToPage(string $pageAddress) : void {
        echo "<script>location.replace('$pageAddress');</script>";
    }

    public static function redirectToLocalPageByKey(string $routeKey): void
    {
        echo "<script>location.replace('/TravelAgency/Outlet.php?page=$routeKey');</script>";
    }


    public static function getNavigationMenuMarkup(array $routes, bool $isUserAuthenticated = false): string
    {
        $resultBuilder = "<nav class='navbar navbar-expand-lg navbar-light text-light'><div class='container-fluid justify-content-center text-center gap-3' style='padding: 32px'>";

        foreach ($routes as $key=>$pagePath) {
            if (($key === ROUTE_Login || $key === ROUTE_Register) && $isUserAuthenticated)
                continue;
            else if ($key === ROUTE_Logout && !$isUserAuthenticated)
                continue;

            $pageName = strtoupper($key[0]).substr($key, 1);

            $page = $_GET['page'] ?? ROUTE_Tours;
            $className = ($key === $page) ? "nav-item active" : "nav-item";
            $resultBuilder.="<a href='/TravelAgency/Outlet.php?page=$key' class='col $className'>$pageName</a>";
        }

        $resultBuilder.="</div></nav>";
        return $resultBuilder;
    }

}