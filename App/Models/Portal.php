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

class Portal extends Model
{
    protected $conn;

    public function __construct()
    {
        $this->conn = self::getDB();
    }

    public function getNotesByLatest(){
        try{
            $stmt = $this->conn->prepare("SELECT * FROM material ORDER BY material_id DESC LIMIT 4");
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
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getAssignmentsByLatest()
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM assignment ORDER BY assignment_id DESC LIMIT 4");
            $stmt->execute();
            $data=$stmt->fetchAll();
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function count($data) {
        try {
            $stmt = $this->conn->prepare("SELECT username FROM login WHERE role=:role");
            $stmt->bindParam("role", $data, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->rowCount();
            if ($data > 0) {
                return $data;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
    }

    public function countPortal($data) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM {$data}");
            $stmt->execute();
            $data = $stmt->rowCount();
            if ($data > 0) {
                return $data;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
    }

    public function search($query, $data) {
        if ($data == 'note') {
            //search note
            try {
                $stmt = $this->conn->prepare("SELECT * FROM material WHERE type='note' AND ((title) LIKE :query OR (content) LIKE :query)");
                $stmt->bindValue(':query', '%' . $query . '%');
                $stmt->execute();
                while($data = $stmt->fetchAll()) {
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
                        if (strlen($data[$i]['content']) > 300) {
                            $lastPos = (300 - 3) - strlen($data[$i]['content']);
                            $data[$i]['content'] = substr($data[$i]['content'], 0, strrpos($data[$i]['content'], ' ', $lastPos)) . '...';
                        }
                    }
                    return $data;
                }
            } catch (PDOException $e) {
                echo '{"error":{"text":' . $e->getMessage() . '}}';
            }
        } elseif ($data == 'assignments') {
            try {
                $stmt = $this->conn->prepare("SELECT * FROM assignment WHERE (assignment_title) LIKE :query OR (assignment) LIKE :query");
                $stmt->bindValue(':query', '%' . $query . '%');
                $stmt->execute();
                while($data = $stmt->fetchAll()) {
                    for($i=0;$i<count($data);$i++) {
                        if (strlen($data[$i]['assignment']) > 300) {
                            $lastPos = (300 - 3) - strlen($data[$i]['content']);
                            $data[$i]['assignment'] = substr($data[$i]['content'], 0, strrpos($data[$i]['content'], ' ', $lastPos)) . '...';
                        }
                    }
                    return $data;
                }
            } catch (PDOException $e) {
                echo '{"error":{"text":' . $e->getMessage() . '}}';
            }
        }
    }



}