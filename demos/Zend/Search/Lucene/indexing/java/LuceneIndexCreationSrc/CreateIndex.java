package org.apache.lucene;

import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.analysis.CaseSensitiveAnalyzer;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.demo.FileDocument;
import org.apache.lucene.demo.XMLDocument;

import java.io.File;
import java.util.*;

class CreateIndex {
  public static void main(String[] args) {
    try {
      boolean store, forceLowCase, inputXML;
      String docFolder;
      String indexFolder;
      Set options = new HashSet(args.length);
      Set folders = new LinkedHashSet(args.length);

      for (int count=0; count < args.length; count++ ) {
          if (args[count].charAt(0) == '-' ) {
              options.add(args[count]);
          } else {
              folders.add(args[count]);
          }
      }

      if (folders.size() == 2) {
          Iterator foldersIterator = folders.iterator();
          docFolder   = (String) foldersIterator.next();
          indexFolder = (String) foldersIterator.next();
      } else {
          printUsage();
          return;
      }

      forceLowCase = true;
      store        = false;
      inputXML     = false;

      Iterator optionsIterator = options.iterator();
      while (optionsIterator.hasNext()) {
          String option = (String) optionsIterator.next();

          if (option.equalsIgnoreCase("-c")       ||option.equalsIgnoreCase("--case-sensitive")) {
              forceLowCase = false;
          } else if (option.equalsIgnoreCase("-s")||option.equalsIgnoreCase("--store-content")) {
              store        = true;
          } else if (option.equalsIgnoreCase("-x")||option.equalsIgnoreCase("--xml")) {
              inputXML     = true;
          } else {
              printUsage();
              return;
          }
      }

      if( inputXML && store ) {
          System.out.println("Warning! -x (--xml) option overrides -s (--store-content) option");
          System.out.println("If -x (--xml) option is specified, then storing behaviour is defined");
          System.out.println("by XML data");
      }

      Date start = new Date();
      IndexWriter writer;

      if (forceLowCase) {
          writer = new IndexWriter(indexFolder, new SimpleAnalyzer(), true);
      } else {
          writer = new IndexWriter(indexFolder, new CaseSensitiveAnalyzer(), true);
      }

      writer.mergeFactor = 20;

      indexDocs(writer, new File(docFolder), store, inputXML);

      writer.optimize();
      writer.close();

      Date end = new Date();

      System.out.print(end.getTime() - start.getTime());
      System.out.println(" total milliseconds");

      Runtime runtime = Runtime.getRuntime();

      System.out.print(runtime.freeMemory());
      System.out.println(" free memory before gc");
      System.out.print(runtime.totalMemory());
      System.out.println(" total memory before gc");

      runtime.gc();

      System.out.print(runtime.freeMemory());
      System.out.println(" free memory after gc");
      System.out.print(runtime.totalMemory());
      System.out.println(" total memory after gc");
    } catch (Exception e) {
      System.out.println(" caught a " + e.getClass() +
             "\n with message: " + e.getMessage());
      printUsage();
    }
  }

  public static void indexDocs(IndexWriter writer, File file, boolean store, boolean inputXML)
       throws Exception {
    if (file.isDirectory()) {
      String[] files = file.list();
      for (int i = 0; i < files.length; i++)
        indexDocs(writer, new File(file, files[i]), store, inputXML);
    } else {
      System.out.println("adding " + file);
      // writer.addDocument(FileDocument.Document(file, store));
      if (inputXML) {
          writer.addDocument(XMLDocument.Document(file));
      } else {
          writer.addDocument(FileDocument.Document(file, store));
      }
    }
  }

    private static void printUsage() {
        System.out.println("USAGE: java -jar LuceneIndexCreation.jar [-c] [-s] [-x] <input_folder> <index_folder>");
        System.out.println("-c   - force index to be case sensitive");
        System.out.println("-s   - store content in the index");
        System.out.println("-x   - treat input files as XML (see ZSearch module documentation for details)");
    }
}
