<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Model\Portal;

use PDO;
use System\Model;

class Assignment extends Model {

    protected $conn;

    public function __construct()
    {
        $this->conn = self::getDB();
    }

    public function addAssignment($title, $assignment, $submitted_by)
    {
        try {
                $sql = "INSERT INTO `assignment` (`assignment_title`, `assignment`, `is_submitted`, `submitted_by`) VALUES (:title, :assignment, '', :sub_by)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':assignment', $assignment);
                $stmt->bindParam(':sub_by', $submitted_by);
                $result = $stmt->execute();
                return $result;
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }

    public function updateAssignment($title, $assignment, $submitted_by, $id)
    {
        try {
            $sql = "UPDATE `assignment` SET `assignment_title`=:title, `assignment`=:assignment, `is_submitted`='', `submitted_by`=:sub_by WHERE assignment_id=:id ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':assignment', $assignment);
            $stmt->bindParam(':sub_by', $submitted_by);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()){
                return true;
            }
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }


    public function getAssignments()
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM assignment");
            $stmt->execute();
            $data=$stmt->fetchAll();
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getAssignment($id)
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM assignment WHERE assignment_id=:id");
            $stmt->bindParam("id", $id,PDO::PARAM_STR) ;
            $stmt->execute();
            $data=$stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function deleteAssignment($id)
    {
        try{
            $stmt = $this->conn->prepare("DELETE FROM assignment WHERE assignment_id=:id");
            $stmt->bindParam("id", $id,PDO::PARAM_STR) ;
            if ($stmt->execute()) {
                return true;
            }
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

}