<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';

class Post extends BaseModel
{
    private ?int $id = null;
    private ?string $title = null;
    private ?string $slug = null;
    private ?string $content = null;
    private ?string $image = null;
    private ?int $user_id = null;
    private ?int $category_id = null;

    /**
     * Generar slug SEO-friendly a partir del título
     * Ej: "Mi Primer Post!" -> "mi-primer-post"
     * 
     * @param string $text
     * @return string slug
     */
    public function generateSlug(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        
        // Limitar a 200 caracteres
        return substr($text, 0, 200);
    }

    /**
     * Asegurar que el slug es único agregando sufijo si es necesario
     * 
     * @param string $slug
     * @return string slug único
     */
    private function ensureUniqueSlug(string $slug): string
    {
        $original = $slug;
        $counter = 1;
        
        while ($this->db->fetch("SELECT id FROM posts WHERE slug = ?", [$slug])) {
            $slug = $original . '-' . $counter++;
            if ($counter > 1000) {
                // Fallback: agregar timestamp si hay muchos duplicados
                $slug = $original . '-' . time();
                break;
            }
        }
        
        return $slug;
    }

    // Crear nuevo post
    // Devuelve la fila creada como array para mantener compatibilidad con vistas/controlladores antiguos
    public function create(string $title, string $content, int $user_id, ?int $category_id = null, ?string $image = null): array
    {
        $this->validateNotEmpty($title, 'title');
        $this->validateNotEmpty($content, 'content');
        $this->validateId($user_id);
        if ($category_id !== null) {
            $this->validateId($category_id);
        }
        
        $slug = $this->ensureUniqueSlug($this->generateSlug($title));
        
        $this->db->query(
            "INSERT INTO posts (title, slug, content, user_id, category_id, image) VALUES (?, ?, ?, ?, ?, ?)",
            [$title, $slug, $content, $user_id, $category_id, $image]
        );
        $this->id = (int)$this->db->lastInsertId();
        $this->title = $title;
        $this->slug = $slug;
        $this->content = $content;
        $this->user_id = $user_id;
        $this->category_id = $category_id;
        $this->image = $image;

        // Devolver la fila recién creada como array para compatibilidad con código que espera arrays
        return $this->getById($this->id) ?? [];
    }

    // Attach tags to a post (expects array of tag ids)
    public function setTags(int $postId, array $tagIds): void
    {
        $this->validateId($postId);
        
        // Remove existing
        $this->db->query("DELETE FROM post_tags WHERE post_id = ?", [$postId]);
        if (empty($tagIds)) return;
        foreach ($tagIds as $tid) {
            if (!is_numeric($tid)) {
                throw new InvalidArgumentException('Tag ID debe ser numérico');
            }
            $this->db->query("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)", [$postId, (int)$tid]);
        }
    }

    // Get tags for a post
    public function getTags(int $postId): array
    {
        $this->validateId($postId);
        return $this->db->fetchAll("SELECT t.* FROM tags t JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = ?", [$postId]) ?? [];
    }

    // Actualizar post
    public function update(int $id, string $title, string $content, ?int $category_id = null, ?string $image = null): void
    {
        $this->validateId($id);
        $this->validateNotEmpty($title, 'title');
        $this->validateNotEmpty($content, 'content');
        if ($category_id !== null) {
            $this->validateId($category_id);
        }
        
        $slug = $this->ensureUniqueSlug($this->generateSlug($title));
        
        $this->db->query(
            "UPDATE posts SET title = ?, slug = ?, content = ?, category_id = ?, image = ? WHERE id = ?",
            [$title, $slug, $content, $category_id, $image, $id]
        );
    }

    // Eliminar post
    public function delete(int $id): void
    {
        $this->validateId($id);
        $this->db->query("DELETE FROM posts WHERE id = ?", [$id]);
    }

    // Obtener post por ID
    public function getById(int $id): ?array
    {
        $this->validateId($id);
        return $this->db->fetch("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?", [$id]);
    }

    // Obtener post por slug (SEO-friendly)
    public function getBySlug(string $slug): ?array
    {
        $this->validateNotEmpty($slug, 'slug');
        return $this->db->fetch("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id WHERE p.slug = ?", [$slug]);
    }

    // Obtener todos los posts
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC") ?? [];
    }

    // Obtener posts de un usuario
    public function getByUser(int $user_id): array
    {
        $this->validateId($user_id);
        return $this->db->fetchAll("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id WHERE p.user_id = ? ORDER BY p.created_at DESC", [$user_id]) ?? [];
    }

    // Buscar posts por texto
    public function search(string $text): array
    {
        $this->validateNotEmpty($text, 'text');
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
            ) ?? [];
        } catch (PDOException $e) {
            // Si falla por tablas no encontradas, busca solo en título y contenido
            return $this->db->fetchAll(
                "SELECT p.*, u.username, u.profile_image FROM posts p 
                 JOIN users u ON p.user_id = u.id
                 WHERE p.title LIKE ? OR p.content LIKE ?",
                [$query, $query]
            ) ?? [];
        }
    }

    // Filtrar posts por categoría
    public function filterByCategory(int $category_id): array
    {
        $this->validateId($category_id);
        return $this->db->fetchAll("SELECT * FROM posts WHERE category_id = ? ORDER BY created_at DESC", [$category_id]) ?? [];
    }

    // Obtener posts recientes
    public function getRecent(int $limit = 5): array
    {
        if ($limit <= 0) {
            throw new InvalidArgumentException('Limit debe ser mayor a 0');
        }
        return $this->db->fetchAll("SELECT p.*, u.username, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT ?", [$limit]) ?? [];
    }

    // ============ GETTERS ============
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    // ============ SETTERS ============
    public function setTitle(string $title): self
    {
        $this->validateNotEmpty($title, 'title');
        $this->title = $title;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->validateNotEmpty($content, 'content');
        $this->content = $content;
        return $this;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function setCategoryId(?int $category_id): self
    {
        if ($category_id !== null) {
            $this->validateId($category_id);
        }
        $this->category_id = $category_id;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'image' => $this->image,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
        ];
    }
}
