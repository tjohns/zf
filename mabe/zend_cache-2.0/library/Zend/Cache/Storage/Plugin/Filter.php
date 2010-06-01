<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Filter\FilterInterface;

class Filter extends AbstractPlugin
{

    /**
     * The write filter
     *
     * @var Zend\Filter\FilterInterface
     */
    protected $_writeFilter = null;

    /**
     * The read filter
     *
     * @var Zend\Filter\FilterInterface
     */
    protected $_readFilter = null;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['writeFilter'] = $this->getWriteFilter();
        $options['readFilter']  = $this->getReadFilter();
        return $options;
    }

    /**
     * Get the write filter
     *
     * @return Zend\Filter\FilterInterface
     */
    public function getWriteFilter()
    {
        if ($this->_writeFilter === null) {
            // TODO: empty filter
            $this->_writeFilter = new \Zend\Filter\EmptyFilter();;
        }

        return $this->_writeFilter;
    }

    /**
     * Get the write filter
     *
     * @param Zend\Filter\FilterInterface
     * @return Zend\Cache\Storage\Plugin\Filter
     */
    public function setWriteFilter(FilterInterface $filter)
    {
        $this->_writeFilter = $filter;
        return $this;
    }

    /**
     * Get the read filter
     *
     * @return Zend\Filter\FilterInterface
     */
    public function getReadFilter()
    {
        if ($this->_readFilter === null) {
            // TODO: empty filter
            $this->_readFilter = new \Zend\Filter\EmptyFilter();;
        }

        return $this->_readFilter;
    }

    /**
     * Get the read filter
     *
     * @param Zend\Filter\FilterInterface
     * @return Zend\Cache\Storage\Plugin\Filter
     */
    public function setReadFilter(FilterInterface $filter)
    {
        $this->_readFilter = $filter;
        return $this;
    }

    public function set($value, $key = null, array $options = array())
    {
        $value = $this->getWriteFilter()->filter($value);
        $this->getStorage()->set($value, $key, $options);
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $filter = $this->getWriteFilter();
        foreach ($keyValuePairs as &$value) {
            $value = $filter->filter($value);
        }

        return $this->getStorage()->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array())
    {
        $value = $this->getWriteFilter()->filter($value);
        $this->getStorage()->add($value, $key, $options);
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $filter = $this->getWriteFilter();
        foreach ($keyValuePairs as &$value) {
            $value = $filter->filter($value);
        }

        return $this->getStorage()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array())
    {
        $value = $this->getWriteFilter()->filter($value);
        $this->getStorage()->replace($value, $key, $options);
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $filter = $this->getWriteFilter();
        foreach ($keyValuePairs as &$value) {
            $value = $serializer->filter($value);
        }

        return $this->getStorage()->replaceMulti($keyValuePairs, $options);
    }

    public function get($key = null, array $options = array())
    {
        $rs = $this->getStorage()->get($key, $options);
        return $this->getReadFilter()->filter($rs);
    }

    public function getMulti(array $keys, array $options = array())
    {
        $rsList = $this->getStorage()->getMulti($keys, $options);

        $filter = $this->getReadFilter();
        foreach ($rsList as &$value) {
            $value = $filter->filter($value);
        }

        return $rsList;
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM)
    {
        $item = $this->getStorage()->fetch($fetchStyle);
        if ($item) {
            $filter = $this->getReadFilter();
            switch ((int)$fetchStyle) {
                case Storage::FETCH_NUM:
                    if (isset($item[1])) {
                        $item[1] = $filter->filter($item[1]);
                    }
                    break;

                case Storage::FETCH_ASSOC:
                    if (isset($item['value'])) {
                        $item['value'] = $filter->filter($item[1]);
                    }
                    break;

                case Storage::FETCH_BOTH:
                    if (isset($item[1])) {
                        $item[1] = $filter->filter($item[1]);
                        $item['value'] = &$item[1];
                    }
                    break;

                case Storage::FETCH_OBJ:
                    if (isset($item->value)) {
                        $item->value = $filter->filter($item->value);
                    }
                    break;

                default:
                    throw new RuntimeException("Unknown fetch style '{$fetchStyle}'");
            }
        }

        return $item;
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        $rs = array();
        while ( ($item = $this->fetch($fetchStyle)) ) {
            $rs[] = &$item;
        }
        return $rs;
    }

    public function increment($value, $key = null, array $options = array())
    {
        $stored = $this->get($key, $options);
        $this->set((int)$stored + (int)$value, $options);
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $storedList = $this->getMulti(array_keys($keyValuePairs), $options);
        foreach ($keyValuePairs as $key => &$value) {
            $stored = isset($storedList[$key]) ? (int)$storedList[$key] : 0;
            $value  = $stored + (int)$value;
        }
        return $this->setMulti($keyValuePairs, $options);
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $stored = $this->get($key, $options);
        $this->set((int)$stored - (int)$value, $options);
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $storedList = $this->getMulti(array_keys($keyValuePairs), $options);
        foreach ($keyValuePairs as $key => &$value) {
            $stored = isset($storedList[$key]) ? (int)$storedList[$key] : 0;
            $value  = $stored - (int)$value;
        }
        return $this->setMulti($keyValuePairs, $options);
    }

}
