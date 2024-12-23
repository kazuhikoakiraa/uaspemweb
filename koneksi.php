<?php
class Koneksi {
    public $db;

    public function __construct() {
        $host = "localhost";
        $dbname = "anime_management";
        $username = "root"; 
        $password = "";     

        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
}
?>