<?php
    include_once dirname(__DIR__) . '/utils/env_io.php';

    function printError (Exception $error)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $date    = date('d/m/Y H:i:s');
        $message = $date . '\n' . $error->getCode() . '\n' . $error->getMessage() . '\n';
        EnvIO::loadEnv();
        file_put_contents(dirname(__DIR__) . '/Errors.txt', $message, FILE_APPEND);
    }

    function convertDate ($date) : string
    {
        $arr  = explode('-', $date);
        $date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

        return $date;
    }

    function response ($data, $isExit)
    {
        header(responseHeaders($data['status_code']));
        header('Content-Type: application/json');
        echo json_encode($data['content'], JSON_UNESCAPED_UNICODE);
        if ($isExit) {
            exit();
        }
    }

    function responseHeaders ($status_code) : string
    {
        return 'HTTP/1.1 ' . $status_code . ' ' . getStatusCodeMessage($status_code);
    }

    function getStatusCodeMessage ($status_code) : string
    {
        $codes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return $codes[$status_code];
    }