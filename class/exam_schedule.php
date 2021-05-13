<?php
    include_once dirname(__DIR__) . '/shared/functions.php';

    class ExamSchedule
    {
        private const exam_schedule_table = 'Exam_Schedule';

        private PDO $connect;

        public function __construct (PDO $connect)
        {
            $this->connect = $connect;
        }

        public function pushData ($data) : string
        {
            $sql_query = '
                INSERT INTO
                    ' . self::exam_schedule_table . ' 
                (
                Semester, Examination, ID_Student, ID_Module, Module_Name, Credit,
                Date_Start, Time_Start, Method, Identification_Number, Room
                )
                VALUES
                (
                :semester, :examination, :id_student, :id_module, :module_name, :credit,
                :date_start, :time_Start, :method, :identification_number, :room
                )';

            $sum = count($data);
            foreach ($data as $semester => $module) {
                foreach ($module as $value) {
                    $stmt = $this->connect->prepare($sql_query);

                    try {
                        $stmt->execute([
                            ':semester' => $this->_formatSemester($semester),
                            ':examination' => $value[0],
                            ':id_student' => $value[1],
                            ':id_module' => $value[2],
                            ':module_name' => $value[3],
                            ':credit' => $value[4],
                            ':date_start' => $value[5],
                            ':time_Start' => $value[6],
                            ':method' => $value[7],
                            ':identification_number' => $value[8],
                            ':room' => $value[9]
                        ]);

                        $response = 'OK';

                    } catch (PDOException $error) {
                        if ($error->getCode() == 23000) {
                            if (count($data) == $sum) {
                                $this->_updateData($semester, $value);
                            }
                            $response = 'OK';
                        }
                        else {
                            printError($error);
                            $response = 'Failed';
                        }
                    }
                }
                unset($data[$semester]);
            }

            return $response;
        }

        private function _updateData ($semester, $value)
        {
            $sql_query =
                'UPDATE
                    ' . self::exam_schedule_table . '
                SET  
                    Date_Start = :date_start, Time_Start = :time_Start, 
                    Identification_Number = :identification_number, Room = :room
                WHERE 
                    Semester = :semester AND
                    ID_Student = :id_student AND
                    ID_Module = :id_module';

            $stmt = $this->connect->prepare($sql_query);
            try {
                $stmt->execute([
                    ':semester' => $this->_formatSemester($semester),
                    ':id_student' => $value[1],
                    ':id_module' => $value[2],
                    ':date_start' => $value[5],
                    ':time_Start' => $value[6],
                    ':identification_number' => $value[8],
                    ':room' => $value[9]
                ]);

            } catch (PDOException $error) {
                printError($error);
            }
        }

        private function _formatSemester ($semester) : string
        {
            $semester_split = explode('_', $semester);
            $semester       = $semester_split[1] . '_' . $semester_split[2] . '_' . $semester_split[0];

            return $semester;
        }
    }
