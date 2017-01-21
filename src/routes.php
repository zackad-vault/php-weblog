<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->view->render('index.twig', $args);
});

$app->get('/test/[{base}]', function ($request, $response, $args) {
    return $this->view->render('base-' . $args['base'] . '.twig', $args);
});
