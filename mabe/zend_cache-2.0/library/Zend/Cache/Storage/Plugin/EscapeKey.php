<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\InvalidArgumentException;

class EscapeKey extends AbstractPlugin
{

    /**
     * The escape character
     *
     * @var string
     */
    protected $_escapeCharacter = null;

    /**
     * Character table to encode key
     *
     * @var array
     */
    protected $_encodeTabel = array();

    /**
     * Character table to decode key
     *
     * @var array
     */
    protected $_decodeTable = array();

    public function __construct($options)
    {
        parent::__construct($options);

        // set default escape character
        if ($this->_escapeCharacter === null) {
            $this->setEscapeCharacter('+');
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['escapeCharacter'] = $this->getEscapeCharacter();
        return $options;
    }

    public function getEscapeCharacter()
    {
        return $this->_escapeCharacter;
    }

    public function setEscapeCharacter($character)
    {
        $character = (string)$character;
        if (strlen($character) != 1) {
            throw new InvalidArgumentException('The escape character "' . $character . '" have to be a length of 1');
        }

        $capabilities = $this->getStorage()->getCapabilities();
        if (strpos($capabilities['key_disallowed_characters'], $character) !== false) {
            throw new InvalidArgumentException('The escape character "' . $character . '" isn\'t allowed by adapter');
        }

        $this->_escapeCharacter = $character;

        // TODO: init encode/decode tables based of escape character and adapter capabilities
    }

    public function getCapabilities()
    {
        $capabilities = $this->getStorage()->getCapabilities();
        $capabilities['key_disallowed_characters'] = ''; // empty string -> all characters are allowed
        return $capabilities;
    }

    // encode/decode cache ids

}
