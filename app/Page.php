<?php


namespace App;

use cebe\markdown\GithubMarkdown;
use Jenssegers\Blade\Blade;
use Webuni\FrontMatter\FrontMatter;

#[\AllowDynamicProperties]
class Page
{

    private string $path;
    private $parsedData;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Magic method to not have to use getter: $page->get('property'), mais : page->property
     * @param string $property
     * @return mixed string|array|null
     */
    public function __get(string $property)
    {
        if(!property_exists($this, $property)) {
            try {
                $this->$property = $this->get($property);
            } catch (\Exception $e) {
                echo $e->getMessage();
                $this->$property =  null;
            }

        }
        return $this->$property;
    }

    /**
     * Returns the property's content if it exists
     * @param string $key
     * @return mixed string|array|null
     */
    public function get(string $key)
    {
        if(is_null($this->parsedData)) {
            $this->parsedData = (new FrontMatter())->parse(file_get_contents($this->path));
        }
        if ($key === 'content') {
            $filenameParts = explode('.', pathinfo($this->path, PATHINFO_FILENAME));
            $type = end($filenameParts);
            $method = "parse".ucfirst($type);
            return $this->$method($this->parsedData->getContent());
        }
        if(!isset($this->parsedData->getDataWithContent()[$key])) {
            // throw new \Exception("La propriété : $key, n'existe pas dans le fichier yaml" . $this->path);
            return null;
        }
        if(is_array($this->parsedData->getDataWithContent()[$key])) {
            return $this->parsedData->getDataWithContent()[$key];
        }
        return nl2br($this->parsedData->getDataWithContent()[$key]);
    }

    /**
     * Convert file path to URL
     * @return string L'URL of the YAML file
     */
    public function getUrl(): string
    {
        $path = str_replace(CONTENT_PATH, '', $this->path);
        $path = current(explode('.', $path));
        $path = str_replace('index', '', $path);
        $path = str_replace('\\', '/', $path);
        return $path;
    }

    /**
     * Render the Blade view corresponding to the layout defined in the YAML file or the default view
     * @param array|null $structure Page's structural elements
     * @param array|null $result Data posted if there is any
     * @return string
     */
    public function render(array $structure = null, array $result = null): string
    {
        $page = $this;
        if(is_null($this->layout)) {
            $layout = 'default';
        } else {
            $layout = $page->layout;
        }

        $blade = new Blade(VIEWS_PATH, BASE_PATH . DIRECTORY_SEPARATOR. 'cache');
        return $blade->render('pages.'.$layout, compact('page', 'structure', 'result'));
    }

    /**
     * @param string $content The Markdown to convert
     * @return string HTML content
     */
    private function parseMarkdown (string $content): string
    {
        $parser = new GithubMarkdown();
        $parser->enableNewlines = true;
        return $parser->parse($content);
    }

    /**
     * @param string $content HTML content
     * @return string HTML content
     */
    private function parseHtml (string $content): string {
        return $content;
    }


}