<?php

function asset(string $path) {
    global $app;
    $root = getEnvValue('DOC_ROOT');
    return $app->getBasePath() . $root . trim($path, '/');
}

function viteAssets(string $mainJsFile = 'resources/js/main.js', bool $noJS = false, bool $push = false): string
{
    $appPath = dirname(dirname(__DIR__));

    if(getEnvValue('APP_DEBUG') === 'true') {
        if($push) {
            return <<<HTML
            <script type="module" src="http://localhost:5173/assets/{$mainJsFile}"></script>
        HTML;
        } else {
            return <<<HTML
            <script type="module" src="http://localhost:5173/assets/@vite/client"></script>
            <script type="module" src="http://localhost:5173/assets/{$mainJsFile}"></script>
        HTML;
        }
    } else {
        $manifest = json_decode(
            file_get_contents($appPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . '.vite' . DIRECTORY_SEPARATOR . 'manifest.json'),
            true
        );
        if($noJS) {
            return <<<HTML
                    <link rel="stylesheet" href="/assets/{$manifest[$mainJsFile]['css'][0]}">
                HTML;
        } else {
            return <<<HTML
                <script type="module" src="/assets/{$manifest[$mainJsFile]['file']}"></script>
                <link rel="stylesheet" href="/assets/{$manifest[$mainJsFile]['css'][0]}">
            HTML;
        }
    }
}

function getEnvValue($key) {
    return $_ENV[$key];
}

function getConfig(): object
{
    global $app;
    return (object) include $app->getBasePath() . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR . 'app.php';
}

function setActiveItem( string $link) {
    $requestUriParts = explode('?', $_SERVER['REQUEST_URI']);
    $requestUri = $requestUriParts[0];
    if($requestUri === $link){
        return 'active';
    }
    $uriParts = explode('/', $requestUri);
    if (str_contains($link, $uriParts[1]) && !empty($uriParts[1])) {
        return 'active';
    }
    return '';
}

function filterContentByTag(array $contents, string $tag, string $key = 'tag'): array {
    return array_filter($contents, function($item) use ($key, $tag) {
        if(isset($item[$key][$tag])) {
            return $item;
        }
        return null;
    });
}

function setTagsForInput(array $contents, string $tag): array {
    $categories = [];
    foreach($contents as $item) {
        foreach ($item[$tag] as $key => $name) {
            if(!in_array($key, $item[$tag])) {
                $categories[$key] = $name;
            }
        }
    }
    return $categories;
}