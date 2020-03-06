<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:xs="http://www.w3.org/2001/XMLSchema">

<xsl:template match="@*">
    <xsl:copy/>
</xsl:template>

<xsl:template match="*">
  <xsl:element name="{translate(local-name(), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')}">
    <xsl:apply-templates select="@*|node()"/>
  </xsl:element>
</xsl:template>

</xsl:stylesheet>
