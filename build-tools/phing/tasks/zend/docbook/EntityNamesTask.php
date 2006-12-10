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
 * @package    Zend_Gdata_Data
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once("phing/Task.php");
require_once("phing/util/DirectoryScanner.php");
require_once("phing/util/FileUtils.php");

/**
 * Phing Taskdef to generate a list of entities for system files under
 * specified directories.
 *
 * For example, a set of files such as this:
 * 
 *  ref/coding_standard.xml
 *  ref/conf.xml
 *  ref/copyrights.xml
 *  ref/faq.xml
 *  ref/install.xml
 *  ref/preface.xml
 *
 * Is output in an XML entities file like this:
 * 
 *  <!-- dir: ref -->
 *  <!-- ref/coding_standard.xml -->
 *  <!ENTITY ref.coding_standard SYSTEM "ref/coding_standard.xml">
 *  ...
 *  <!-- ref -->
 *  <!ENTITY ref.all "&ref.coding_standard;&ref.conf;&ref.copyrights;&ref.faq;&ref.install;&ref.preface;">
 *
 * @category   Zend
 * @package    Zend_Gdata_Data
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class EntityNamesTask extends Task
{
    protected $filelists = array();
    protected $filesets = array();
    protected $propertyName = null;
    protected $outputFile = null;
    protected $output = '';

    public function createFileList() {
        $num = array_push($this->filelists, new FileList());
        return $this->filelists[$num-1];
    }

    public function createFileSet() {
        $num = array_push($this->filesets, new FileSet());
        return $this->filesets[$num-1];
    }

    public function setProperty($property) 
    {
        $this->propertyName = (string) $property;
    }

    public function setOutputfile($filename) 
    {
        $this->outputFile = (string) $filename;
    }

    public function init() 
    {
        $this->output = "<!-- auto-generated -->\n";
    }

    public function main()
    {
        if ($this->propertyName == null && $this->outputFile == null) {
            throw new BuildException('You must specify either the "property" or "outputfile" attribute.');
        }

        // append the files in the filelists
        foreach($this->filelists as $fl) {
            try {
                $files = $fl->getFiles($this->project);
                $this->makeEntityNames($files, $fl->getDir($this->project));
            } catch (BuildException $be) {
                $this->log($be->getMessage(), PROJECT_MSG_WARN);
            }
        }
        
        // append any files in filesets
        foreach($this->filesets as $fs) {
            try {
                $files = $fs->getDirectoryScanner($this->project)->getIncludedFiles();
                $this->makeEntityNames($files, $fs->getDir($this->project));
            } catch (BuildException $be) {
                $this->log($be->getMessage(), PROJECT_MSG_WARN);
            }
        }
        if ($this->propertyName != null) {
            $this->project->setProperty($this->propertyName, $this->output);
        }
        if ($this->outputFile != null) {
            $handle = fopen($this->outputFile, 'w');
            fwrite($handle, $this->output);
            fclose($handle);
        }
    }

    private function makeEntityNames($dirnames, PhingFile $rootDir)
    {
        if (empty($dirnames)) {
            return;
        }
        foreach ($dirnames as $dirname) {
            $this->output .= "<!-- dir: $dirname -->\n";
            $ds = new DirectoryScanner();
            $ds->setIncludes(array("**/*.xml"));
            $futil = new FileUtils();
            $baseDir = $futil->resolveFile($rootDir, $dirname);
            $ds->setBasedir($baseDir->getPath());
            $ds->scan();
            $xmlFiles = $ds->getIncludedFiles();
            $allEntities = '';
            foreach ($xmlFiles as $xmlFilename) {
                $xmlFile = $futil->resolveFile($baseDir, $xmlFilename);
                $entityName = $this->fileToEntity($xmlFile, $rootDir);
                $this->output .= "<!-- $dirname/$xmlFilename-->\n";
                $this->output .= "<!ENTITY $entityName SYSTEM \"$dirname/$xmlFilename\">\n";
                $allEntities .= "&$entityName;";
            }
            $this->output .= "<!-- $dirname -->\n";
            $this->output .= "<!ENTITY $dirname.all \"$allEntities\">\n";

        }
    }

    private function fileToEntity(PhingFile $file, PhingFile $rootDir)
    {
        $entityName = str_replace(
            array('.xml', '.' . DIRECTORY_SEPARATOR . 'docbook', DIRECTORY_SEPARATOR),
            array('', '', '.'),
            substr($file->getAbsolutePath(), strlen($rootDir->getAbsolutePath())+1));
        return $entityName;
    }

}

?>
