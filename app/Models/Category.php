<?php
require_once __DIR__ . '/Database.php';

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
    }

    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }

    public function create($name)
    {
        $this->db->query("INSERT INTO categories (name) VALUES (?)", [$name]);
        return $this->db->lastInsertId();
    }
}
