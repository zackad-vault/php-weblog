<?php

namespace Models;

/**
* Sitemap class
*/
class Sitemap extends Article
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUrlset()
    {
        $select = "SELECT id, title, date_modified FROM articles ORDER BY date_modified DESC";
        $prepare = $this->db->prepare($select);
        $prepare->execute();
        $res = $prepare->fetchAll();
        foreach ($res as $key => $url) {
            $res[$key]['slug'] = $this->slugify($url['title']);
            $res[$key]['permalink'] = '/post/'.$url['id'].'/'.$res[$key]['slug'].'/';
        }
        return $res;
    }
}
