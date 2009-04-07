<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include Console classes
 */
require_once 'Zend/Console/Context/Interface.php';
require_once 'Zend/Console/Exception.php';

/**
 * Include Build classes
 */
require_once 'Zend/Console/Factory.php';

/**
 * Include GetOpt classes
 */
require_once 'Zend/Console/Getopt.php';

/**
 * @category   Zend
 * @package    Zend_Console
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Console implements Zend_Console_Context_Interface
{
    const ERR_PATH_SEPARATOR = ': ';

    const MF_ACTION_TYPE                = 'action';
    const MF_RESOURCE_TYPE              = 'resource';

    protected $_native_exec = null;
    protected $_php_exec = null;
    protected $_verbosity;

    public function init(array $argv = array(), $verbosity = 0)
    {
        $this->_verbosity = $verbosity;

        /* First element is the php script name. Store it for debugging. */
        $this->_php_exec = array_shift($argv);

        /* Second element is just the executable we called to run ZF commands. Store it for printing errors/usage. */
        $this->_native_exec = array_shift($argv);

        $opts = $this->getOptions()->addArguments($argv);
        $opts->parse();

        // Shortcut to verbosity option so that we can display more earlier
        if(isset($opts->verbosity))
        {
            $this->_verbosity =  $opts->verbosity;  
        }

        // Shortcut to help option so that no arguments have to be specified
        if(isset($opts->help))
        {
            $this->printHelp();
            return null;
        }

        try
        {
            $actionName = array_shift($argv);
            $context = Zend_Build_Manifest::getInstance()->getContext(self::MF_ACTION_TYPE, $actionName);
            $config = $this->_parseParams($context, $argv);
            $action = new $context->class;
            $action.setConfig($config);
            $action.configure(); 
        } catch (Zend_Console_Exception $e) {
            throw $e->prependUsage($this->getUsage());
        }

        return $this;
    }

    /**
     * @param string $command_string
     */
    public function execute()
    {
        if(!$command.validate())
        {
            printError(array($cli_exec, $command.getName()), 'Incorrect command syntax');
            print $command.shortUsage();
            exit(1);
        }
        try
        {
            $command.execute();
        }
        catch(Zend_Console_CommandExecutionException $e)
        {
            printError(array($cli_exec, $e.getErrPath(), $e.getErrMsg()));
            exit(1);
        }

        /**
         * As far as we know, everything is A-OK
         */
        exit(0);
    }

    public function getUsage()
    {
        return $this->_native_exec . ' <options> ' . ' <command> ' . ' <command options> ' . ' <resources> ';
    }

    public function getOptions()
    {
        $opts = new Zend_Console_Getopt(
          array(
            'verbose|v=i'    => 'This option specifies verbose output at some level from 1-3',
            'help|h' => 'This option prints out help for the command'
          ),
          array() // We'll add the arguments later
        );

        // Don't parse the entire line here
        $opts->setOption('parseAll', false);

        return $opts;
    }

    public function printHelp()
    {
        print("HELPPPPPP!\n");
    }

    private function _parseParams($context, $argv)
    {
       $opts = new Zend_Console_Getopt();
       $aliases = array();
       foreach($context->attribute as $attribute)
       {
           // Key should be the rule string, value is the long usage
           $opts->addRules(array($attribute->getopt, $attribute->usage));
       }
       $opts->addArguments($argv);
       return $opts->parse();
    }

}