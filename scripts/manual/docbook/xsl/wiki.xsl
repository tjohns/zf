<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:fn="http://www.w3.org/2005/xpath-functions">

<xsl:output method="text" version="1.0" encoding="UTF-8" />
<xsl:preserve-space elements="programlisting" />
<xsl:strip-space elements="*" />

<!-- Chapter -->
<xsl:template match="chapter">
{zone-template-instance:ZFDOCDEV:manual-template}
{zone-data:content}
<xsl:apply-templates/>
{zone-data}
{zone-template-instance}
</xsl:template>

<!-- Chapter title -->
<xsl:template match="chapter/title">
h1. <xsl:value-of select="."/>
</xsl:template>

<!-- Section heading level 1 -->
<xsl:template match="sect1/title">
h2. <xsl:value-of select="."/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Section heading level 2 -->
<xsl:template match="sect2/title">
h3. <xsl:value-of select="."/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Section heading level 3 -->
<xsl:template match="sect3/title">
h4. <xsl:value-of select="."/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Section heading level 4 -->
<xsl:template match="sect4/title">
h5. <xsl:value-of select="."/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Section heading level 5 (maximum level) -->
<xsl:template match="sect4/title">
h6. <xsl:value-of select="."/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Itemized list -->
<xsl:template match="itemizedlist">
<xsl:text>&#10;</xsl:text>
<xsl:apply-templates/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- List item -->
<xsl:template match="listitem">
* <xsl:apply-templates/>
</xsl:template>

<xsl:template match="example">
<xsl:apply-templates/>
{panel}
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Example title -->
<xsl:template match="example/title">
{panel:title=Example: <xsl:value-of select="."/>|borderStyle=dashed| borderColor=#ccc| titleBGColor=#F7D6C1| bgColor=#FFFFCE}
</xsl:template>

<!-- Important notice -->
<xsl:template match="note">
<xsl:text>&#10;</xsl:text>
{tip:icon=true|title=Note: <xsl:value-of select="title"/>}
<xsl:apply-templates/>
{tip}
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Useful tip -->
<xsl:template match="tip">
<xsl:text>&#10;</xsl:text>
{tip:icon=true|title=Tip: <xsl:value-of select="title"/>}
<xsl:apply-templates/>
{tip}
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Code snippet -->
<xsl:template match="programlisting">
<xsl:text>&#10;</xsl:text>
{code:<xsl:value-of select="@role"/>}
<xsl:apply-templates/>
{code}
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Paragraph -->
<xsl:template match="para">
<xsl:apply-templates/>
<xsl:text>&#10;</xsl:text>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="entry/para">
 <xsl:apply-templates />
</xsl:template>

<xsl:template match="para/text()">
<xsl:value-of select="normalize-space(.)"/>
</xsl:template>


<xsl:template match="row/entry/text()">
<xsl:value-of select="normalize-space(.)"/>
</xsl:template>

<!-- Table -->
<xsl:template match="table">
<xsl:text>&#10;</xsl:text>
*<xsl:value-of select="title" />*
<xsl:text>&#10;</xsl:text>
<xsl:apply-templates select="tgroup"/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Table group -->
<xsl:template match="tgroup">
<xsl:apply-templates select="thead"/>
<xsl:apply-templates select="tbody"/>
</xsl:template>

<!-- Table header -->
<xsl:template match="thead">
<xsl:apply-templates select="row">
	<xsl:with-param name="delim">||</xsl:with-param>
</xsl:apply-templates>
</xsl:template>

<!-- Table body -->
<xsl:template match="tbody">
<xsl:apply-templates select="row">
	<xsl:with-param name="delim">|</xsl:with-param>
</xsl:apply-templates>
</xsl:template>

<!-- Table row -->
<xsl:template match="row">
<xsl:param name="delim"/>
<xsl:apply-templates select="entry">
    <xsl:with-param name="delim" select="$delim" />
</xsl:apply-templates>
<xsl:value-of select = "$delim" />
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Table entry (colum) -->
<xsl:template match="entry">
<xsl:param name = "delim" />
<xsl:value-of select = "$delim" />
<xsl:text> </xsl:text>
<xsl:apply-templates/>
<xsl:text> </xsl:text>
</xsl:template>

<!-- Italic -->
<xsl:template match="code"><xsl:text> </xsl:text>{{<xsl:value-of select="normalize-space(.)"/>}}<xsl:text> </xsl:text></xsl:template>

<!-- External links -->
<xsl:template match="ulink"><xsl:text> </xsl:text>[<xsl:value-of select="normalize-space(.)"/>|<xsl:value-of select="@url"/>]<xsl:text> </xsl:text></xsl:template>

<!-- Bold and italic -->
<xsl:template match="emphasis">
<xsl:if test="@role='strong'"><xsl:text> </xsl:text>*<xsl:value-of select="normalize-space(.)"/>*<xsl:text> </xsl:text></xsl:if>
<xsl:if test="@role='italic'"><xsl:text> </xsl:text>_<xsl:value-of select="normalize-space(.)"/>_<xsl:text> </xsl:text></xsl:if>
</xsl:template>

</xsl:stylesheet>