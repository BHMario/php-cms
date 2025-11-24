<?php
require_once __DIR__ . '/Database.php';

class Tag
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM tags ORDER BY name ASC");
    }

    public function getByName($name)
    {
        return $this->db->fetch("SELECT * FROM tags WHERE name = ?", [$name]);
    }

    public function create($name)
    {
        $this->db->query("INSERT INTO tags (name) VALUES (?)", [$name]);
        return $this->db->lastInsertId();
    }

    public function getOrCreate($name)
    {
        $t = $this->getByName($name);
        if ($t) return $t['id'];
        return $this->create($name);
    }
}
