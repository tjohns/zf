<?php

namespace zend\cache\plugin;
use \zend\cache\UnexpectedValueException as UnexpectedValueException;

class WriteControl extends PluginAbstract
{

    /**
     * Option - remove item(s) on failure
     *
     * @var boolean
     */
    protected $_removeOnFailure = false;

    /**
     * Get option - remove item(s) on failure
     *
     * @return boolean
     */
    public function getRemoveOnFailure()
    {
        return $this->_removeOnFailure;
    }

    /**
     * Set option - remove item(s) on failure
     *
     * @param boolean $flag
     * @return zend\cache\plugin\WriteControl
     */
    public function setRemoveOnFailure($flag)
    {
        $this->_removeOnFailure = (bool)$flag;
        return $this;
    }

    public function set($value, $key = null, array $options = array())
    {
        $adapter = $this->getAdapter();
        $ret = $adapter->set($value, $key, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValue = $adapter->get($key, $options);
            if ($checkValue != $value) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $adapter->remove($key, $options);
                    } catch (Exception $e) {
                        // don't throw exceptions if remove failed
                    }
                }

                throw new UnexpectedValueException('Written and read value doesn\'t match');
            }
        }

        return $ret;
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $adapter = $this->getAdapter();
        $ret = $adapter->setMulti($keyValuePairs, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValues = $adapter->getMulti($keyValuePairs, $options);
            $wrongKeys   = null;
            foreach ($checkValues as $checkKey => $checkValue) {
                if (!isset($value[$checkKey]) || $checkValue != $values[$checkKey]) {
                    $wrongKeys[] = $checkKey;
                }
            }

            if ($wrongKeys !== null) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $adapter->removeMulti($wrongKeys, $options);
                    } catch (Exception $e) {
                        // don't throw exceptions if remove failed
                    }
                }

                throw new UnexpectedValueException(
                    'Written and read values doesn\'t match, keys: ' . implement(', ', $wrongKeys)
                );
            }
        }

        return $ret;
    }

    public function add($value, $key = null, array $options = array())
    {
        $adapter = $this->getAdapter();
        $ret = $adapter->add($value, $key, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValue = $adapter->get($key, $options);
            if ($checkValue != $value) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $adapter->remove($key, $options);
                    } catch (Exception $e) {
                        // don't throw exceptions if remove failed
                    }
                }

                throw new UnexpectedValueException('Written and read value doesn\'t match');
            }
        }

        return $ret;
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $adapter = $this->getAdapter();
        $ret = $adapter->addMulti($keyValuePairs, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValues = $adapter->getMulti($keyValuePairs, $options);
            $wrongKeys   = null;
            foreach ($checkValues as $checkKey => $checkValue) {
                if (!isset($value[$checkKey]) || $checkValue != $values[$checkKey]) {
                    $wrongKeys[] = $checkKey;
                }
            }

            if ($wrongKeys !== null) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $adapter->removeMulti($wrongKeys, $options);
                    } catch (Exception $e) {
                        // don't throw exceptions if remove failed
                    }
                }

                throw new UnexpectedValueException(
                    'Written and read values doesn\'t match, keys: ' . implement(', ', $wrongKeys)
                );
            }
        }

        return $ret;
    }

    public function replace($value, $key = null, array $options = array())
    {
        $adapter = $this->getAdapter();
        $ret = $adapter->replace($value, $key, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValue = $adapter->get($key, $options);
            if ($checkValue != $value) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $adapter->remove($key, $options);
                    } catch (Exception $e) {
                        // don't throw exceptions if remove failed
                    }
                }

                throw new UnexpectedValueException('Written and read value doesn\'t match');
            }
        }

        return $ret;
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $adapter = $this->getAdapter();
        $ret = $adapter->replaceMulti($keyValuePairs, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValues = $adapter->getMulti($keyValuePairs, $options);
            $wrongKeys   = null;
            foreach ($checkValues as $checkKey => $checkValue) {
                if (!isset($value[$checkKey]) || $checkValue != $values[$checkKey]) {
                    $wrongKeys[] = $checkKey;
                }
            }

            if ($wrongKeys !== null) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $adapter->removeMulti($wrongKeys, $options);
                    } catch (Exception $e) {
                        // don't throw exceptions if remove failed
                    }
                }

                throw new UnexpectedValueException(
                    'Written and read values doesn\'t match, keys: ' . implement(', ', $wrongKeys)
                );
            }
        }

        return $ret;
    }

}
