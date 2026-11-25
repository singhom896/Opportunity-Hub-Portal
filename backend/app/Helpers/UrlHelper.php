<?php

if (!function_exists('backendUrl')) {
    function backendUrl($path = '')
    {
        return url(
            config('app.backend_prefix') . '/' . ltrim($path, '/')
        );
    }
}


?>
