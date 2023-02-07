<?php
namespace Comment;
class Comment
{
    private int $id;
    private int $authorId;
    private int $postId;
    private string $text;

    public function __construct(int $id, int $authorId, int $postId, string $text)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->postId = $postId;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return "Комментарий: $this->text";
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

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId($postId)
    {
        $this->postId = $postId;
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