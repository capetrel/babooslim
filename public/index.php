<?php


require '../vendor/autoload.php';

use App\Controller\PageController;
use App\Page;
use App\Structure;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app');
define('CONTENT_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'site-content');
define('STRUCTURE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'site-structure');
define('VIEWS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views');

$yaml_pattern = '/^.+\.yaml/i';
Dotenv::createImmutable(BASE_PATH)->load();

$app = AppFactory::create();
$app->addErrorMiddleware($_ENV['APP_DEBUG'], $_ENV['APP_DEBUG'], $_ENV['APP_DEBUG']);

$pageFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(CONTENT_PATH));
$pageFiles = new RegexIterator($pageFiles, $yaml_pattern, RecursiveRegexIterator::GET_MATCH);

$structureFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(STRUCTURE_PATH));
$structureFiles = new RegexIterator($structureFiles, $yaml_pattern, RecursiveRegexIterator::GET_MATCH);

$structureElements = new Structure(['menus', 'blocs']);
$commonElements = $structureElements->getStructureElements($structureFiles);

session_start();
foreach ($pageFiles as $file) {
    $file = $file[0];
    $page = new Page($file);
    $app->any($page->getUrl(), new PageController($page, $commonElements));
}
session_destroy();

$app->run();