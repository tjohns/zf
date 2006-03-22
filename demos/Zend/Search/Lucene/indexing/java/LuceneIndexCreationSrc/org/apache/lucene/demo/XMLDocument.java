package org.apache.lucene.demo;

import java.io.*;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import javax.xml.parsers.*;
import org.xml.sax.*;


/** An utility for making Lucene Documents from a XML File. */
public class XMLDocument {
    static class DocumentConstructor
            extends org.xml.sax.helpers.DefaultHandler
            implements org.xml.sax.ContentHandler {
        private Document doc;

        private int deep;
        private String currentField;
        private String currentFieldData;
        private boolean storeCurrentField;
        private boolean isKeyword;

        public DocumentConstructor() {
            doc = new Document();
            deep = 0;
            currentField = "";
            currentFieldData = "";
            storeCurrentField = false;
            isKeyword = false;
        }

        public void startElement(String namespace, String localname, String type, Attributes attributes )
                throws org.xml.sax.SAXException   {
            if (deep == 0) {
                if (!type.equals("xml")) // Incorrect XML format
                    throw new SAXException("Incorrect XML format");
                deep++; return;
            }

            if (deep != 1) // Incorrect XML format
                throw new SAXException("Incorrect XML format");

            deep++;
            currentField = type;
            currentFieldData = "";
            storeCurrentField = new String("true").equalsIgnoreCase(attributes.getValue("stored"));
            isKeyword = new String("keyword").equalsIgnoreCase(attributes.getValue("type"));
        }

        public void endElement(String namespace, String localname, String type)
                throws org.xml.sax.SAXException {
            Field newField;

            if (isKeyword) {
                newField = Field.Keyword(currentField, currentFieldData);
            } else if( storeCurrentField ) {
                newField = Field.Text(currentField, currentFieldData);
            } else {
                newField = Field.Text(currentField, new StringReader(currentFieldData));
            }

            doc.add(newField);
            deep--;
        }

        public void characters(char[] ch, int start, int len) {
            currentFieldData += new String( ch, start, len ).trim();
        }

        public Document getDoc() {return doc;}
    }

    /** Makes a document for a XML File.
        <p>
        The file must look as follows:
        &lt;xml&gt;
           &lt;title&gt;This is the title field.&lt;/title&gt;
           &lt;body stored="true"&gt;This is the body field.&lt;/body&gt;
        &lt;xml&gt;
        <p>

        The XML must be one-level deep as shown above.  If the user tries to index
        anything deeper than that, or if the the file is not valid XML, it stops
        and reports an error.
        */
      public static Document Document(File f)
           throws Exception {
        SAXParserFactory saxParserFactory = SAXParserFactory.newInstance();
        SAXParser saxParser = saxParserFactory.newSAXParser();
        XMLReader parser = saxParser.getXMLReader();

        DocumentConstructor docConstructor = new DocumentConstructor();
        parser.setContentHandler( docConstructor );

        FileInputStream is = new FileInputStream(f);
        Reader reader = new BufferedReader(new InputStreamReader(is));

        parser.parse( new org.xml.sax.InputSource( reader ));

        // return the document
        return docConstructor.getDoc();
      }

      private XMLDocument() {}
}
