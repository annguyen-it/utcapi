<?php


    include_once $_SERVER['DOCUMENT_ROOT'] . '/utcapi/class/account.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/utcapi/shared/functions.php';

    class NotificationByIDAccount
    {
        private const notification_account_table = 'Notification_Account';
        private const account_table = 'Account';
        private const notification_table = 'Notification';
        private const other_department_table = 'Other_Department';
        private const faculty_table = 'Faculty';

        private PDO $connect;

        public function __construct (PDO $connect)
        {
            $this->connect = $connect;
        }

        public function getAll ($id) : array
        {
            $account = new Account($this->connect);
            $id_account = $account->getIDAccount($id);

            $sql_query = "
                    SELECT
                        n.*,
                        od.Other_Department_Name, 
                        a.Permission 
                    FROM
                         " . self::notification_account_table . " na,
                         " . self::notification_table . " n,
                         " . self::other_department_table . " od, 
                         " . self::account_table . " a  
                    WHERE
                        na.ID_Account = :id_account AND 
                        n.ID_Notification = na.ID_Notification AND
                        od.ID = n.ID_Sender AND 
                        a.id = n.ID_Sender 
                UNION
                    SELECT
                        n.*, 
                        f.Faculty_Name, 
                        a.Permission 
                    FROM
                         " . self::notification_account_table . " na,
                         " . self::notification_table . " n,
                         " . self::faculty_table . " f, 
                         " . self::account_table . " a    
                    WHERE
                        na.ID_Account = :id_account AND 
                        n.ID_Notification = na.ID_Notification AND
                        f.ID = n.ID_Sender AND 
                        a.id = n.ID_Sender 
                    ";

            try {
                $stmt = $this->connect->prepare($sql_query);
                $stmt->execute([':id_account' => $id_account]);

                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$data)
                {
                    $data['notification'] = [];
                    $data['sender'] = [];

                    return $data;
                }

                $data = $this->modifyResponse($data);

            } catch (PDOException $error) {
                printError($error);

                $data['notification'] = [];
                $data['sender'] = [];
            }


            return $data;
        }

        private function modifyResponse ($arr) : array
        {
            $data = [];

            for ($i = 0; $i < count($arr); $i++) {
                $arr[$i]['ID_Notification'] = intval($arr[$i]['ID_Notification']);
                $arr[$i]['ID_Sender']       = intval($arr[$i]['ID_Sender']);
                $arr[$i]['Permission']      = intval($arr[$i]['Permission']);

                $data['notification'][$i] = $arr[$i];
                unset($data['notification'][$i]['Other_Department_Name']);
                unset($data['notification'][$i]['Permission']);

                $data['sender'][$i]['ID_Sender']   = $arr[$i]['ID_Sender'];
                $data['sender'][$i]['Sender_Name'] = $arr[$i]['Other_Department_Name'];
                $data['sender'][$i]['Permission']  = $arr[$i]['Permission'];
            }

            $data['sender'] = array_values(array_unique($data['sender'], SORT_REGULAR));

            return $data;
        }

    }