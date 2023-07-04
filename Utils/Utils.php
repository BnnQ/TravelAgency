<?php
function unsetCookie($cookieName) : void {
    setcookie($cookieName, "", time() - 1);
}