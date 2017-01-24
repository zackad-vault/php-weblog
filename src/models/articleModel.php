<?php

namespace Models;

use \Exception;

/**
* Article class
*/
class Article extends Database
{
    private $title;
    private $post;
    private $poster;
    private $datePosted;
    private $dateModified;
    private $tags;
    private $views;
    
    public function __construct()
    {
        parent::__construct();
        try {
            $this->getItem('a');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function __construct1($args)
    {
        parent::__construct();
    }

    public function addItem($data)
    {
        $insert = "INSERT OR IGNORE INTO articles (title, post, poster, date_posted, date_modified, tags, views) VALUES (:title, :post, :poster, datetime(), datetime(), :tags, 0)";
        $prepare = $this->db->prepare($insert);
        return $prepare->execute([
            ':title' => $data['title'],
            ':post' => $data['post'],
            ':tags' => $data['tags'],
            ':poster' => $data['poster'],
        ]);
    }

    public function createTable()
    {
        $create = "CREATE TABLE IF NOT EXISTS articles (id integer primary key, title text, post text, poster text, date_posted text, date_modified text, tags text, views integer default 0)";
        return $this->db->query($create);
    }

    public function getItem($type = '')
    {
        try {
            switch ($type) {
                case 'random':
                    $select = $this->getRandomItems();
                    break;
                case 'popular':
                    $select = $this->getPopularItems();
                    break;
                case 'latest':
                default:
                    $select = $this->getLatestItems();
                    break;
            }
            $prepare = $this->db->prepare($select);
            $prepare->execute();
            return $prepare->fetchAll();
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'no such table') !== false) {
                $this->createTable();
                $this->getItem();
            } else {
                return "error message : " . $e->getMessage();
            }
        }
    }

    public function getItemById($id)
    {
        $select = "SELECT * FROM articles WHERE id = :id";
        $prepare = $this->db->prepare($select);
        $prepare->execute([
            ':id' => $id,
        ]);
        $res = $prepare->fetch();
        // if (empty($res)) {
        //     $res['title'] = 'Article Not Found';
        //     $res['message'] = 'Sorry, the article that you looking for is not found!';
        // }
        return $res;
    }

    public function getItemByTagName($tag_name)
    {
        $select = "SELECT * FROM tags AS t JOIN article_tags AS at ON at.tag_id = t.id JOIN articles AS a ON a.id = at.article_id  WHERE t.tag_name = :tag_name";
        $prepare = $this->db->prepare($select);
        $prepare->execute([
            ':tag_name' => $tag_name,
        ]);
        return $prepare->fetchAll();
    }

    private function getLatestItems()
    {
        return "SELECT * FROM articles ORDER BY date_modified DESC LIMIT 20";
    }

    private function getPopularItems()
    {
        return "SELECT * FROM articles ORDER BY views DESC LIMIT 20";
    }

    public function getRandomArticle()
    {
        $select = "SELECT * FROM articles ORDER BY random() LIMIT 1";
        return $this->db->query($select)->fetch();
    }

    private function getRandomItems()
    {
        return "SELECT * FROM articles ORDER BY RANDOM() LIMIT 20";
    }

    public function insertItem($data = [])
    {
        if (empty($data)) {
            return;
        }
        $insert = "INSERT INTO articles (title, post, poster, date_posted, date_modified, tags, views) VALUES (:title, :post, :poster, datetime('now'), datetime('now'), :tags, 0)";
        $prepare = $this->db->prepare($insert);
        return $prepare->execute([
            ':post' => $data['post'],
            ':poster' => $data['poster'],
            ':tags' => $data['tags'],
            ':title' => $data['title'],
        ]);
    }

    public function notFound()
    {
        return [
            'title' => 'Article Not Found',
            'message' => 'Sorry, the article that you looking for is not found!',
        ];
    }

    public function slugify($text = '')
    {
        // replace non letter or number by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text);
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function truncate()
    {
        $this->db->query("DELETE FROM articles;VACUUM;");
    }

    public function updateItem($data)
    {
        $update = "UPDATE articles SET title = :title, post = :post, date_modified = datetime(), tags = :tags WHERE id = :id";
        $prepare = $this->db->prepare($update);
        return $prepare->execute([
            ':title' => $data['title'],
            ':post' => $data['post'],
            ':tags' => $data['tags'],
            ':id' => $data['id'],
        ]);
    }

    public function updateViewCount($articleId)
    {
        $update = "UPDATE articles SET views = views + 1 WHERE id = :id";
        $prepare = $this->db->prepare($update);
        return $prepare->execute([
            ':id' => $articleId,
        ]);
    }
}
