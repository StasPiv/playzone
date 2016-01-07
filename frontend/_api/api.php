<?php
    /**
     * Fake api controller for test json responses
     */
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata, true);

    function randomFile($dir)
    {
        $files = glob($dir . '/*.*');
        $file = array_rand($files);
        return $files[$file];
    }

    $directory = __DIR__ . '/' . $_GET['method'] . '/';

    $jsonResponse = file_get_contents(randomFile($directory));

    http_response_code(json_decode($jsonResponse, true)['status']);

    echo $jsonResponse;