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
 * @package    Zend_Http
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


require_once( 'AbstractHandler.php' );

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DefaultHandler extends AbstractHandler
{

    public function handle()
    {
        $types = array(
                "php" => "text/html",
                "html" => "text/html",
                "htm" => "text/html",
                "css" => "text/css",
                "jpg" => "image/jpeg",
                "jpeg" => "image/jpeg",
                "gif" => "image/gif",
                "phps" => "text/html",
                "pdf" => "application/pdf",
                "" => "text/plain",
            );

        $real_file = $this->options[ "rewrite" ] ? $this->applyRewriteRules() : $this->request->path . "/" . $this->request->file;

        $extension = array_pop( explode( ".", basename( $real_file ) ) );

        if( !in_array( $extension, array_keys( $types ) ) )
        {
            $extension = "";
        }

        if( file_exists( $this->request->document_root . $real_file ) )
        {
            // DefaultHandler: File exists
            if( $extension == "phps" )
            {
                $output = highlight_file( $this->request->document_root . $real_file, true );

                $headers = array(
                    "Content-Type" => $types[ $extension ],
                    "Content-Length" => strlen( $output )
                );

                $response = new Zend_Http_Response( 200, $headers, $output );
            }
            else if( $extension == "php" )
            {
                // DefaultHandler: PHP script

                $current_directory = getcwd();
                chdir( dirname( $this->request->document_root . $real_file ) );

                // Setting up globals

                $_GET = $this->request->get;
                $_POST = $this->request->post;
                $_REQUEST = array_merge( $_GET, $_POST );

                $_SERVER[ "DOCUMENT_ROOT" ] = $this->request->document_root;
                $_SERVER[ "SCRIPT_FILENAME" ] = $this->request->file;
                $_SERVER[ "PHP_SELF" ] = $this->request->path;
                $_SERVER[ "SCRIPT_NAME" ] = $this->request->path . "/" . $this->request->file;
                $_SERVER[ "argv" ] = $this->request->query_string;
                $_SERVER[ "SERVER_ADDR" ] = "";
                $_SERVER[ "SERVER_NAME" ] = "";
                $_SERVER[ "SERVER_SOFTWARE" ] = "Zend HTTP Server (alpha)";
                $_SERVER[ "SERVER_PROTOCOL" ] = "";
                $_SERVER[ "REQUEST_METHOD" ] = $this->request->method;
                $_SERVER[ "REQUEST_TIME" ] = time();
                $_SERVER[ "QUERY_STRING" ] = $this->request->query_string;
                $_SERVER[ "REQUEST_URI" ] = $this->request->uri;
                $_SERVER[ "HTTP_HOST" ] = $this->request->headers[ "Host" ];

                unset( $_SERVER[ "argc" ] );

                $output = self::startScript( $this->request->document_root . $real_file );

                // DefaultHandler: Done.  Sending response.

                chdir( $current_directory );

                $headers = array(
                    "Content-Type" =>  "text/html",
                    "Content-Length" => strlen( $output )
                );

                foreach( headers_list() as $header )
                {
                    list( $name, $value ) = split( ":", $header );
                    $headers[ $name ] = $value;
                }

                $response = new Zend_Http_Response( 200, $headers, $output );
            }
            else
            {
                $data = file_get_contents( $this->request->document_root . $real_file );
                $response = new Zend_Http_Response( 200, array( "Content-Type" => $types[ $extension ], "Content-Length" => strlen( $data ) ), $data );
            }
        }
        else
        {
            $response = new Zend_Http_Response( 404, array( "Content-Type" => "text/plain" ), "File Not Found!" );
        }

        return $response;
    }

    /*
        Apply rewrite rules to the current request before continuing to parse
    */
    private function applyRewriteRules()
    {
        /*
            Parse rewrite rules and run them... a bit beyond this prototype, but this basic skeleton is here to remind
            me to do it!

            For now, put in just the rule neccessary for Zend_Controller_RewriteRouter to work
        */
        if( !preg_match( "!\.(js|ico|gif|jpg|png|css)$!", $this->request->file ) )
        {
            return "/index.php";
        }

        return $this->request->path . "/" . $this->request->file;
    }


    /*

        Utility function to execute a PHP script and capture output - it is static so that $this is not available in the
        script's context!

    */
    private static function startScript( $file )
    {
                ob_start();
                include( $file );
                return ob_get_clean();

    }
}
