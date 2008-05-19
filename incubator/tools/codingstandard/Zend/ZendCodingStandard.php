<?php
/**
 * Zend Framework Coding Standard
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
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */
if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * PHP_CodeSniffer_Standards_Zend_ZendCodingStandard
 *
 * Base class for coding standard checks
 *
 * Processed Sniffs
 * ================
 * ARRAY SNIFFS
 * ------------
 * ArrayBracketSpacingSniffs Ensure that there are no spaces around square brackets
 *
 * CLASSES SNIFFS
 * --------------
 * ClassDeclarationSniff       Checks the declaration of the class and its inheritance is correct
 * ClassFileNameSniff          Tests that the file name and the name of the class contained within
 *                             the file match
 * LowercaseClassKeywordsSniff Ensures all class keywords are lowercase
 * SelfMemberReferenceSniff    Tests self member references
 *
 * CODEANALYSIS SNIFFS
 * -------------------
 * EmptyStatementSniff              Detects empty statements
 * ForLoopShouldBeWhileLoopSniff    Detects for-loops that can be simplified to a while-loop
 * ForLoopWithTestFunctionCallSniff Detects for-loops that use a function call in the test expression
 * JumbledIncrementerSniff          Detects incrementer jumbling in for loops
 * UnconditionalIfStatementSniff    Detects unconditional if- and elseif-statements
 * UnnecessaryFinalModifierSniff    Detects unnecessary final modifiers inside of final classes
 * UnusedFunctionParameterSniff     Checks the for unused function parameters
 * UselessOverridingMethodSniff     Detects unnecessary final modifiers inside of final classes
 *
 * COMMENTING SNIFFS
 * -----------------
 * BlockCommentSniff            Verifies that block comments are used appropriately
 * ClassCommentSniff            Parses and verifies the class doc comment
 * DocCommentAlignmentSniff     Tests that the stars in a doc comment align correctly
 * EmptyCatchCommentSniff       Checks for empty Catch clause, these must have at least one comment
 * FileCommentSniff             Parses and verifies the doc comments for files
 * FunctionCommentSniff         Parses and verifies the doc comments for functions
 * FunctionCommentThrowTagSniff Verifies that a @throws tag exists for a function that throws
 *                              exceptions, verifies the number of @throws tags and the number of
 *                              throw tokens matches, verifies the exception type
 * InlineCommentSniff           Checks that no perl-style comments (#) are used
 * PostStatementCommentSniff    Checks to ensure that there are no comments after statements
 * VariableCommentSniff         Parses and verifies the variable doc comment
 *
 * CONTROLSTRUCTURE SNIFFS
 * -----------------------
 * ControlSignatureSniff       Verifies that control statements conform to their coding standards
 * ElseIfDeclarationSniff      Verifies that there are not elseif statements. The else and the if
 *                             should be separated by a space
 * ForLoopDeclarationSniff     Verifies that there is a space between each condition of for loops
 * ForEachLoopDeclarationSniff Verifies that there is a space between each condition of foreach loops
 * InlineControlStructureSniff Verifies that inline control statements are not present
 * InlineIfDeclarationSniff    Tests the spacing of shorthand IF statements
 * LowercaseDeclarationSniff   Ensures all control structure keywords are lowercase
 * SwitchDeclarationSniff      Ensures all the breaks and cases are aligned correctly according to
 *                             their parent switch's alignment and enforces other switch formatting
 *
 * FILE SNIFFS
 * -----------
 * ClosingTagSniff    Checks that the file does not include a closing tag
 *                    Wether at file end nor inline for output purposes
 * IncludingFileSniff Checks that the include_once is used in conditional situations, and
 *                    require_once is used elsewhere. Also checks that brackets do not surround
 *                    the file being included. And checks that require_once and include_once are
 *                    used instead of require and include
 * LineEndingsSniff   Checks for Unix (\n) linetermination, disallowing Windows (\r\n) or Max (\r)
 * LineLengthSniff    Checks all lines in the file, and throws warnings if they are over 100
 *                    characters in length and errors if they are over 120
 *
 * FORMATTING SNIFFS
 * -----------------
 * MultipleStatementAlignSniff Checks alignment of assignments. If there are multiple adjacent
 *                             assignments, it will check that the equals signs of each assignment
 *                             are aligned. It will display a warning to advise that the signs
 *                             should be aligned
 * OperatorBracketSniff        Tests that all arithmetic operations are bracketed
 * OutputBufferingIndentSniff  Checks the indenting used when an ob_start() call occurs
 * SpaceAfterCastSniff         Ensures there is a single space after cast tokens
 *
 * FUNCTION SNIFFS
 * ---------------
 * FunctionCallArgumentSpacingSniff        Checks that calls to methods and functions are spaced correctly
 * FunctionCallSignatureSniff              Checks for the right spacing in fuction declaration
 * FunctionDeclarationArgumentSpacingSniff Checks that arguments in function declarations are spaced correctly
 * FunctionDeclarationSniff                Checks the function declaration is correct
 * FunctionDuplicateArgumentSniff          Checks that duplicate arguments are not used in function declarations
 * GlobalFunctionSniff                     Tests for functions outside of classes
 * LowercaseKeywordsSniff                  Ensures all class keywords are lowercase
 * OpeningFunctionBraceSniff               Checks that the opening brace of a function is on the line after the
 *                                         function declaration
 * ValidDefaultValueSniff                  A Sniff to ensure that parameters defined for a function that have a
 *                                         default value come at the end of the function signature
 *
 * METRICS
 * -------
 * NestingLevelSniff Checks the nesting level for methods
 *
 * NAMINGCONVENTIONS SNIFFS
 * ------------------------
 * UpperCaseConstantNameSniff Ensures that constant names are all uppercase
 * ValidClassNameSniff        Ensures class and interface names start with a capital letter
 *                            and use _ separators
 * ValidFunctionNameSniff     Ensures method names are correct depending on whether they are public
 *                            or private, and that functions are named correctly
 * ValidVariableNameSniff     Checks the naming of variables and member variables
 *
 * OBJECT SNIFFS
 * -------------
 * ObjectInstantiationSniff Ensures objects are assigned to a variable when instantiated
 *
 * OPERATOR SNIFFS
 * ---------------
 * ComparisonOperatorUsageSniff Enforce the use of IDENTICAL type operators rather than EQUAL
 *                              operators. The use of === true is enforced over implicit true
 *                              statements, It also enforces the use of === false over ! operators.
 * IncrementDecrementUsageSniff Tests that the ++ operators are used when possible
 * ValidLogicalOperatorsSniff   Checks to ensure that the logical operators 'and' and 'or' are used
 *                              instead of the && and || operators
 *
 * PHP SNIFFS
 * ----------
 * CommentedOutCodeSniff            Warn about commented out code
 * DisallowCountInLoopsSniff        Disallows the use of count in loop conditions
 * DisallowMultipleAssignmentsSniff Ensures that there is only one value assignment on a line, and
 *                                  that it is the first thing on the line
 * DisallowObEndFlushSniff          Disallow ob_end_flush, use ob_get_contents() and ob_end_clean() instead
 * DisallowShortOpenTagSniff        Makes sure that shorthand PHP open tags are not used ("<?"), but allows open
 *                                  tag with echo ("<?="). short_open_tag must be set to true for this test to work
 * EvalSniff                        The use of eval() is discouraged
 * ForbiddenFunctionsSniff          Discourages the use of alias functions that are kept in PHP for compatibility
 *                                  with older versions. Can be used to forbid the use of any function
 * GlobalKeywordSniff               Stops the usage of the "global" keyword
 * HeredocSniff                     Heredocs are discuraged
 * InnerFunctionsSniff              Ensures that functions within functions are never used
 * LowerCaseConstantSniff           Checks that all uses of 'true', 'false' and 'null' are lowercase
 * LowercasePHPFunctionsSniff       Ensures all calls to inbuilt PHP functions are lowercase
 * NonExecutableCodeSniff           Warns about code that can never been executed. This happens when a function
 *                                  returns before the code, or a break ends execution of a statement etc
 *
 * SCOPE SNIFFS
 * ------------
 * MemberVarScopeSniff  Verifies that class variables have scope modifiers
 * MethodScopeSniff     Verifies that class members have scope modifiers
 * StaticThisUsageSniff Checks for usage of "$this" in static methods, which will cause runtime errors
 *
 * STRING SNIFFS
 * -------------
 * ConcatenationSpacingSniff Makes sure there is one spaces between the concatenation operator (.) and
 *                           the strings being concatenated
 * DoubleQuoteUsageSniff     Makes sure that any use of Double Quotes ("") are warranted
 * EchoedStringsSniff        Makes sure that any strings that are "echoed" are not enclosed in brackets
 *                           like a function call
 *
 * WHITESPACE SNIFFS
 * -----------------
 * CastSpacingSniff               Ensure cast statements dont contain whitespace
 * ControlStructureSpacingSniff   Checks that there is a empty line after control structures for readability
 * DisallowTabSniff               Checks if tabs are used and errors if any are found
 * FunctionOpeningBraceSpaceSniff Checks that there is no empty line after the opening brace of a function
 * FunctionSpacingSniff           Checks the separation between methods in a class or interface
 * LanguageConstructSpacingSniff  Ensures all language constructs (without brackets) contain a
 *                                single space between themselves and their content
 * MemberVarSpacingSniff          Verifies that class members are spaced correctly
 * ObjectOperatorSpacingSniff     Ensure there is no whitespace before and after the object operator
 * OperatorSpacingSniff           Verifies that operators have valid spacing surrounding them
 * ScopeClosingBraceSniff         Checks that the closing braces of scopes are aligned correctly
 * ScopeIndentSniff               Checks that control structures are structured correctly, and their content
 *                                is indented correctly. This sniff will throw errors if tabs are used
 *                                for indentation rather than spaces
 * ScopeKeywordSpacingSniff       Ensure there is a single space after scope keywords
 * SemicolonSpacingSniff          Ensure there is no whitespace before a semicolon
 * SuperflousWhitespaceSniff      Checks that no whitespace proceeds the first content of the file, exists
 *                                after the last content of the file, resides after content on any line, or
 *                                are two empty lines in functions
 *
 * @category  Zend
 * @package   Zend_CodingStandard
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class PHP_CodeSniffer_Standards_Zend_ZendCodingStandard extends
      PHP_CodeSniffer_Standards_CodingStandard
{

    /**
     * Return a list of external sniffs to include with the Zend Framework standard
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
            'Squiz/Sniffs/Arrays/ArrayBracketSpacingSniff.php',
            'Squiz/Sniffs/Classes/LowercaseClassKeywordsSniff.php',
            'Squiz/Sniffs/Classes/SelfMemberReferenceSniff.php',
            'Generic/Sniffs/CodeAnalysis/EmptyStatementSniff.php',
            'Generic/Sniffs/CodeAnalysis/ForLoopShouldBeWhileLoopSniff.php',
            'Generic/Sniffs/CodeAnalysis/ForLoopWithTestFunctionCallSniff.php',
            'Generic/Sniffs/CodeAnalysis/JumbledIncrementerSniff.php',
            'Generic/Sniffs/CodeAnalysis/UnconditionalIfStatementSniff.php',
            'Generic/Sniffs/CodeAnalysis/UnnecessaryFinalModifierSniff.php',
            'Generic/Sniffs/CodeAnalysis/UnusedFunctionParameterSniff.php',
            'Generic/Sniffs/CodeAnalysis/UselessOverridingMethodSniff.php',
            'Squiz/Sniffs/Commenting/DocCommentAlignmentSniff.php',
            'Squiz/Sniffs/Commenting/EmptyCatchCommentSniff.php',
            'Squiz/Sniffs/Commenting/FunctionCommentThrowTagSniff.php',
            'Squiz/Sniffs/ControlStructures/ControlSignatureSniff.php',
            'Squiz/Sniffs/ControlStructures/ElseIfDeclarationSniff.php',
            'Squiz/Sniffs/ControlStructures/ForEachLoopDeclarationSniff.php',
            'Squiz/Sniffs/ControlStructures/ForLoopDeclarationSniff.php',
            'Generic/Sniffs/ControlStructures/InlineControlStructureSniff.php',
            'Squiz/Sniffs/ControlStructures/InlineIfDeclarationSniff.php',
            'Squiz/Sniffs/ControlStructures/LowercaseDeclarationSniff.php',
            'Generic/Sniffs/Files/LineEndingsSniff.php',
            'Squiz/Sniffs/Formatting/OperatorBracketSniff.php',
            'Squiz/Sniffs/Formatting/OutputBufferingIndentSniff.php',
            'Generic/Sniffs/Formatting/SpaceAfterCastSniff.php',
            'Squiz/Sniffs/Functions/FunctionDeclarationSniff.php',
            'Squiz/Sniffs/Functions/FunctionDuplicateArgumentSniff.php',
            'Squiz/Sniffs/Functions/LowercaseFunctionKeywordsSniff.php',
            'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
            'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',
            'Squiz/Sniffs/Objects/ObjectInstantiationSniff.php',
            'Squiz/Sniffs/Operators/ComparisonOperatorUsageSniff.php',
            'Squiz/Sniffs/Operators/IncrementDecrementUsageSniff.php',
            'Squiz/Sniffs/PHP/CommentedOutCodeSniff.php',
            'Squiz/Sniffs/PHP/DisallowSizeFunctionsInLoopsSniff.php',
            'Squiz/Sniffs/PHP/DisallowMultipleAssignmentsSniff.php',
            'Squiz/Sniffs/PHP/DisallowObEndFlushSniff.php',
            'Squiz/Sniffs/PHP/EvalSniff.php',
            'Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php',
            'Squiz/Sniffs/PHP/GlobalKeywordSniff.php',
            'Squiz/Sniffs/PHP/HeredocSniff.php',
            'Squiz/Sniffs/PHP/InnerFunctionsSniff.php',
            'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
            'Squiz/Sniffs/PHP/LowercasePHPFunctionsSniff.php',
            'Squiz/Sniffs/PHP/NonExecutableCodeSniff.php',
            'Squiz/Sniffs/Scope/MemberVarScopeSniff.php',
            'Squiz/Sniffs/Scope/MethodScopeSniff.php',
            'Squiz/Sniffs/Scope/StaticThisUsageSniff.php',
            'Squiz/Sniffs/Strings/DoubleQuoteUsageSniff.php',
            'Squiz/Sniffs/Strings/EchoedStringsSniff.php',
            'Squiz/Sniffs/WhiteSpace/CastSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/ControlStructureSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/FunctionOpeningBraceSpaceSniff.php',
            'Squiz/Sniffs/WhiteSpace/LanguageConstructSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/ObjectOperatorSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/ScopeKeywordSpacingSniff.php',
            'Generic/Sniffs/WhiteSpace/ScopeIndentSniff.php',
            'Squiz/Sniffs/WhiteSpace/SemicolonSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/SuperfluousWhitespaceSniff.php',
        );
    }
}

/**
 * TODO:
 * class end  - no empty line before closing brace of a class
 *    }
 * }
 *
 * class begin - no empty line after the beginning brace of a class
 * class xxxx
 * {
 *   /**
 *
 * if / foreach / for / function - when linebreak indended to equality sign or 1 after the opening brace
 * if ((ss = a) or
 *     (ww = b))
 *
 * Optional descriptions should begin with the Description "(optional)"
 * @param string $variable (Optional) my variable
 *
 * Error on @throws tag when no exception is thrown
 * Error on @throws tag in false order
 */