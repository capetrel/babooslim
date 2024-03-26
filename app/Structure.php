<?php


namespace App;


use RegexIterator;

class Structure // extends Page
{

    private array $elementsName;

    /**
     * Structure constructor. The name of the structural elements in an array
     * @param array $elementsName
     */
    public function __construct(array $elementsName)
    {
        $this->elementsName = $elementsName;
    }

    /**
     * Get structural elements define in the YAML file
     * @param RegexIterator $structures
     * @return array
     */
    public function getStructureElements(RegexIterator $structures): array {
        $commonElements = [];
        foreach ($structures as $element) {
            $element = $element[0];
            $structureElement = new Page($element);
            foreach ($this->elementsName as $name) {
                if(!is_null($structureElement->$name)) {
                    $commonElements[$name] = $structureElement->$name;
                }
            }
        }
        return $commonElements;

    }

}