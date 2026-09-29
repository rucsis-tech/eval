<?php
namespace classes;
class XMLClass implements Iterator {

    private $position = 0;
    private $parent = null;

    private $children = [];
    private $attributes = [];
    private $text = '';
    private $name = '';

    function __construct($simpleXML, $parent = null) {
        $this->parent = $parent;
        $this->position = 0;
        $this->setSimpleXML($simpleXML);

    }

    public function setSimpleXML($simpleXML) {
        $this->name = $simpleXML->getName();
        $this->children = [];
        foreach ($simpleXML->children() as $simpleChild) {
            $name = $simpleChild->getName();
            $content = new XMLClass($simpleChild, $this);
            $this->children[] = $content;
        }

        $attributes = [];
        foreach ($simpleXML->attributes() as $key => $value) {
            $attributes[$key] = $value->__toString();
        }
        $this->attributes = $attributes;

        if ($attributes) {
            $this->attributes = $attributes;
        }

        $this->text = $simpleXML->__toString();
    }

    public function __get($name) {

        if ($name == '_name') {
            return $this->name;
        }
        if ($name == '_text') {
            return $this->text;
        }
        if ($name == '_attributes') {
            return $this->attributes;
        }
        if ($name == '_children') {
            return $this->children;
        }

        foreach ($this->children as $child) {
            if ($child->_name == $name) {
                if (count($child->_attributes) == 0 && count($child->_children) == 0) {
                    return $child->_text;
                } else {
                    return $child;
                }
            }
        }
        return null;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->parent->_getChild($this->name, $this->position);
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {

        $target = $this->parent->_getChild($this->name, $this->position);
        return ($target != null);
    }

    protected function _getChild($name, $position) {

        foreach ($this->children as $child) {
            if ($child->_name == $name) {
                if ($position-- == 0) {
                    return $child;
                }
            }
        }
        return null;

    }


}