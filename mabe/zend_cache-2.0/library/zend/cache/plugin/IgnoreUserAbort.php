<?php

namespace zend\cache\plugin;

class IgnoreUserAbort extends PluginAbstract
{

    /**
     * Exit if connection aborted
     *
     * @var boolean
     */
    protected $_exitOnAbort = true;

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
            $ret = $this->getAdapter()->set($value, $key, $options);

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
            $ret = $this->getAdapter()->setMulti($keyValuePairs, $options);

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
            $ret = $this->getAdapter()->add($value, $key, $options);

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
            $ret = $this->getAdapter()->addMulti($keyValuePairs, $options);

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
            $ret = $this->getAdapter()->replace($value, $key, $options);

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
            $ret = $this->getAdapter()->replaceMulti($keyValuePairs, $options);

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
            $ret = $this->getAdapter()->remove($key, $options);

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
            $ret = $this->getAdapter()->removeMulti($keys, $options);

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
            $ret = $this->getAdapter()->increment($value, $key, $options);

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
            $ret = $this->getAdapter()->incrementMulti($keyValuePairs, $options);

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
            $ret = $this->getAdapter()->decrement($value, $key, $options);

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
            $ret = $this->getAdapter()->decrementMulti($keyValuePairs, $options);

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

    public function clear($match, array $options = array())
    {
        $ignoreUserAbort = !ignore_user_abort(true);

        try {
            $ret = $this->getAdapter()->clear($match, $options);

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
            $ret = $this->getAdapter()->optimize($options);

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
