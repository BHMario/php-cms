<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';

class Tag extends BaseModel
{
    private ?int $id = null;
    private ?string $name = null;

    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM tags ORDER BY name ASC") ?? [];
    }

    public function getByName(string $name): ?array
    {
        $this->validateNotEmpty($name, 'name');
        return $this->db->fetch("SELECT * FROM tags WHERE name = ?", [$name]);
    }

    public function create(string $name): int
    {
        $this->validateNotEmpty($name, 'name');
        $this->db->query("INSERT INTO tags (name) VALUES (?)", [$name]);
        return (int)$this->db->lastInsertId();
    }

    public function getOrCreate(string $name): int
    {
        $this->validateNotEmpty($name, 'name');
        
        $t = $this->getByName($name);
        if ($t) {
            return (int)$t['id'];
        }
        return $this->create($name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->validateNotEmpty($name, 'name');
        $this->name = $name;
        return $this;
    }
}
