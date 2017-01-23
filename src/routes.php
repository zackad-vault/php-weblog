<?php
// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->view->render($response, 'index.twig', $args);
})->setName('homepage');

$app->get('/{type:latest|popular|random}/', function ($request, $response, $args) {
    $this->logger->info("'/post/".$args['type']."/' route");
    $article = new Models\Article;
    $data['type'] = $args['type'];
    $data['postlist'] = $article->getItem($args['type']);
    foreach ($data['postlist'] as $key => $value) {
        $data['postlist'][$key]['slug'] = $article->slugify($value['title']);
    }
    return $this->view->render($response, 'post-list.twig', $data);
});

$app->get('/post/[{id:[0-9]+}/[{slug}/]]', function ($request, $response, $args) {
    $this->logger->info("'/post' route");

    // Initiate a
    $article = new Models\Article;
    $post = $article->getItemById($args['id']);
    if (empty($post)) {
        return $this->view->render($response, 'errors/404-notfound.twig');
    }
    $slug = $article->slugify($post['title']);

    // Redirect article post if slug is wrong or not exists
    if (!isset($args['slug']) || $args['slug'] !== $slug) {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $redirectPath = $this->router->pathFor('article', ['id' => $args['id']]) . $slug .'/';
        return $response->withRedirect((string)$redirectPath, 302);
    }

    // Update article view counter
    $article->updateViewCount($post['id']);

    // Data to be passed to view renderer
    $data = [
        'title' => 'Test Post',
        'post' => $post,
        'id' => $args['id'],
        'args' => $args,
    ];
    return $this->view->render($response, 'post.twig', $data);
})->setName('article');

$app->get('/tags[/]', function ($request, $response, $args) {
    $this->logger->info("'/tags' route");
    $data = [];
    $data['title'] = 'Categories';
    return $this->view->render($response, 'post-list.twig', $data);
});
