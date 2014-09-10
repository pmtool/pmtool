<?xml version="1.0"?>
<!--
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/templates/report/worksheet/template.xsl,v $
 * $Revision: 1.1.1.1 $
 * $Date: 2003/07/28 19:23:09 $
 * $Author: willuhn $
 * $Locker:  $
 * $State: Exp $
 *
 * Copyright (c) by willuhn.webdesign
 * All rights reserved
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 ***************************************************************************/
//-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes"/>
  <xsl:template match="/">
    <html>
      <head>
        <style type="text/css">
          body  {
            background-color    : #ffffff;
            color               : #000000;
            margin-left         : 10px;
            margin-right        : 10px;
            margin-top          : 10px;
            margin-bottom       : 10px;
          }
          p,h1,h2,h3,h4,ul,ol,li,div,td,th,address,blockquote,nobr,b,i {
            font-family         : Verdana, Helvetica, Arial;
            color               : #000000;
            font-size           : 8pt;
          }

          h1 {
            margin-top          : 0pt;
            margin-bottom       : 10pt;
            margin-right        : 10pt;
            font-size           : 10pt;
            font-weight         : bold;
          }

          h2 {
            margin-top          : 0pt;
            margin-bottom       : 9pt;
            font-weight         : bold;
          }

          th {
            background-color    : #f1f1f1;
            text-align          : left;
          }

          th.right {
            background-color    : #f1f1f1;
            text-align          : right;
          }

          li {
            margin-top          : 4pt;
            margin-bottom       : 4pt;
          }

          pre  {
            font-family         : Courier New;
            font-size           : 8pt;
          }

          a  {
            text-decoration     : none;
            font-size           : 8pt;
          }

          a:link  {
            color               : #000055;
          }

          a:active  {
            color               : #222299;
            text-decoration     : underline;
          }

          a:visited  {
            color               : #555555;
          }

          a:hover  {
            color               : #222299;
            text-decoration     : underline;
          }

          .comment {
            color               : #999999;
            font-size           : 7pt;
          }

          .small {
            font-size           : 7pt;
          }

          .smallbold {
            font-size           : 7pt;
            font-weight         : bold;
          }

          .red {
            color               : #CA2222;
          }

          .yellow {
            color               : #CED02A;
          }

          .green {
            color               : #169216;
          }

          .black {
            color               : #000000;
          }

          .gray {
            color               : #808080;
          }
        </style>
      </head>
      <body>
        <xsl:apply-templates/>
      </body>
    </html>
  </xsl:template>

  <xsl:template match="report">
    <table border="0" width="100%">
      <tr>
        <td rowspan="2"><img src="grafx/dummy.gif" width="20" height="1" border="0"/></td>
        <td valign="bottom">
          <table border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td>
                <table border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="small"><nobr><u>olaf.willuhn webdesign - Ungerstr. 21 - 04318 Leipzig</u></nobr></td>
                  </tr>
                  <tr>
                    <td>
                      <br/>
                      <xsl:value-of select="company"></xsl:value-of><br/>
                      <xsl:value-of select="firstname"></xsl:value-of> <xsl:value-of select="lastname"></xsl:value-of><br/>
                      <xsl:value-of select="street"></xsl:value-of>
                      <br/><br/>
                      <b><xsl:value-of select="zip"></xsl:value-of> <xsl:value-of select="city"></xsl:value-of></b>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <img src="grafx/dummy.gif" width="1" height="38" border="0"/>
        </td>
        <td valign="top" align="right">
          <table border="0">
            <tr>
              <td>
                <img src="templates/report/worksheet/logo.gif" width="191" height="76" border="0"/>
                <br/>
                olaf.willuhn webdesign
                <br/>
                Ungerstr. 21
                <br/>
                04318 Leipzig
                <br/><br/>
                <table border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="comment">tel:</td>
                    <td class="comment">+49 (341) 6 99 12 75-0</td>
                  </tr>
                  <tr>
                    <td class="comment">fax:</td>
                    <td class="comment">+49 (341) 6 99 12 75-39</td>
                  </tr>
                  <tr>
                    <td class="comment">web:</td>
                    <td class="comment">http://www.willuhn.de/webdesign/</td>
                  </tr>
                </table>
                <br/>
                <table border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td valign="top">Steuernummer:<img src="grafx/dummy.gif" width="9" height="1" border="0"/></td>
                    <td>230/287/07150<br/>(FA Leipzig I)</td>
                  </tr>
                  <tr>
                    <td>Rechnungsdatum:<img src="grafx/dummy.gif" width="9" height="1" border="0"/></td>
                    <td><xsl:value-of select="date"></xsl:value-of></td>
                  </tr>
                  <tr>
                    <td><b>Kundennummer:</b><img src="grafx/dummy.gif" width="9" height="1" border="0"/></td>
                    <td><b><xsl:value-of select="number"></xsl:value-of></b></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
        <td rowspan="2"><img src="grafx/dummy.gif" width="20" height="1" border="0"/></td>
      </tr>
      <tr>
        <td colspan="2">
          <br/>
          <br/>
          <br/>
          <br/>
          <br/>
          <br/>
          <br/>
          <br/>
          <h1>worksheet for project &quot;<xsl:value-of select="subject"></xsl:value-of>&quot;</h1>
          <br/>
          <br/>
          <table border="0" width="100%" cellpadding="2" cellspacing="1" bgcolor="#ffffff">
            <tr>
              <th>comment</th>
              <th>start</th>
              <th>stop</th>
              <th class="right"><nobr>used time</nobr></th>
            </tr>
            <xsl:for-each select="task">
              <tr>
                <td valign="top">
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><b>task</b></td>
                      <td><b>:</b></td>
                      <td rowspan="3"><img src="grafx/dummy.gif" width="7" height="1" border="0"/></td>
                      <td><b><xsl:value-of select="subject"></xsl:value-of></b></td>
                    </tr>
                    <tr>
                      <td>type</td>
                      <td>: </td>
                      <td><xsl:value-of select="type"></xsl:value-of></td>
                    </tr>
                    <tr>
                      <td>status</td>
                      <td>:</td>
                      <td><nobr><xsl:value-of select="status"></xsl:value-of></nobr></td>
                    </tr>
                  </table>
                </td>
                <td colspan="3"><img src="grafx/dummy.gif" width="1" height="1" border="0"/></td>
              </tr>
              <xsl:for-each select="job">
                <tr>
                  <td valign="top"><xsl:value-of select="comment"></xsl:value-of></td>
                  <td valign="top"><xsl:value-of select="start"></xsl:value-of></td>
                  <td valign="top"><xsl:value-of select="stop"></xsl:value-of></td>
                  <td valign="top" align="right"><xsl:value-of select="used"></xsl:value-of></td>
                </tr>
              </xsl:for-each>
              <tr>
                <td align="right" colspan="3">summary:</td>
                <td align="right" valign="top"><nobr><xsl:value-of select="customersummaryrounded"></xsl:value-of></nobr></td>
              </tr>
              <tr>
                <td colspan="4"><img src="grafx/dummygrey.gif" width="100%" height="1" border="0"/></td>
              </tr>
            </xsl:for-each>
            <tr>
              <td colspan="4"><img src="grafx/dummy.gif" width="1" height="20" border="0"/></td>
            </tr>
            <tr>
              <td align="right" colspan="3"><b>overall summary:</b></td>
              <td align="right"><b><nobr><xsl:value-of select="customersummaryrounded"></xsl:value-of></nobr></b></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </xsl:template>
</xsl:stylesheet>


