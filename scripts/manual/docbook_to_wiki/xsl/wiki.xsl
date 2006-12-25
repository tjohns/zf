<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="text"/>    

<xsl:template match="chapter">
{zone-template-instance:ZFDOCDEV:manual-template}
{zone-data:content}
<xsl:apply-templates/>
{zone-data}
{zone-template-instance}
</xsl:template>

<xsl:template match="sect1/title">
h1. <xsl:value-of select="normalize-space(.)"/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="sect2/title">
h2. <xsl:value-of select="normalize-space(.)"/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="sect3/title">
h3. <xsl:value-of select="normalize-space(.)"/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="sect4/title">
h4. <xsl:value-of select="normalize-space(.)"/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="note">
<xsl:text>&#10;</xsl:text>
{tip:icon=false|title=<xsl:value-of select="normalize-space(title)"/>}
<xsl:value-of select="normalize-space(.)"/>
{tip}
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="para">
<xsl:apply-templates/>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="listitem">
* <xsl:apply-templates/>
</xsl:template>

<xsl:template match="tip">
<xsl:text>&#10;</xsl:text>
{tip:title=Info|title=<xsl:value-of select="title"/>}
<xsl:value-of select="."/>
{tip}
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="programlisting">
<xsl:text>&#10;</xsl:text>
{code:<xsl:value-of select="@role"/>}
<xsl:value-of select="."/>
{code}
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="example/title">
*<xsl:value-of select="normalize-space(.)"/>*
<xsl:text>&#10;</xsl:text>
</xsl:template>

<xsl:template match="thead/row/entry">||<xsl:value-of select="normalize-space(.)"/></xsl:template>
<xsl:template match="tbody/row/entry">|<xsl:value-of select="normalize-space(.)"/></xsl:template>

<xsl:template match="para/code">{{<xsl:value-of select="normalize-space(.)"/>}}</xsl:template>

<xsl:template match="ulink">[<xsl:value-of select="normalize-space(.)"/>|<xsl:value-of select="@url"/>]</xsl:template>

<xsl:template match="emphasis"><xsl:if test="@role='strong'">*<xsl:value-of select="normalize-space(.)"/>*</xsl:if><xsl:if test="@role='italic'">_<xsl:value-of select="normalize-space(.)"/>_</xsl:if></xsl:template>

</xsl:stylesheet>