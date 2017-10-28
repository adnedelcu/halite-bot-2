<?php

spl_autoload_register(function ($className) {
    if (file_exists('hlt/'.$className.'.php')) {
        include $className.'.php';
    }
});
