<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/12/2018
 * Time: 9:12 PM
 */

namespace App\Model;

use PDO;
use System\Model;

class Student extends Model
{
    protected $conn;

    public function __construct()
    {
        $this->conn = self::getDB();
    }

    public function studentRegister($fname, $lname, $email, $password)
    {
        $username = $this->random_username($fname . $lname );
        $password = md5($password);
        try {
            //check email is already exist or not
            $stmtCE = $this->conn->prepare("SELECT email FROM login WHERE email=:email");
            $stmtCE->bindParam("email", $email,PDO::PARAM_STR) ;
            $stmtCE->execute();
            $data=$stmtCE->fetch(PDO::FETCH_ASSOC);
            if ($data>0) return false;

            $sql = "INSERT INTO `student` (`first_name`, `last_name`, `email`) VALUES (:fname, :lname, :email)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fname', $fname);
            $stmt->bindParam(':lname', $lname);
            $stmt->bindParam(':email', $email);
            $result = $stmt->execute();
            if($result) {
                $sql = "INSERT INTO `login` (`username`, `email`, `password`, `role`) VALUES (:username, :email, :password, 'Student')";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $result = $stmt->execute();
                return $result;
            }
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }

    public function studentInfo($email)
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM student WHERE email=:email");
            $stmt->bindParam("email", $email,PDO::PARAM_STR) ;
            $stmt->execute();
            $data=$stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }



}