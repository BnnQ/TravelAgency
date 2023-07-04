<?php

require_once "Utils\Router.php";
require_once "Utils\RouteConstants.php";

use Models\Route;
use Utils\Router;

class Startup
{
    public function __construct()
    {
        Router::registerRoute(new Route(key: ROUTE_Tours, pagePath: "Pages/Tours.php"));
        Router::registerRoute(new Route(key: ROUTE_Login, pagePath: "Pages/Login.php"));
        Router::registerRoute(new Route(key: ROUTE_Register, pagePath: "Pages/Register.php"));
        Router::registerRoute(new Route(key: ROUTE_Logout, pagePath: "Pages/Logout.php"));
        Router::registerRoute(new Route(key: ROUTE_Comments, pagePath: "Pages/Comments.php"));
        Router::registerRoute(new Route(key: ROUTE_Admin, pagePath: "Pages/AdminPanel.php"));
    }

}