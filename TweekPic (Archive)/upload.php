<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings(){
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable." . $uploadDirectory);
        }

        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }

        if(!$replaceOldFile){
            $filename = uniqid();
            /// don't overwrite previous files that were uploaded
            /*while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }*/
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            $filename .= '.' . $ext;
            $ret = array('success'=>true, 'filename'=>$filename);

            $reg = mysql_query("SELECT pic_path FROM tweekpic_jobs WHERE user_id='{$_SESSION['id']}' AND day='{$_GET['day']}'");

            if (mysql_num_rows($reg) == 0) {
                $regb = mysql_query("INSERT INTO tweekpic_jobs (day, user_id, pic_path, do_ad) VALUES ('{$_GET['day']}','{$_SESSION['id']}', '{$filename}', 'off')");
            } else {
                unlink($uploadDirectory . mysql_result($reg, 0));
                $regb = mysql_query("UPDATE tweekpic_jobs SET pic_path='{$filename}' WHERE user_id='{$_SESSION['id']}' AND day='{$_GET['day']}'");
            }
            if  ($regb) {
                return ($ret);
            } else {
                unlink($uploadDirectory . $filename);
                return array('error'=> 'Could not save job to database.');
            }
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }

    }
}
session_start();
$the_db = mysql_connect('HOST','USER','PASS');
mysql_select_db('DATABASE', $the_db);

//OFF
if ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == 'YES') && (isset($_SESSION['id'])) &&
    (isset($_GET['off'])) && (strlen($_GET['off']) == 3))
{
    $reg = mysql_query("UPDATE tweekpic_jobs SET do_ad='off' WHERE user_id='{$_SESSION['id']}' AND day='{$_GET['off']}'");
    if ($reg) { echo '1'; die(); }
    else { echo '0'; die(); }
}
//ON
elseif ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == 'YES') && (isset($_SESSION['id'])) &&
    (isset($_GET['on'])) && (strlen($_GET['on']) == 3))
{
    $reg = mysql_query("UPDATE tweekpic_jobs SET do_ad='on' WHERE user_id='{$_SESSION['id']}' AND day='{$_GET['on']}'");
    if ($reg) { echo '1'; die(); }
    else { echo '0'; die(); }
}
//DELETE
elseif ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == 'YES') && (isset($_SESSION['id'])) &&
    (isset($_GET['del'])) && (strlen($_GET['del']) == 3))
{
    $reg = mysql_query("SELECT pic_path FROM tweekpic_jobs WHERE user_id='{$_SESSION['id']}' AND day='{$_GET['del']}'");
    $regb = mysql_query("DELETE FROM tweekpic_jobs WHERE user_id='{$_SESSION['id']}' AND day='{$_GET['del']}'");
    if ($reg && $regb) {
        unlink('./images/' . mysql_result($reg, 0));
        echo '1';
        die();
    }
    else { echo '0'; die(); }
}
//UPLOAD
elseif ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == 'YES') && (isset($_SESSION['id'])) &&
        (isset($_GET['day'])) && (strlen($_GET['day']) == 3))
{
    // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $allowedExtensions = array("jpeg" , "jpg", "png", "gif");
    // max file size in bytes
    $sizeLimit = 10 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result = $uploader->handleUpload('./images/');
    // to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
} else {
    header("HTTP/1.0 404 Not Found");
}
?>
