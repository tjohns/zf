<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>    

<xsl:template match="chapter">
{zone-template-instance:ZFDOCDEV:manual-template}
{zone-data:content}
<xsl:apply-templates/>
{zone-data}
{zone-template-instance}
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

{tip:icon=false}
<xsl:value-of select="."/>
{tip}

</xsl:template>

<xsl:template match="para">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="listitem">
* <xsl:apply-templates/>
</xsl:template>

<xsl:template match="tip">
{tip:title=Info}
<xsl:value-of select="."/>
{tip}
</xsl:template>

<xsl:template match="programlisting">
{code:<xsl:value-of select="@role"/>}
<xsl:value-of select="."/>
{code}
</xsl:template>

<xsl:template match="example/title">
*<xsl:value-of select="."/>*

</xsl:template>

<xsl:template match="thead/row/entry">||<xsl:value-of select="."/></xsl:template>
<xsl:template match="tbody/row/entry">|<xsl:value-of select="."/></xsl:template>

<xsl:template match="para/code">{{<xsl:value-of select="."/>}}</xsl:template>

<xsl:template match="ulink">[<xsl:value-of select="."/>|<xsl:value-of select="@url"/>]</xsl:template>
   
</xsl:stylesheet>