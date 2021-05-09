<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    class HandleFile
    {
        private array $file_arr;

        public function __construct (array $fileArr)
        {
            $this->file_arr = $fileArr;
        }

        public function handleFile ()
        {
            var_dump($this->file_arr);
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $new_file_name_arr = null;

            foreach ($this->file_arr as $file) {
                $nameSplit = explode('.', $file['name']);
                $timeSplit = explode('.', microtime(true));

                $new_file_name = $nameSplit[0] . '_' . $timeSplit[0] . $timeSplit[1] . '.' . $nameSplit[1];

                $location = $_SERVER['DOCUMENT_ROOT'] . '/file_upload/' . $new_file_name;

                if (move_uploaded_file($file['tmp_name'], $location)) {
                    echo 1234;
                    $new_file_name_arr[] = $new_file_name;
                }
                else {
                    $new_file_name_arr = null;
                }
            }

            return $new_file_name_arr;
        }
    }