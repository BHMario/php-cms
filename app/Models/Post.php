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

    // Attach tags to a post (expects array of tag ids)
    public function setTags($postId, $tagIds)
    {
        // Remove existing
        $this->db->query("DELETE FROM post_tags WHERE post_id = ?", [$postId]);
        if (empty($tagIds)) return;
        foreach ($tagIds as $tid) {
            $this->db->query("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)", [$postId, $tid]);
        }
    }

    // Get tags for a post
    public function getTags($postId)
    {
        return $this->db->fetchAll("SELECT t.* FROM tags t JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = ?", [$postId]);
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
        return $this->db->fetch("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?", [$id]);
    }

    // Obtener todos los posts
    public function getAll()
    {
        return $this->db->fetchAll("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
    }

    // Obtener posts de un usuario
    public function getByUser($user_id)
    {
        return $this->db->fetchAll("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id WHERE p.user_id = ? ORDER BY p.created_at DESC", [$user_id]);
    }

    // Buscar posts por texto
    public function search($text)
    {
        $query = "%" . $text . "%";
        // Intenta la búsqueda con tags; si falla (tablas no existen), busca sin tags
        try {
            return $this->db->fetchAll(
                "SELECT DISTINCT p.*, u.username, u.profile_image FROM posts p 
                 LEFT JOIN post_tags pt ON p.id = pt.post_id 
                 LEFT JOIN tags t ON pt.tag_id = t.id 
                 JOIN users u ON p.user_id = u.id
                 WHERE p.title LIKE ? OR p.content LIKE ? OR t.name LIKE ?",
                [$query, $query, $query]
            );
        } catch (PDOException $e) {
            // Si falla por tablas no encontradas, busca solo en título y contenido
            return $this->db->fetchAll(
                "SELECT p.*, u.username, u.profile_image FROM posts p 
                 JOIN users u ON p.user_id = u.id
                 WHERE p.title LIKE ? OR p.content LIKE ?",
                [$query, $query]
            );
        }
    }

    // Filtrar posts por categoría
    public function filterByCategory($category_id)
    {
        return $this->db->fetchAll("SELECT * FROM posts WHERE category_id = ? ORDER BY created_at DESC", [$category_id]);
    }

    // Obtener posts recientes
    public function getRecent($limit = 5)
    {
        return $this->db->fetchAll("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT ?", [$limit]);
    }
}
