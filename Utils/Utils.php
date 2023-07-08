<?php
function unsetCookie($cookieName) : void {
    setcookie($cookieName, "", time() - 1);
}

function isDirectoryEmpty($directoryPath): bool {
    return count(glob($directoryPath . '/*')) === 0;
}