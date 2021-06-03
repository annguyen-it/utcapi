<?php
    session_start();
    include_once dirname(__DIR__, 3) . '/config/db.php';
    include_once dirname(__DIR__, 3) . '/shared/functions.php';
    include_once dirname(__DIR__, 3) . '/class/module_score.php';
    include_once dirname(__DIR__, 3) . '/class/exam_schedule.php';
    include_once dirname(__DIR__, 3) . '/class/account.php';
    include_once dirname(__DIR__, 3) . '/class/crawl_qldt_data.php';
    set_error_handler('exceptions_error_handler');

    $data = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
        isset($data)) {

        try {
            $db           = new Database();
            $connect      = $db->connect();
            $account      = new Account($connect);
            $module_score = new ModuleScore($connect, $data['id_student']);

            $data['qldt_password'] = $account->getQLDTPasswordOfStudentAccount($data['id_account']);
            $crawl                 = new CrawlQLDTData($data['id_student'], $data['qldt_password']);

            if (isset($crawl_data[0])) {
                if ($crawl_data[0] == -1) {
                    $response['status_code'] = 500;
                    $response['content']     = 'Error';
                }
                else {
                    $response['status_code'] = 401;
                    $response['content']     = 'Invalid Password';
                }
            }
            else {
                $exam_schedule = new ExamSchedule($connect, $data['id_student']);

                if ($data['all'] == 'true') {
                    $semester   = $module_score->getAllRecentSemester();
                    $crawl_data = $crawl->getStudentExamSchedule($semester);

                    $exam_schedule->pushAllData($crawl_data);
                }
                else {
                    $semester   = $module_score->getRecentLatestSemester();
                    $crawl_data = $crawl->getStudentExamSchedule($semester);

                    if (count($crawl_data) == 2) {
                        array_shift($crawl_data);
                    }

                    $exam_schedule->pushData($crawl_data);
                }

                $response['status_code'] = 200;
                $response['content']     = 'OK';
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
