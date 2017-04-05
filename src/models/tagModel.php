<?php

namespace Models;

/**
* Tags for article models
*/
class Tags extends Database
{
    public function addItem($tagName)
    {
        if (empty($tagName)) {
            return;
        }
        try {
            $insert = "INSERT OR IGNORE into tags (tag_name) VALUES (:tag_name)";
            $prepare = $this->db->prepare($insert);
                $prepare->execute([
                    ':tag_name' => $tagName,
                ]);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'no such table')) {
                $this->createTable();
                $this->addItem($tags);
            }
        }
    }

    public function addTagToArticle($articleId, $tagName)
    {
        if (empty($articleId) || strtolower($articleId) === 'null') {
            return;
        }
        $this->addItem($tagName);
        $insertArticleTagQuery = "INSERT OR IGNORE INTO article_tags (article_id, tag_id) VALUES (:article_id, (SELECT id FROM tags WHERE tag_name = :tag_name))";
        $insertArticleTag = $this->db->prepare($insertArticleTagQuery);
        $this->db->beginTransaction();
        $insertArticleTag->execute([
            ':tag_name' => $tagName,
            ':article_id' => $articleId,
        ]);
        return $this->db->commit();
    }

    public function createTable()
    {
        $create = "CREATE TABLE IF NOT EXISTS tags (id integer primary key, tag_name text unique);";
        $create .= "CREATE TABLE IF NOT EXISTS article_tags (id integer primary key, article_id integer not null, tag_id integer not null, UNIQUE(article_id, tag_id));";
        return $this->db->query($create);
    }

    public function deleteTagFromArticle($articleId, $tagName)
    {
        if (empty($articleId) || strtolower($articleId) === 'null') {
            return;
        }
        try {
            $delete = "DELETE FROM article_tags WHERE article_id = :article_id AND tag_id = (SELECT id FROM tags WHERE tag_name = :tag_name)";
            $prepare = $this->db->prepare($delete);
            return $prepare->execute([
                ':article_id' => $articleId,
                ':tag_name' => $tagName,
            ]);
        } catch (\Exception $e) {
            return 'Error message : ' . $e->getMessage();
        }
    }

    public function getAllTags()
    {
        $select = "SELECT count(at.id) jumlah, at.tag_id id, t.tag_name FROM article_tags AS at LEFT JOIN tags AS t ON t.id = at.tag_id GROUP BY tag_id ORDER BY tag_name";
        $prepare = $this->db->prepare($select);
        $prepare->execute();
        return $prepare->fetchAll();
    }

    public function getTagsByArticleId($articleId)
    {
        if (!isset($articleId)) {
            return;
        }

        $select = "SELECT t.id tag_id, t.tag_name FROM article_tags AS at LEFT JOIN tags AS t ON t.id = at.tag_id LEFT JOIN articles AS a ON a.id = at.article_id WHERE a.id = :id ORDER BY t.tag_name";
        $prepare = $this->db->prepare($select);
        $prepare->execute([
            ':id' => $articleId,
        ]);
        return $prepare->fetchAll();
    }
}
