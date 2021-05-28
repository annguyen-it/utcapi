<?php
    include_once dirname(__DIR__, 2) . '/config/db.php';
    include_once dirname(__DIR__, 2) . '/shared/functions.php';
    include_once dirname(__DIR__, 2) . '/class/account.php';

    $data     = json_decode(file_get_contents('php://input'), true);
    $response = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
        !empty($data)) {

        try {
            $db      = new Database();
            $connect = $db->connect();
            $account = new Account($connect);

            $account_info = $account->login($data);
            if ($account_info == 'Failed') {
                $response['status_code']        = 404;
                $response['content']['message'] = 'failed';
            }
            else {
                switch ($account_info['permission']) {
                    case '0':
                        $response = $account->getDataAccountOwner($account_info['id'], 'Student');
                        break;

                    case '1':
                        $response = $account->getDataAccountOwner($account_info['id'], 'Teacher');
                        break;

                    default:
                        $response['status_code']        = 404;
                        $response['content']['message'] = 'failed';
                }
            }

        } catch (Exception $error) {
            $response['status_code']        = 500;
            $response['content']['message'] = 'error';
        }
    }
    else {
        $response['status_code'] = 406;
        $response['content']     = 'Invalid Request';
    }

    response($response, true);
