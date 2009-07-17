<?php

class Zend_Entity_StateTransformer_XmlSerializerTest extends PHPUnit_Framework_TestCase
{
    public function dataArrayToXml()
    {
        return array(
            array(
                array("foo" => "bar", "bar" => "baz"),
                '<?xml version="1.0" ?><array type="array"><foo type="string"><![CDATA[bar]]></foo><bar type="string"><![CDATA[baz]]></bar></array>',
            ),
            array(
                array("foo" => array("bar" => "baz")),
                '<?xml version="1.0" ?><array type="array"><foo type="array"><bar type="string"><![CDATA[baz]]></bar></foo></array>',
            ),
            array(
                array("foo" => array("baz", "foo")),
                '<?xml version="1.0" ?>'.
                '<array type="array">'.
                    '<foo type="array">'.
                        '<array_numeric_elem0 type="string"><![CDATA[baz]]></array_numeric_elem0>'.
                        '<array_numeric_elem1 type="string"><![CDATA[foo]]></array_numeric_elem1>'.
                    '</foo>'.
                '</array>',
            ),
            array(
                array("foo" => array()),
                '<?xml version="1.0" ?>'.
                '<array type="array">'.
                    '<foo type="array" />'.
                '</array>',
            ),
            array(
                array(),
                '<?xml version="1.0" ?><array type="array"></array>',
            ),
            array(
                array('foo' => 100, 'bar' => 1.32),
                '<?xml version="1.0" ?><array type="array"><foo type="integer"><![CDATA[100]]></foo><bar type="double"><![CDATA[1.32]]></bar></array>'
            )
        );
    }

    /**
     * @dataProvider dataArrayToXml
     * @param array $array
     * @param string $expectedXml
     */
    public function testArrayToXml($array, $expectedXml)
    {
        $actualXml = Zend_Entity_StateTransformer_XmlSerializer::toXml($array);
        $actualXml = str_replace("\n", "", $actualXml);
        $this->assertEquals($expectedXml, $actualXml);
    }

    public function testNonArrayToXml_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception",
            "Trying to transform 'string' to xml but an array is expected."
        );

        Zend_Entity_StateTransformer_XmlSerializer::toXml("invalid");
    }

    public function testArray_WithNonPrimativeObjectIncluded_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception",
            "Can only serialize an array of primative integer, float and string datataypes."
        );

        Zend_Entity_StateTransformer_XmlSerializer::toXml(array(new stdClass()));
    }

    public function testArray_WithNonPrimativeBooleanIncluded_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception",
            "Can only serialize an array of primative integer, float and string datataypes."
        );

        Zend_Entity_StateTransformer_XmlSerializer::toXml(array(true));
    }

    public function testDeserialize_NonString_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception",
            "Can only deserialize xml strings to an array."
        );

        Zend_Entity_StateTransformer_XmlSerializer::fromXml(array());
    }

    public function testDeserialize_NonXmlBeginning_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_StateTransformer_Exception",
            "Can only deserialize xml strings to an array."
        );

        Zend_Entity_StateTransformer_XmlSerializer::fromXml("foo");
    }

    static public function dataXmlToArray()
    {
        return array(
            array(
                '<?xml version="1.0" ?><array type="array"></array>', array()
            ),
            array(
                '<?xml version="1.0" ?><array type="array"><foo type="array" /></array>', array("foo" => array())
            ),
            array(
                '<?xml version="1.0" ?><array type="array"><foo type="array"><array_numeric_elem0 type="string"><![CDATA[bar]]></array_numeric_elem0></foo></array>',
                array('foo' => array('bar'))
            ),
            array(
                '<?xml version="1.0" ?><array type="array"><foo type="array"><baz type="string"><![CDATA[bar]]></baz></foo></array>',
                array('foo' => array('baz' => 'bar'))
            ),
            array(
                '<?xml version="1.0" ?><array type="array"><foo type="double"><![CDATA[1.32]]></foo><bar type="integer"><![CDATA[100]]></bar></array>',
                array('foo' => 1.32, 'bar' => 100),
            ),
        );
    }

    /**
     *
     * @dataProvider dataXmlToArray
     * @param string $xml
     * @param array $expectedArray
     */
    public function testDeserializeXmlToArray($xml, $expectedArray)
    {
        $actualArray = Zend_Entity_StateTransformer_XmlSerializer::fromXml($xml);
        $this->assertEquals($expectedArray, $actualArray);
    }
}