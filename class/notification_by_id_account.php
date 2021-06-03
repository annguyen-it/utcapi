<?php
    include_once dirname(__DIR__) . '/shared/functions.php';

    class NotificationByIDAccount
    {
        private const notification_account_table = 'Notification_Account';
        private const account_table = 'Account';
        private const notification_table = 'Notification';
        private const other_department_table = 'Other_Department';
        private const faculty_table = 'Faculty';
        private const teacher_table = 'Teacher';

        private PDO $connect;

        public function __construct (PDO $connect)
        {
            $this->connect = $connect;
        }

        public function getAll ($id_account) : array
        {
            $sql_query = '
                    SELECT
                        n.*,
                        od.Other_Department_Name, 
                        a.permission 
                    FROM
                         ' . self::notification_account_table . ' na,
                         ' . self::notification_table . ' n,
                         ' . self::other_department_table . ' od, 
                         ' . self::account_table . ' a  
                    WHERE
                        na.ID_Account = :id_account AND 
                        n.ID_Notification = na.ID_Notification AND
                        od.ID = n.ID_Sender AND 
                        a.id = n.ID_Sender 
                UNION
                    SELECT
                        n.*, 
                        concat(\'Khoa \', f.Faculty_Name), 
                        a.permission 
                    FROM
                         ' . self::notification_account_table . ' na,
                         ' . self::notification_table . ' n,
                         ' . self::faculty_table . ' f, 
                         ' . self::account_table . ' a    
                    WHERE
                        na.ID_Account = :id_account AND 
                        n.ID_Notification = na.ID_Notification AND
                        f.ID = n.ID_Sender AND 
                        a.id = n.ID_Sender 
                UNION
                    SELECT
                        n.*, 
                        concat(\'Gv.\', t.Name_Teacher), 
                        a.permission 
                    FROM
                         ' . self::notification_account_table . ' na,
                         ' . self::notification_table . ' n,
                         ' . self::teacher_table . ' t, 
                         ' . self::account_table . ' a    
                    WHERE
                        na.ID_Account = :id_account AND 
                        n.ID_Notification = na.ID_Notification AND
                        t.ID = n.ID_Sender AND 
                        a.id = n.ID_Sender 
                    ';

            try {
                $stmt = $this->connect->prepare($sql_query);
                $stmt->execute([':id_account' => $id_account]);
                $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $record = $this->modifyResponse($record);

                if (empty($record)) {
                    $data['status_code'] = 204;
                }
                else {
                    $data['status_code']     = 200;
                    $data['content']['data'] = $record;
                }

                return $data;

            } catch (PDOException $error) {
                throw $error;
            }
        }

        private function modifyResponse ($arr) : array
        {
            $data = [];

            for ($i = 0; $i < count($arr); $i++) {
                $arr[$i]['ID_Notification'] = intval($arr[$i]['ID_Notification']);
                $arr[$i]['ID_Sender']       = intval($arr[$i]['ID_Sender']);
                $arr[$i]['permission']      = intval($arr[$i]['permission']);

                $data['notification'][$i] = $arr[$i];
                unset($data['notification'][$i]['Other_Department_Name']);
                unset($data['notification'][$i]['permission']);

                $data['sender'][$i]['ID_Sender']   = $arr[$i]['ID_Sender'];
                $data['sender'][$i]['Sender_Name'] = $arr[$i]['Other_Department_Name'];
                $data['sender'][$i]['permission']  = $arr[$i]['permission'];
            }

            $data['sender'] = array_values(array_unique($data['sender'], SORT_REGULAR));

            return $data;
        }

    }