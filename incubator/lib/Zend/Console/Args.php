<?php
/*****************************************************************************/
/* IonZoft Services Platform                                                 */
/* Copyright (C) 2001-2006 IonZoft, Inc.  All Rights Reserved.               */
/*                                                                           */
/* Mail:  IonZoft, Inc., PO Box 204, Bellwood, PA 16617, USA                 */
/* Email: info@ionzoft.com                                                   */
/* Web:   http://www.ionzoft.com                                             */
/*****************************************************************************/

//Notes:
//  This file consists of several classes that were taken from the IZSP project.
//  Normally, this would be split into several files, but for the sake of 
//  this example, it was placed into one file.
//  This is new code and has not been tested/debugged to a great extent.

//  See the accompanying example files for usage information.
//  Enjoy.

//Classes:
//  Zend_CLI                    - The base class for Command Line Interface functionality
//  Zend_CLI_GetOpt             - The class used to read in command line options
//  Zend_CLI_GetOpt_Option      - Used to represent command line options
//  Zend_CLI_GetOpt_Parameter   - Used to represent command line parameters
//  Zend_BitFlag                - A utility class for working with bitwise flags

/*****************************************************************************/
/* The purpose of this module is to read and parse command line options      */
/* provided to a php script running under the CLI SAPI                       */
/*                                                                           */
/* It is designed to read options in the following formats:                  */
/*   -a							Short                                        */
/*   --option-a					Long                                         */
/*   --option-a=parameter		Long with Argument                           */
/*   -abc						Short with Multiple                          */
/*   -- 						everything after this is arguments           */
/*                                                                           */
/* In addition, this module is designed to read parameters passed (usually)  */
/* to the end of the specified command.                                      */
/* specified command.                                                        */
/*                                                                           */
/* - Options can have a min num of occurances, and a max num of occurance    */
/* - Options can be aliased.                                                 */
/* - Arguments to options can be optional, forbidden, or required.           */
/*                                                                           */
/*****************************************************************************/


###################################################################################################
###################################################################################################
###################################################################################################

//Because this is not running from witin the framework
class Zend_Exception extends Exception
{}


class Zend_CLI
{
	//Define module constants
	const ArgOptional	 	= 1;
	const ArgRequired 		= 2;
	const ArgNone		 	= 4;

	const ParamOptional	 	= 8;
	const ParamRequired 	= 16;

	const TypeInt 			= 32;
	const TypeFloat			= 64;
	const TypeString		= 128;
	const TypeLoose	 		= 256;

}

###################################################################################################
###################################################################################################
###################################################################################################

class Zend_CLI_GetOpt
{
	//Stores the allowable command line options in the following format:
	// 'optionname' => Zend_CLI_GetOpt_Option object
	// 'aliasname' => 'optionname'
	protected $_Opt = array();

	//Stores the allowable command line parameters in the following format:
	// nOffset => Zend_CLI_GetOpt_Parameter object
	// 'aliasname' => nOffset
	protected $_Par = array();

	//Stores an array of error messages
	protected $_ErrorMessage = array();

	###############################################################################################
	function __construct()
	{
	}
	
	###############################################################################################
	public function AddOption($sNames, $nMin, $nMax, $nOpts)
	{
		//Convert parameters to correct types
		$sNames = (string) $sNames;
		$nMin = (integer) $nMin;
		$nMax = (integer) $nMax;
		$nOpts = (integer) $nOpts;

		//Split out the aliases
		$aAliases = explode('|', $sNames);
		foreach($aAliases as $s)
		{
			if(isset($this->_Opt[$s]))
				throw new Zend_Exception("The option name/alias '$s' has already been defined.");
			if(! preg_match('/^[0-9a-zA-Z][0-9a-zA-Z-]*$/', $s))
				throw new Zend_Exception("The option name/alias '$s' must not begin with a dash and only contain a-zA-Z0-9 and dashes.");
		}
		
		//Get the option name
		$sName = array_shift($aAliases);

		//$nMin must be >= 0
		if($nMin < 0)
			throw new Zend_Exception("The second argument must be >= 0");
		
		//$nMax must be >= $nMin
		if($nMax < $nMin)
			throw new Zend_Exception("The third argument must be >= the second argument");
		
		//The option flags must consist of one of (Zend_CLI::ArgRequired, Zend_CLI::ArgOptional, Zend_CLI::ArgNone) 
		if(! Zend_BitFlag::OneOf($nOpts, Zend_CLI::ArgRequired, Zend_CLI::ArgOptional, Zend_CLI::ArgNone))
			throw new Zend_Exception("For option '$sNames', the option flags must consist of one of (Zend_CLI::ArgRequired, Zend_CLI::ArgOptional, Zend_CLI::ArgNone)");
		
		//If an argument is possible, then the argument type must be specified.
		if(Zend_BitFlag::OneOf($nOpts, Zend_CLI::ArgRequired, Zend_CLI::ArgOptional))
		{
			if(! Zend_BitFlag::OneOf($nOpts, Zend_CLI::TypeInt, Zend_CLI::TypeFloat, Zend_CLI::TypeString, Zend_CLI::TypeLoose))
				throw new Zend_Exception("For option '$sNames', the option flags must specify the type of argument (Zend_CLI::TypeInt, Zend_CLI::TypeFloat, Zend_CLI::TypeString, Zend_CLI::TypeLoose)");
		}
		//Otherwise, the argument type must not be specified
		else
		{
			if(Zend_BitFlag::SomeOf($nOpts, Zend_CLI::TypeInt, Zend_CLI::TypeFloat, Zend_CLI::TypeString, Zend_CLI::TypeLoose))
				throw new Zend_Exception("For option '$sNames', the option flags must not specify the type of argument (Zend_CLI::TypeInt, Zend_CLI::TypeFloat, Zend_CLI::TypeString, Zend_CLI::TypeLoose)");
		}

		//Taking advantage of the reference features of PHP
		$oOpt = new Zend_CLI_GetOpt_Option($sName, $nOpts, $nMin, $nMax);

		//Add this option to the options array
		$this->_Opt[$sName] = $oOpt;
		
		foreach($aAliases as $sAlias)
		{
			$this->_Opt[$sAlias] = $sName;
		}
	}


	###############################################################################################
	public function AddParameter($sNames, $nOpts)
	{
		//Convert parameters to correct types
		$sNames = (string) $sNames;
		$nOpts = (integer) $nOpts;

		//Split out the aliases
		$aAliases = explode('|', $sNames);
		foreach($aAliases as $s)
		{
			if(isset($this->_Par[$s]))
				throw new Zend_Exception("The parameter name/alias '$s' has already been defined.");
			if(preg_match('/^[0-9]*$/', $s))
				throw new Zend_Exception("The parameter name/alias '$s' must not represent an integer or be empty.");
		}

		//The parameter type must be specified
		if(! Zend_BitFlag::OneOf($nOpts, Zend_CLI::TypeInt, Zend_CLI::TypeFloat, Zend_CLI::TypeString, Zend_CLI::TypeLoose))
			throw new Zend_Exception("For parameter '$sNames', the option flags must specify the type of parameter (Zend_CLI::TypeInt, Zend_CLI::TypeFloat, Zend_CLI::TypeString, Zend_CLI::TypeLoose)");

		//Optional or required must be specified
		if(! Zend_BitFlag::OneOf($nOpts, Zend_CLI::ParamOptional, Zend_CLI::ParamRequired))
			throw new Zend_Exception("For parameter '$sNames', the option flags must specify that the parameter is optional or required (Zend_CLI::ParamOptional, Zend_CLI::ParamRequired)");

		//Get the offset of the next parameter.  
		$nOffset = 0;
		foreach(array_keys($this->_Par) as $k)
			if(is_int($k))
				$nOffset++;

		//Create the parameter object
		$oPar = new Zend_CLI_GetOpt_Parameter($nOffset, $nOpts);
		
		//Add the parameter object to the option array by parameter offset
		$this->_Par[$nOffset] = $oPar;
		
		foreach($aAliases as $sAlias)
			$this->_Par[$sAlias] = $nOffset;
	}


	###############################################################################################
	//If you do not pass an argv array, Parse() will automatically look at $_SERVER['argv']
	//  This parameter SHOULD NOT CONTAIN THE PROGRAM NAME
	//Returns true on success, or false on failure.
	//  On failure, there will be error messages available from the GetErrors() method
	public function Parse($ARGV = NULL)
	{
		//0. Init
		$this->_ErrorMessage = array();
		
		//1. Get the $ARGV array
		if(! is_array($ARGV))
			$ARGV = array_slice($_SERVER['argv'], 1);
	
		//2. Parse $ARGV into tokens
		//Token types: o=>option, oa=>option with argument, p=>parameter
		//Array elements: [0]=>tokentype [1]=>tokenvalue
		$aTokens = array();
		$bAllPar = FALSE;
		foreach($ARGV as $nKey => $sArg)
		{
			//-- has been specified, everything after here is a parameter
			if($bAllPar)
			{
				$aTokens[] = array('p', $sArg);
			}
			//Long with argument
			elseif(preg_match('/^--([0-9a-zA-Z-]+)=(.*)$/', $sArg, $aRegs))
			{
				$aTokens[] = array('oa', $aRegs[1], $aRegs[2]);
			}
			//Long without argument
			elseif(preg_match('/^--([0-9a-zA-Z-]+)$/', $sArg, $aRegs))
			{
				$aTokens[] = array('o', $aRegs[1]);
			}
			//Short (single/multiple)
			elseif(preg_match('/^-([0-9a-zA-Z]+)$/', $sArg, $aRegs))
			{
				for($i=0; $i<strlen($aRegs[1]); $i++)
					$aTokens[] = array('o', $aRegs[1]{$i});
			}
			//-- (everything after is a parameter)
			elseif(preg_match('/^--$/', $sArg, $aRegs))
			{
				$bAllPar = TRUE;
			}
			//Must be a parameter
			else
			{
				$aTokens[] = array('p', $sArg);
			}
		}

		//3. Now we have valid tokens, it's time to compile
		
		$nOffset = -1;
		foreach($aTokens as $aToken) switch($aToken[0])
		{
			///////////////////////////////////////////////////////////////////////////////////
			//Working with an Option (no argument)
			case 'o':
				//Get the option
				if($oCurrentOpt = $this->GetOptionObject($aToken[1], TRUE))
				{
					if($oCurrentOpt->Opts & Zend_CLI::ArgRequired)
					{
						$this->SetError("Option '{$aToken[1]}' requires an argument.");
					}
					else
					{
						//Default value is TRUE when there is no argument
						$oCurrentOpt->AddValue(TRUE);
					}
				}
				else
				{
					$this->SetError("Option '{$aToken[1]}' is not valid.");
				}
				break;
			///////////////////////////////////////////////////////////////////////////////////
			//Working with an Option (with argument)
			case 'oa':
				//Get the option
				if($oCurrentOpt = $this->GetOptionObject($aToken[1], TRUE))
				{
					if($oCurrentOpt->Opts & Zend_CLI::ArgNone)
					{
						$this->SetError("Option '{$aToken[1]}' does not accept an argument.");
					}
					else
					{
						$oCurrentOpt->AddValue($aToken[2]);
					}
				}
				else
				{
					$this->SetError("Option '{$aToken[1]}' is not valid.");
				}
				break;
			///////////////////////////////////////////////////////////////////////////////////
			//Working with a parameter
			case 'p':
				$nOffset++;

				//Get the parameter
				if($oCurrentPar = $this->GetParameterObject($nOffset, TRUE))
				{
					$oCurrentPar->SetValue($aToken[1]);
				}
				else
				{
					$this->SetError("Parameter offset $nOffset is not valid.");
				}
				break;
		}
	
		
		//4. Scan for errors
		foreach($this->_Opt as $oOpt) if($oOpt instanceof Zend_CLI_GetOpt_Option)
		{
			foreach($oOpt->GetErrors() as $e)
			{
				$this->SetError($e);
			}
		}
		
		foreach($this->_Par as $oPar) if($oPar instanceof Zend_CLI_GetOpt_Parameter)
		{
			foreach($oPar->GetErrors() as $e)
			{
				$this->SetError($e);
			}
		}

		//Ok, if we have any errors, then return false.  Otherwise return true.
		return (count($this->_ErrorMessage) == 0);
	}

	###############################################################################################
	//Get an option value
	public function GetOptionValue($sNameOrAlias)
	{
		return $this->GetOptionObject($sNameOrAlias)->GetValue();
	}

	//Get an option values
	public function GetOptionValues($sNameOrAlias)
	{
		return $this->GetOptionObject($sNameOrAlias)->GetValues();
	}

	//Get an parameter value
	public function GetParameterValue($nOffsetOrAlias)
	{
		return $this->GetParameterObject($nOffsetOrAlias)->GetValue();
	}
	
	
	###############################################################################################
	//Get an Option Object by name/alias
	protected function GetOptionObject($sNameOrAlias, $bNoError = FALSE)
	{
		if(! isset($this->_Opt[$sNameOrAlias]))
		{
			if($bNoError)
				return FALSE;
			else
				throw new Zend_Exception("The option name/alias '$sNameOrAlias' does not exist.");
		}

		//Is this the object itself?
		if($this->_Opt[$sNameOrAlias] instanceof Zend_CLI_GetOpt_Option)
		{
			//Return the object
			return $this->_Opt[$sNameOrAlias];
		}
		//This is an alias that points to the name of the option.
		elseif(is_scalar($this->_Opt[$sNameOrAlias]))
		{
			//Look up the object and return it
			return $this->_Opt[$this->_Opt[$sNameOrAlias]];
		}
		
		//If we made it to here... It's not found
		if($bNoError)
			return FALSE;
		else
			throw new Zend_Exception("The option name/alias '$sNameOrAlias' does not exist.");

	}
	
	###############################################################################################
	//Get a parameter by name/alias
	protected function GetParameterObject($sNameOrAlias, $bNoError = FALSE)
	{
		if(! isset($this->_Par[$sNameOrAlias]))
		{
			if($bNoError)
				return FALSE;
			else
				throw new Zend_Exception("The parameter name/alias '$sNameOrAlias' does not exist.");
		}

		//Is this the object itself?
		if($this->_Par[$sNameOrAlias] instanceof Zend_CLI_GetOpt_Parameter)
		{
			//Return the object
			return $this->_Par[$sNameOrAlias];
		}
		//This is an alias that points to the name of the parameter.
		else
		{
			//Look up the object and return it
			return $this->_Par[$this->_Par[$sNameOrAlias]];
		}
	}
	
	###############################################################################################
	//SetError
	protected function SetError($sMessage)
	{
		$this->_ErrorMessage[] = $sMessage;
	}
	
	public function GetErrors()
	{
		return $this->_ErrorMessage;
	}
	
}


###################################################################################################
###################################################################################################
###################################################################################################


class Zend_CLI_GetOpt_Option
{
	public $Name;
	public $Opts;
	public $Min;
	public $Max;
	protected $Values = array();
	
	function __construct($sName, $nOpts, $nMin, $nMax)
	{
		$this->Name = $sName;
		$this->Opts = $nOpts;
		$this->Min = $nMin;
		$this->Max = $nMax;
	}
	
	public function AddValue($eValue)
	{
		//Adds an alias and and occurance 
		$this->Values[] = $eValue;
	}
	
	public function GetValue()
	{
		if(count($this->Values) == 0)
			return FALSE;

		//Return the last set value
		return end($this->Values);
	}
	
	public function GetValues()
	{
		//return the whole list of values
		return $this->Values;
	}

	public function GetErrors()
	{
		//0. Init
		$aErrors = array();
		
		//1. Check min and max
		if((count($this->Values) < $this->Min) || (count($this->Values) > $this->Max))
			$aErrors[] = "Option '{$this->Name}' is required between {$this->Min} and {$this->Max} times. " . count($this->Values) . " instances found.";
		
		//2. check type
		
		//3. return errors
		return $aErrors;
	}
}

###################################################################################################
###################################################################################################
###################################################################################################

class Zend_CLI_GetOpt_Parameter
{
	public $Offset;
	public $Opts;
	public $Value = FALSE;
	
	function __construct($nOffset, $nOpts)
	{
		$this->Offset = $nOffset;
		$this->Opts = $nOpts;
	}

	public function SetValue($eValue)
	{
		//Adds an alias and and occurance 
		$this->Value = $eValue;
	}

	public function GetValue()
	{
		return $this->Value;
	}

	public function GetErrors()
	{
		//0. Init
		$aErrors = array();
		
		//1. Check if required
		if(($this->Value === FALSE) && ($this->Opts & Zend_CLI::ParamRequired))
			$aErrors[] = "Parameter offset {$this->Offset} is required.";

		//1. check type
		
		//2. return errors
		return $aErrors;
	}
}

###################################################################################################
###################################################################################################
###################################################################################################

class Zend_BitFlag
{
	###############################################################################################
	//This function takes an integer as the first parameter, and then returns true if exactly
	//  one of the following parameters (bitwise flags) exists in the first parameter.
	public static function OneOf(/* $nOpts, $nOpt01[, $nOpt02[, ...]]*/)
	{
		$aArgs = func_get_args();
		$nOpts = array_shift($aArgs);
		
		if(count($nOpts) == 0)
			throw new Zend_Exception("Argument 2 missing.  At least one bitwise flag must be passed.");
			
		$n = 0;
		foreach($aArgs as $nOpt)
			$n += ($nOpts & $nOpt ? 1 : 0);
		
		return $n == 1;
	}

	###############################################################################################
	//This function takes an integer as the first parameter, and then returns true if one or more
	//  of the following parameters (bitwise flags) exists in the first parameter.
	public static function SomeOf(/* $nOpts, $nOpt01[, $nOpt02[, ...]]*/)
	{
		$aArgs = func_get_args();
		$nOpts = array_shift($aArgs);
		
		if(count($nOpts) == 0)
			throw new Zend_Exception("Argument 2 missing.  At least one bitwise flag must be passed.");
			
		$n = 0;
		foreach($aArgs as $nOpt)
			$n += ($nOpts & $nOpt ? 1 : 0);
		
		return $n >= 1;
	}

	###############################################################################################
	//This function takes an integer as the first parameter, and then returns true if one or more
	//  of the following parameters (bitwise flags) exists in the first parameter.
	public static function NoneOf(/* $nOpts, $nOpt01[, $nOpt02[, ...]]*/)
	{
		$aArgs = func_get_args();
		$nOpts = array_shift($aArgs);
		
		if(count($nOpts) == 0)
			throw new Zend_Exception("Argument 2 missing.  At least one bitwise flag must be passed.");
			
		$n = 0;
		foreach($aArgs as $nOpt)
			$n += ($nOpts & $nOpt ? 1 : 0);
		
		return $n == 0;
	}
}


?>