<?php
    include_once dirname(__DIR__, 2) . '/config/db.php';
    include_once dirname(__DIR__, 2) . '/shared/functions.php';
    include_once dirname(__DIR__, 2) . '/class/module_score.php';
    include_once dirname(__DIR__, 2) . '/class/data_version.php';
    set_error_handler('exceptions_error_handler');

    if ($_SERVER['REQUEST_METHOD'] == 'GET' &&
        isset($_GET['id'])) {

        try {
            $db           = new Database();
            $connect      = $db->connect();
            $module_score = new ModuleScore($connect, $_GET['id_student']);
            $data_version = new DataVersion($connect, $_GET['id_student']);

            $response = $module_score->getScore();
            if ($response['status_code'] == 200) {
                $response['content']['data_version'] = $data_version->getDataVersion('Module_Score');
            }

        } catch (Exception $error) {
            printError($error);
            $response['status_code'] = 500;
            $response['content']     = 'Error';
        }
    }
    else {
        $response['status_code'] = 406;
        $response['content']     = 'Invalid Request';
    }

    response($response, true);
