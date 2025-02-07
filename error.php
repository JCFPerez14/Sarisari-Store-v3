<?php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (error_reporting() === 0) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    http_response_code($_SERVER['REDIRECT_STATUS']);
    $status = $_SERVER['REDIRECT_STATUS'];
    $codes = array(
        400 => array('400 Bad Request', 'The request cannot be fulfilled due to bad syntax'),
        401 => array('401 Unauthorized', 'Authentication failed or not yet provided'),
        403 => array('403 Forbidden', 'The request was valid, but the server is refusing action'),
        404 => array('404 Not Found', 'The requested resource could not be found'),
        500 => array('500 Internal Server Error', 'An unexpected condition was encountered')
    );

    $title = $codes[$status][0];
    $message = $codes[$status][1];
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?></title>
        <style>
            body { 
                font-family: Arial, sans-serif;
                text-align: center;
                padding: 50px;
            }
            .error-container {
                max-width: 500px;
                margin: 0 auto;
            }
            .error-code {
                font-size: 72px;
                color: #e74c3c;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-code"><?php echo $status; ?></div>
            <h1><?php echo $title; ?></h1>
            <p>We're sorry, but something went wrong.</p>
            <a href="/Sarisari-Store-v3/SSSSJC.php">Return to Homepage</a>
        </div>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    error_log($e->getMessage());
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    include 'error.php';
    exit;
}
?>