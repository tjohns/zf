<?php

class Zend_Tool_Project_Context_Zf_ViewScriptFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    protected $_filesystemName = 'view.phtml';
    
    protected $_scriptName = null;

    public function init()
    {
        $this->_scriptName     = $this->_resource->getAttribute('scriptName');
        $this->_filesystemName = $this->_scriptName . '.phtml';
        
        parent::init();
    }
    
    
    public function getPersistentParameters()
    {
        return array(
            'scriptName' => $this->_scriptName
            );
    }
    
    public function getName()
    {
        return 'ViewScriptFile';
    }
    
    
    public function getContents()
    {
        $contents = '';
        
        if ($this->_filesystemName == 'error.phtml') {  // should also check that the above directory is forController=error
            $contents .= <<<EOS
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"; "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
  <title>Zend Framework Default Application</title> 
</head> 
<body> 
  <h1>An error occurred</h1> 
  <h2><?= \$this->message ?></h2> 

  <? if ('development' == \$this->env): ?> 
  
  <h3>Exception information:</h3> 
  <p> 
      <b>Message:</b> <?= \$this->exception->getMessage() ?> 
  </p> 

  <h3>Stack trace:</h3> 
  <pre><?= \$this->exception->getTraceAsString() ?> 
  </pre> 

  <h3>Request Parameters:</h3> 
  <pre><? var_dump(\$this->request->getParams()) ?> 
  </pre> 
  <? endif ?>
  
</body> 
</html>
            
EOS;
        } else {
            $contents = 'Hello from the view script!';
        }
        return $contents;
    }
    
}