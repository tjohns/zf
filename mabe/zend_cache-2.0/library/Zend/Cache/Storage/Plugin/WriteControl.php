<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage\AbstractPlugin;
use \Zend\Cache\UnexpectedValueException;

class WriteControl extends AbstractPlugin
{

    /**
     * Option - remove item(s) on failure
     *
     * @var boolean
     */
    protected $_removeOnFailure = false;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['removeOnFailure'] = $this->getRemoveOnFailure();
        return $options;
    }

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
        $ret = $this->getStorage()->set($value, $key, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValue = $this->get($key, $options);
            if ($checkValue != $value) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $this->remove($key, $options);
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
        $ret = $this->getStorage()->setMulti($keyValuePairs, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValues = $this->getMulti($keyValuePairs, $options);
            $wrongKeys   = null;
            foreach ($checkValues as $checkKey => $checkValue) {
                if (!isset($value[$checkKey]) || $checkValue != $values[$checkKey]) {
                    $wrongKeys[] = $checkKey;
                }
            }

            if ($wrongKeys !== null) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $this->removeMulti($wrongKeys, $options);
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
        $ret = $this->getStorage()->add($value, $key, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValue = $this->get($key, $options);
            if ($checkValue != $value) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $this->remove($key, $options);
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
        $ret = $this->getStorage()->addMulti($keyValuePairs, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValues = $this->getMulti($keyValuePairs, $options);
            $wrongKeys   = null;
            foreach ($checkValues as $checkKey => $checkValue) {
                if (!isset($value[$checkKey]) || $checkValue != $values[$checkKey]) {
                    $wrongKeys[] = $checkKey;
                }
            }

            if ($wrongKeys !== null) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $this->removeMulti($wrongKeys, $options);
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
        $ret = $this->getStorage()->replace($value, $key, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValue = $this->get($key, $options);
            if ($checkValue != $value) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $this->remove($key, $options);
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
        $ret = $this->getStorage()->replaceMulti($keyValuePairs, $options);

        if ($ret === true) {
            $options+= array('validate' => false);
            $checkValues = $this->getMulti($keyValuePairs, $options);
            $wrongKeys   = null;
            foreach ($checkValues as $checkKey => $checkValue) {
                if (!isset($value[$checkKey]) || $checkValue != $values[$checkKey]) {
                    $wrongKeys[] = $checkKey;
                }
            }

            if ($wrongKeys !== null) {
                if ($this->getRemoveOnFailure()) {
                    try {
                        $this->removeMulti($wrongKeys, $options);
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
