<?php


    include_once $_SERVER['DOCUMENT_ROOT'] . '/utcapi/config/db.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/utcapi/config/print_error.php';

    class LoginApp
    {
        private const account_table_name = 'Account';
        private const student_table_name = 'Student';
        private const teacher_table_name = 'Teacher';
        private PDO $connect;

        public function __construct (PDO $connect)
        {
            $this->connect = $connect;
        }

        public function checkAccount ($account) : array
        {
            $sql_query = "
                SELECT
                    *
                FROM
                     " . self::account_table_name . "
                WHERE
                    Username = :username AND
                    password = :password";

            try {
                $stmt = $this->connect->prepare($sql_query);
                $stmt->execute([':username' => $account['ID'],
                                ':password' => md5($account['Password'])]);

                $response = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$response) {
                    $data['message'] = 'failed';
                }
                else {
                    if ($response['Permission'] == '0') {
                        $data = $this->_getData($response['id'], self::student_table_name);
                    }
                    else {
                        if ($response['Permission'] == '1') {
                            $data = $this->_getData($response['id'], self::teacher_table_name);
                        }
                        else {
                            $data['message'] = 'failed';
                        }
                    }
                }

                return $data;

            } catch (PDOException $error) {
                printError($error);
                $data['message'] = 'failed';

                return $data;
            }
        }

        private function _getData ($id_account, $table_name) : array
        {
            $sql_query = "
                SELECT
                    * 
                FROM 
                     " . $table_name . "
                WHERE 
                    ID = :id";


            try {
                $stmt = $this->connect->prepare($sql_query);
                $stmt->execute([':id' => $id_account]);

                $response = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$response) {
                    $data['message'] = 'failed';
                }
                else {
                    $data['message'] = 'success';
                    $data['info']    = $response;
                }

                return $data;

            } catch (PDOException $error) {
                printError($error);
                $data['message'] = 'failed';

                return $data;
            }
        }
    }

