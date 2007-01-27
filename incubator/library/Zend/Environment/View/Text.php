<?php

/**
 * Extra config settings:-
 * 'width'     : Width (in characters) of output
 * 'inset'     : Inset width (in characters) of section properties
 * 'eol'       : End of line character (defaults to LF)
 * 'underline' : The string used to underline the heading
 * 'delimiter' : The string used between the property and the value
 *
 */
define('ZF_ENVIRONMENT_WIDTH',      80);
define('ZF_ENVIRONMENT_INSET',      5);
define('ZF_ENVIRONMENT_EOL',        "\n");
define('ZF_ENVIRONMENT_UNDERLINE',  '=');
define('ZF_ENVIRONMENT_DELIMITER',  ' => ');

/**
 * Internal calculations
 */
define('ZF_ENVIRONMENT_COLWIDTH',   ZF_ENVIRONMENT_WIDTH - 10 - 1 - ZF_ENVIRONMENT_INSET);
define('ZF_ENVIRONMENT_PADDING',    ZF_ENVIRONMENT_WIDTH - ZF_ENVIRONMENT_COLWIDTH);
define('ZF_ENVIRONMENT_TITLEWIDTH', 10 + ZF_ENVIRONMENT_INSET);

foreach ($this->environment as $this->section) {
    echo $this->render('Text/' . ucwords($this->section->getType()) . '.php');
}
