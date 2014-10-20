<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <!-- import the default stylesheet -->
  <xsl:import href="phpunit-frames.xsl"/>

  <!-- override the template producing the test table header --> 
  <xsl:template name="testcase.test.header">
    <xsl:param name="show.class" select="''"/>
    <tr valign="top">
      <xsl:if test="boolean($show.class)">
        <th>Class</th>
      </xsl:if>
      <th>Name</th>
      <th>Status</th>
      <th width="60%">Type</th>
      <th nowrap="nowrap">Time(s)</th>

      <!-- ADDED -->
      <th>Screenshot1</th>
      <th>Screenshot2</th>

    </tr>
  </xsl:template>

  <!-- override the template producing a test table row -->
  <xsl:template match="testcase" mode="print.test">
    <xsl:param name="show.class" select="''"/>
    <tr valign="top">
      <xsl:attribute name="class">
        <xsl:choose>
          <xsl:when test="error">Error</xsl:when>
          <xsl:when test="failure">Failure</xsl:when>
          <xsl:otherwise>TableRowColor</xsl:otherwise>
        </xsl:choose>
      </xsl:attribute>
      <xsl:variable name="class.href">
        <xsl:value-of select="concat(translate(../@package,'.','/'), '/', ../@id, '_', ../@name, '.html')"/>
      </xsl:variable>
      <xsl:if test="boolean($show.class)">
        <td><a href="{$class.href}"><xsl:value-of select="../@name"/></a></td>
      </xsl:if>
      <td>
        <a name="{@name}"/>
        <xsl:choose>
          <xsl:when test="boolean($show.class)">
            <a href="{concat($class.href, '#', @name)}"><xsl:value-of select="@name"/></a>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="@name"/>
          </xsl:otherwise>
        </xsl:choose>
      </td>
      <xsl:choose>
        <xsl:when test="failure">
          <td>Failure</td>
          <td><xsl:apply-templates select="failure"/></td>
        </xsl:when>
        <xsl:when test="error">
          <td>Error</td>
          <td><xsl:apply-templates select="error"/></td>
        </xsl:when>
        <xsl:otherwise>
          <td>Success</td>
          <td></td>
        </xsl:otherwise>
      </xsl:choose>
      <td>
        <xsl:call-template name="display-time">
          <xsl:with-param name="value" select="@time"/>
        </xsl:call-template>
      </td>

      <!-- ADDED -->
      <td>
        <a href="../screenshots/{@name}1.jpg">screenshot1</a>
      </td>
      <td>
        <a href="../screenshots/{@name}2.jpg">screenshot2</a>
      </td>

    </tr>
  </xsl:template>

</xsl:stylesheet>