<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;

class IgnoreUserAbort extends AbstractPlugin
{

    /**
     * Exit (after writing) if connection aborted
     *
     * @var boolean
     */
    protected $_exitOnAbort = true;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['exitOnAbort'] = $this->getExitOnAbort();
        return $options;
    }

    public function getExitOnAbort()
    {
        return $this->_exitOnAbort;
    }

    public function setExitOnAbort($flag)
    {
        $this->_exitOnAbort = (bool)$flag;
    }

    public function set($value, $key = null, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->set($value, $key, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->setMulti($keyValuePairs, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function add($value, $key = null, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->add($value, $key, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->addMulti($keyValuePairs, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function replace($value, $key = null, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->replace($value, $key, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->replaceMulti($keyValuePairs, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function remove($key = null, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->remove($key, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->removeMulti($keys, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function increment($value, $key = null, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->increment($value, $key, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->incrementMulti($keyValuePairs, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->decrement($value, $key, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->decrementMulti($keyValuePairs, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function clear($match = Storage::MATCH_EXPIRED, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->clear($match, $options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

    public function optimize(array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getStorage()->optimize($options);

            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            return $ret;

        } catch (Exception $e) {
            if ($ignoreUserAbort) {
                if ($this->getExitOnAbort() && connection_aborted()) {
                    exit;
                }
                ignore_user_abort(false);
            }

            throw $e;
        }
    }

}
