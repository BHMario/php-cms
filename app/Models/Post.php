<?php

require_once __DIR__ . '/Database.php';

class Post
{
    private $db;
    public $id;
    public $title;
    public $content;
    public $image;
    public $user_id;
    public $category_id;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Crear nuevo post
    public function create($title, $content, $user_id, $category_id = null, $image = null)
    {
        $this->db->query(
            "INSERT INTO posts (title, content, user_id, category_id, image) VALUES (?, ?, ?, ?, ?)",
            [$title, $content, $user_id, $category_id, $image]
        );
        $this->id = $this->db->lastInsertId();
        $this->title = $title;
        $this->content = $content;
        $this->user_id = $user_id;
        $this->category_id = $category_id;
        $this->image = $image;
        return $this;
    }

    // Actualizar post
    public function update($id, $title, $content, $category_id = null, $image = null)
    {
        $this->db->query(
            "UPDATE posts SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?",
            [$title, $content, $category_id, $image, $id]
        );
    }

    // Eliminar post
    public function delete($id)
    {
        $this->db->query("DELETE FROM posts WHERE id = ?", [$id]);
    }

    // Obtener post por ID
    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM posts WHERE id = ?", [$id]);
    }

    // Obtener todos los posts
    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM posts ORDER BY created_at DESC");
    }
}
