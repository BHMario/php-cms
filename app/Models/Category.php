<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel
{
    private ?int $id = null;
    private ?string $name = null;

    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC") ?? [];
    }

    public function getById(int $id): ?array
    {
        $this->validateId($id);
        return $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }

    public function create(string $name): int
    {
        $this->validateNotEmpty($name, 'name');
        $this->db->query("INSERT INTO categories (name) VALUES (?)", [$name]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->validateId($id);
        $this->db->query("DELETE FROM categories WHERE id = ?", [$id]);
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
