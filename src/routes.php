<?php
// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->view->render('index.twig', $args);
});

$app->get('/{type:latest|popular|random}/', function ($request, $response, $args) {
    $this->logger->info("'/post/".$args['type']."/' route");
    $article = new Models\Article;
    $data['type'] = $args['type'];
    $data['postlist'] = $article->getItem($args['type']);
    foreach ($data['postlist'] as $key => $value) {
        $data['postlist'][$key]['slug'] = $article->slugify($value['title']);
    }
    return $this->view->render('post-list.twig', $data);
});

// $app->get('/post/[{id:[0-9]+}/]', function ($request, $response, $args) {
$app->get('/post/[{id:[0-9]+}/[{slug}/]]', function ($request, $response, $args) {
    $this->logger->info("'/post' route");

    $article = new Models\Article;
    $post = $article->getItemById($args['id']);
    $article->updateViewCount($post['id']);
    $data = [
        'title' => 'Test Post',
        'post' => $post,
        'id' => $args['id'],
    ];
    return $this->view->render('post.twig', $data);
});

$app->get('/tags[/]', function ($request, $response, $args) {
    $this->logger->info("'/tags' route");
    $data = [];
    $data['title'] = 'Categories';
    return $this->view->render('post-list.twig', $data);
});

$app->get('/test/[{base}/]', function ($request, $response, $args) {
    return $this->view->render('base-' . $args['base'] . '.twig', $args);
});
