<?php


    include_once $_SERVER['DOCUMENT_ROOT'] . "/worker/handle_file.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . "/worker/read_file.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . "/worker/push_data_to_database.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . "/config/db.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        var_dump($_FILES);
        $handleFile = new HandleFile($_FILES);
        $response   = $handleFile->handleFile();

        if ($_POST['flag'] == 1 ||
            $response != null) {

            $db           = new Database();
            $connect      = $db->connect();

            $read_file    = new ReadFIle();
            $work_with_db = new WorkWithDatabase($connect);

            foreach ($response as $file_name) {
                $data = $read_file->getData($file_name);

                echo json_decode($data['module_json']);
                //                $work_with_db->setData($data['student_json']);
                //                $work_with_db->pushData("Student");

                //                $work_with_db->setData($data['module_json']);
                //                $work_with_db->pushData("Module");

                //                $work_with_db->setData($data['module_class_json']);
                //                $work_with_db->pushData("Module_Class");
                //
                //                $work_with_db->setData($data['participate_json']);
                //                $work_with_db->pushData("Participate");

//                $work_with_db->setData($data['schedule_json']);
//                $work_with_db->pushData("Schedules");

            }
            $response = 'OK';
        }
    }
    else {
        $response = 'Invalid Request';
    }

//    echo json_encode($response);

