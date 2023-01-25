<?php
namespace Post;
class Post
{
    private int $id;
    private int $authorId;
    private string $title;
    private string $text;

    public function __construct(int $id, int $authorId, string $title, string $text)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return "Название: $this->title Текст: $this->text";
    }
    
    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }
}