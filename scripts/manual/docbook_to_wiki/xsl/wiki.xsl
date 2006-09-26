<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>    

<xsl:template match="chapter">
{toc:style=none|indent=25px}
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="sect1/title">
h1. <xsl:value-of select="."/>

</xsl:template>

<xsl:template match="sect2/title">
h2. <xsl:value-of select="."/>

</xsl:template>

<xsl:template match="sect3/title">
h3. <xsl:value-of select="."/>

</xsl:template>

<xsl:template match="sect4/title">
h4. <xsl:value-of select="."/>

</xsl:template>

<xsl:template match="note">

{note}
<xsl:value-of select="."/>
{note}

</xsl:template>

<xsl:template match="para">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="listitem">
* <xsl:value-of select="."/>
</xsl:template>

<xsl:template match="tip">
{tip}
<xsl:value-of select="."/>
{tip}
</xsl:template>

<xsl:template match="programlisting">
{code}
<xsl:value-of select="."/>
{code}
</xsl:template>

<xsl:template match="example/title">
*<xsl:value-of select="."/>*

</xsl:template>

<xsl:template match="thead/row/entry">||<xsl:value-of select="."/></xsl:template>
<xsl:template match="tbody/row/entry">|<xsl:value-of select="."/></xsl:template>

<xsl:template match="para/code">_<xsl:value-of select="."/>_</xsl:template>
   
</xsl:stylesheet>