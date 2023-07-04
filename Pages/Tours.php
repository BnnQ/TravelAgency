<?php

namespace Pages;

use DependencyContainer;

class Tours
{
    public function __construct()
    {
        //empty
    }

}

$component = DependencyContainer::getContainer()->get(Tours::class);
?>

<div id="body" class="container-fluid p-0 min-vh-100 w-100">
    <div class="row">
        <div class="col">Tours</div>
    </div>
</div>
