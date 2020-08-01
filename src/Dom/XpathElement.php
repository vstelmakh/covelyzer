<?php

declare(strict_types=1);

namespace VStelmakh\Covelyzer\Dom;

class XpathElement
{
    /**
     * @var \DOMXPath
     */
    private $domXpath;

    /**
     * @var \DOMElement
     */
    private $domElement;

    /**
     * @param \DOMXPath $domXpath
     * @param \DOMElement $domElement
     */
    public function __construct(\DOMXPath $domXpath, \DOMElement $domElement)
    {
        if ($domXpath->document !== $domElement->ownerDocument) {
            throw new \InvalidArgumentException('DOMXpath and DOMElement should have same owner document');
        }

        $this->domXpath = $domXpath;
        $this->domElement = $domElement;
    }

    /**
     * Create new XpathElement with same DOMXpath
     *
     * @param \DOMElement $domElement
     * @return self
     */
    public function createElement(\DOMElement $domElement): self
    {
        return new self($this->domXpath, $domElement);
    }

    /**
     * Do xpath query on current DOMElement
     *
     * @param string $xpath
     * @return \DOMNodeList<\DOMElement>
     */
    public function xpath(string $xpath): \DOMNodeList
    {
        $nodeList = $this->domXpath->query($xpath, $this->domElement);
        return $nodeList ?: new \DOMNodeList();
    }

    /**
     * @return string
     */
    public function getTagName(): string
    {
        return $this->domElement->tagName;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getAttribute(string $name): ?string
    {
        $hasAttribute = $this->domElement->hasAttribute($name);
        return $hasAttribute ? $this->domElement->getAttribute($name) : null;
    }

    /**
     * @return \DOMNode|null
     */
    public function getParent(): ?\DOMNode
    {
        return $this->domElement->parentNode;
    }
}
