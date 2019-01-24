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

class Material extends Model {

    protected $conn;

    public function __construct()
    {
        $this->conn = self::getDB();
    }

    public function addMaterial($title, $material, $type)
    {
        try {
                $sql = "INSERT INTO `material` (`title`, `content`, `type`) VALUES (:title, :content, :type)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':content', $material);
                $stmt->bindParam(':type', $type);
                $result = $stmt->execute();
                return $result;
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }

    // need to change
    public function updateMaterial($title, $material, $id)
    {
        try {
            $sql = "UPDATE `material` SET `title`=:title, `content`=:content WHERE material_id=:id ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $material);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()){
                return true;
            }
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }


    public function getMaterials()
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM material");
            $stmt->execute();
            while($data=$stmt->fetchAll()){
                for($i=0;$i<count($data);$i++) {
                    $noteTitle = explode(",",$data[$i]['title']);
                    if (!empty($noteTitle[0])) {
                        //title
                        $data[$i]['link'] = $noteTitle[0];
                    }
                    if (!empty($noteTitle[1])) {
                        //subject
                        $data[$i]['subject'] = $noteTitle[1];
                    }
                    if (!empty($noteTitle[2])) {
                        //course
                        $data[$i]['course'] = $noteTitle[2];
                    }
                    if (!empty($noteTitle[0]) && !empty($noteTitle[1])) {
                        $noteId = $data[$i]['material_id'];
                        $data[$i]['title'] = $noteTitle[0].' | Note for '.$noteTitle[1].' , '.$noteTitle[2];
                        $data[$i]['link'] = $noteTitle[1].'/'.$noteId;
                    }

                    if (strlen($data[$i]['content']) > 300)
                    {
                        $lastPos = (300 - 3) - strlen($data[$i]['content']);
                        $data[$i]['content'] = substr($data[$i]['content'], 0, strrpos($data[$i]['content'], ' ', $lastPos)) . '...';
                    }
                }
                return $data;
            }
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getMaterial($id)
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM material WHERE material_id=:id");
            $stmt->bindParam("id", $id,PDO::PARAM_STR) ;
            $stmt->execute();
            if($data=$stmt->fetch(PDO::FETCH_ASSOC)){
                    $noteTitle = explode(",",$data['title']);
                    if (!empty($noteTitle[0])) {
                        //title
                        $data['link'] = $noteTitle[0];
                    }
                    if (!empty($noteTitle[1])) {
                        //subject
                        $data['subject'] = $noteTitle[1];
                    }
                    if (!empty($noteTitle[2])) {
                        //course
                        $data['course'] = $noteTitle[2];
                    }
                    if (!empty($noteTitle[0]) && !empty($noteTitle[1])) {
                        $noteId = $data['material_id'];
                        $data['title'] = $noteTitle[0].' | Note for '.$noteTitle[1].' , '.$noteTitle[2];
                        $data['eTitle'] = $noteTitle[0];
                        $data['link'] = $noteTitle[1].'/'.$noteId;
                    }
                return $data;
            }
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function deleteMaterial($id)
    {
        try{
            $stmt = $this->conn->prepare("DELETE FROM material WHERE material_id=:id");
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