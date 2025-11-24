<?php

require_once __DIR__ . '/../Models/Database.php';

class PostController
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function show($id)
    {
        $post = $this->db->fetch("SELECT * FROM posts WHERE id = ?", [$id]);
        if ($post) {
            echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";
            echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";
        } else {
            http_response_code(404);
            echo "Post no encontrado";
        }
    }
}
