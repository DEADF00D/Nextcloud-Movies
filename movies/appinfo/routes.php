<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Movies\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'watch#nowWatch', 'url' => '/watch/{id}', 'verb' => 'GET'],

       [
           'name' => 'movies#myMovies',
           'url' => '/api/v1/movies/',
           'verb' => 'GET',
           'requirements' => [
               'path' => '.*',
           ],
           'defaults' => [
               'path' => '',
           ],
       ],[
           'name' => 'movies#myMovie',
           'url' => '/api/v1/movie/{path}',
           'verb' => 'GET',
           'requirements' => [
               'path' => '.*',
           ],
           'defaults' => [
               'path' => '',
           ],
       ]
    ]
];
