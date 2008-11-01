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
 * @package    Zend_ProgressBar
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * This sample file demonstrates a simple use case of a jspush-driven progressbar
 */

if (isset($_GET['uploadId'])) {
    set_include_path(realpath(dirname(__FILE__) . '/../../../library')
                     . PATH_SEPARATOR
                     . realpath(dirname(__FILE__) . '/../../../../../trunk/library')
                     . PATH_SEPARATOR . get_include_path());
    
    require_once 'Zend/ProgressBar.php'; 
    require_once 'Zend/ProgressBar/Adapter/JsPush.php';
    
    $data = uploadprogress_get_info($_GET['uploadId']);

    if ($data === null) {
        die;
    }
    
    $adapter     = new Zend_ProgressBar_Adapter_JsPush(array('updateMethodName' => 'Zend_ProgressBar_Update',
                                                             'finishMethodName' => 'Zend_ProgressBar_Finish')); 
    $progressBar = new Zend_ProgressBar(0, $data['bytes_total'], $adapter);
     
    do {
        $data = uploadprogress_get_info($_GET['uploadId']);

        $progressBar->update($data['bytes_uploaded']); 
        usleep(10000); 
    } while ($data['bytes_uploaded'] < $data['bytes_total']);
    
    $progressBar->finish();
    
    die;
}
?>
<html>
<head>
    <title>Zend_ProgressBar Upload Demo</title>
    <style type="text/css">   
        iframe {
            position: absolute;
            left: -100px;
            top: -100px;
        
            width: 10px;
            height: 10px;
            overflow: hidden;
        }
    
        #progressbar {
            position: absolute;
            left: 10px;
            top: 50px;
        }
        
        .pg-progressbar {
            position: relative;
        
            width: 250px;
            height: 24px;
            overflow: hidden;
        
            border: 1px solid #c6c6c6;
        }
        
        .pg-progress {
            z-index: 150;
        
            position: absolute;
            left: 0;
            top: 0;
        
            width: 0;
            height: 24px;
            overflow: hidden;
        }
        
        .pg-progressstyle {
            height: 22px;
        
            border: 1px solid #748a9e;
            background-image: url('animation.gif');
        }
        
        .pg-text,
        .pg-invertedtext {
            position: absolute;
            left: 0;
            top: 4px;
        
            width: 250px;
        
            text-align: center;
            font-family: sans-serif;
            font-size: 12px;
        }
        
        .pg-invertedtext {
            color: #ffffff;
        }
        
        .pg-text {
            z-index: 100;
            color: #000000;
        }
    </style>
    <script type="text/javascript">
        function observeProgress()
        {
            setTimeout("startProgress()", 1500);
        }
        
        function startProgress()
        {
            var iFrame = document.createElement('iframe');
            document.getElementsByTagName('body')[0].appendChild(iFrame);
            iFrame.src = 'Upload.php?uploadId=' + document.getElementById('uploadId').value;
        }
        
        function Zend_ProgressBar_Update(data)
        {
            document.getElementById('pg-percent').style.width = data.percent + '%';
        
            document.getElementById('pg-text-1').innerHTML = data.text;
            document.getElementById('pg-text-2').innerHTML = data.text;
        }
        
        function Zend_ProgressBar_Finish()
        {
            document.getElementById('pg-percent').style.width = '100%';
        
            document.getElementById('pg-text-1').innerHTML = 'Upload done';
            document.getElementById('pg-text-2').innerHTML = 'Upload done';
        }
    </script>
</head>
<body>
    <form enctype="multipart/form-data" method="post" action="Upload.php" target="uploadTarget" onsubmit="observeProgress();">
        <input type="hidden" name="UPLOAD_IDENTIFIER" id="uploadId" value="<?php echo md5(uniqid(rand())); ?>" />
        <input type="file" name="file" />
        <input type="submit" value="Upload!" />
    </form>
    <iframe name="uploadTarget"></iframe>

    <div id="progressbar">
        <div class="pg-progressbar">
            <div class="pg-progress" id="pg-percent">
                <div class="pg-progressstyle"></div>
                <div class="pg-invertedtext" id="pg-text-1"></div>
            </div>
            <div class="pg-text" id="pg-text-2"></div>
        </div>
    </div>
    <div id="progressBar"><div id="progressDone"></div></div>
</body>
</html>
