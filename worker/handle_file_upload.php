<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once $_SERVER['DOCUMENT_ROOT'] . '/worker/handle_file.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/worker/read_file.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/worker/push_data_to_database.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/worker/amazon_s3.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $handleFile = new HandleFile($_FILES);
        $response   = $handleFile->handleFile();

        if ($_POST['flag'] == 1 ||
            $response != null) {

            $db      = new Database();
            $connect = $db->connect();

            $read_file    = new ReadFIle();
            $work_with_db = new WorkWithDatabase($connect);
            $aws          = new AWS();

            $location = $_SERVER['DOCUMENT_ROOT'] . '/file_upload/';
            foreach ($response as $file_name) {
                $file_location = $location . $file_name;

                $data = $read_file->getData($file_name);
                $aws->uploadFile($file_name, $file_location);

                //                $work_with_db->setData($data['student_json']);
                //                $work_with_db->pushData('Student');

                //                $work_with_db->setData();
                //                $work_with_db->pushData('Module');

                //                $work_with_db->setData($data['module_class_json']);
                //                $work_with_db->pushData('Module_Class');
                //
                //                $work_with_db->setData($data['participate_json']);
                //                $work_with_db->pushData('Participate');

                //                $work_with_db->setData($data['schedule_json']);
                //                $work_with_db->pushData('Schedules');

            }
            $response = 'OK';
        }
    }
    else {
        $response = 'Invalid Request';
    }

    echo json_encode($response);

