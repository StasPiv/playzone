<?php
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata, true);

    function randomFile($dir = 'uploads')
    {
        $files = glob($dir . '/*.*');
        $file = array_rand($files);
        return $files[$file];
    }

    $directory = __DIR__ . '/' . $_GET['method'] . '/';

    echo file_get_contents(randomFile($directory));