<?php

namespace Pages;

use DependencyContainer;
use Services\UserManagerBase;
use Utils\Router;

class Logout
{
    public function __construct(public readonly UserManagerBase $userManager)
    {
        //empty
    }
}

$component = DependencyContainer::getContainer()->get(Logout::class);
$component->userManager->signOutUser();
Router::redirectToLocalPageByKey('tours');