<?php

/**
 * Article Management
 */

// Article listing for management
$app->get('/manage/article/', function ($request, $response, $args) {
    if (!$this->auth->isLogin()) {
        return $this->view->render($response, 'errors/403-forbidden.twig');
    }
    $article = new Models\Article;
    $data['args'] = $args;
    $data['postlist'] = $article->getItem();
    return $this->view->render($response, 'manage-post.twig', $data);
})->setName('manage-article');

// Article editing
$app->get('/edit/article/[{id}/[{flash}/]]', function ($request, $response, $args) {
    if (!$this->auth->isLogin()) {
        return $this->view->render($response, 'errors/403-forbidden.twig');
    }
    $article = new Models\Article;
    $tag = new Models\Tags;
    if (!isset($args['id'])) {
        return $response->withRedirect($this->router->pathFor('manage-article'), 302);
    }
    $data['post'] = $article->getItemById($args['id']);
    if (empty($data['post'])) {
        $data = $article->notFound();
        return $this->view->render($response, 'notice.twig', $data);
    }
    $tags = $tag->getTagsByArticleId($args['id']);
    foreach ($tags as $key => $value) {
        $tagList[] = $value['tag_name'];
    }
    $data['post']['tags'] = $tagList;
    $data['title'] = 'Editing "' . $data['post']['title'] . '"';
    return $this->view->render($response, 'post-edit.twig', $data);
})->setName('edit-article');

$app->get('/add/article/[{status}/]', function ($request, $response, $args) {
    if (!$this->auth->isLogin()) {
        return $this->view->render($response, 'errors/403-forbidden.twig');
    }
    if (isset($args['status']) and $args['status'] === 'done') {
        $data['flash'] = 'Saved Successfully';
        $data['link'] = [
            [
                'link' => '/add/article/',
                'anchor' => 'Add Other Article',
            ],
            [
                'href' => '/manage/article/',
                'anchor' => 'Manage Article',
            ]
        ];
        return $this->view->render($response, 'notice.twig', $data);
    }
    $data['title'] = 'Adding Article';
    return $this->view->render($response, 'post-edit.twig', $data);
})->setName('add-article');

// Article processing
$app->post('/{type:add|edit}/article/[{id}/]', function ($request, $response, $args) {
    if (!$this->auth->isLogin()) {
        return $this->view->render($response, 'errors/403-forbidden.twig');
    }
    $article = new Models\Article;
    $tag = new Models\Tags;
    $post = $request->getParsedBody();
    if ($args['type'] === 'add') {
        $post['poster'] = $_SESSION['username'];
        $article->addItem($post);
        return $response->withRedirect($this->router->pathFor('add-article', [
            'status' => 'done',
            ]), 302);
    } else {
        $post['id'] = $args['id'];
        $article->updateItem($post);
        $data['post'] = $article->getItemById($args['id']);
        $tags = $tag->getTagsByArticleId($args['id']);
        foreach ($tags as $key => $value) {
            $tagList[] = $value['tag_name'];
        }
        $data['post']['tags'] = $tagList;
        $data['title'] = 'Editing "' . $data['post']['title'] . '"';
        $data['flash'] = 'Saved Successfully';
        $data['body'] = $post;
    }
    return $this->view->render($response, 'post-edit.twig', $data);
});


/**
 * Authentication Prosess
 */
$app->get('/login/', function ($request, $response, $args) {
    return $this->view->render($response, 'loginPage.twig');
})->setName('login');

$app->post('/login/', function ($request, $response, $args) {
    $user = $request->getParsedBody()['user'];
    $pass = $request->getParsedBody()['pass'];
    $auth = new Models\Auth;
    if ($auth->login($user, $pass)) {
        // Do something if success
        print_r($auth);
        return $response->withRedirect($this->router->pathFor('homepage'));
    } else {
        $data = [
            'username' => $user,
            'password' => $pass,
        ];
        return $this->view->render($response, 'loginPage.twig', $data);
    }
});

$app->get('/logout/', function ($request, $response, $args) {
    $auth = new Models\Auth;
    $auth->logout();
    return $response->withRedirect($this->router->pathFor('homepage'));
})->setName('logout');


/**
 * Sitemap generator
 */
$app->get('/sitemap.xml', function ($request, $response, $args) {
    $sitemap = new Models\Sitemap;
    $urlset = $sitemap->getUrlset();
    $uri = $request->getUri();
    $data['uri'] = $uri->getPath();
    $data['baseurl'] = $uri->getScheme() . '://' . $uri->getHost();
    $data['urlset'] = $urlset;

    $response = $response->withHeader('Content-type', 'text/xml');
    return $this->view->render($response, 'sitemap.twig', $data);
})->setName('sitemap');


/**
 * Tags Management
 */
$app->post('/add/tags/', function ($request, $response, $args) {
    if (!$this->auth->isLogin()) {
        return $this->view->render($response, 'errors/403-forbidden.twig');
    }
    $body = $request->getParsedBody();
    $tag = new Models\Tags;
    $tag->addTagToArticle($body['articleId'], $body['tagName']);
    return $response->withJson($body, null, JSON_NUMERIC_CHECK);
});
