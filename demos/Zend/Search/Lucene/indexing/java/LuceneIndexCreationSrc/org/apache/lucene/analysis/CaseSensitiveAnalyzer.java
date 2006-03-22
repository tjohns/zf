package org.apache.lucene.analysis;

import java.io.Reader;

/** An Analyzer that filters LetterTokenizer. */

public final class CaseSensitiveAnalyzer extends Analyzer {
  public TokenStream tokenStream(String fieldName, Reader reader) {
    return new LetterTokenizer(reader);
  }
}
