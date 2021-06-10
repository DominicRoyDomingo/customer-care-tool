<?php

Route::group(['namespace' => '\CRM\Web'], function () {
    foreach (scandir($path = base_path('CRM/Web')) as $dirName) {
        if (file_exists($filepath = $path . '/' . $dirName . '/Route.php')) {
            include $filepath;
        }
    }
});
