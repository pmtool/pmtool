<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/inc/box.inc.php,v $
 * $Revision: 1.1.1.1 $
 * $Date: 2003/07/28 19:22:58 $
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

class box {

  var $bgColor;
  var $title;
  var $align;
  var $width;
  var $height;
  var $contents="";
  var $template;

  /*********************************************************************
  ** constructor                                                     **/

  function box() {
    // fill some default values
    $this->setWidth("100");
    $this->setHeight("150");
    $this->setTemplate("templates/box/default/box.php");
  }

  /**                                                                 **
  *********************************************************************/

  /*********************************************************************
  ** public methods                                                  **/

  function setTitle($title) {
    if ($title) $this->title = $title;
  }

  function setBgColor($bgcolor) {
    if ($bgcolor) $this->bgColor = $bgcolor;
  }

  function clearContent() {
    $this->contents = "";
  }

  function addContent($contents="") {
    if (isset($contents) && $contents != "") {
      $this->contents .= $contents;
    }
  }

  function setAlign($align) {
    if ($align == "right" || $align == "left" || $align == "center") $this->align = $align;
  }

  function setHeight($height) {
    global $toolInst;
    if ($toolInst->checkInt($height)) $this->height = $height;
  }

  function setWidth($width) {
    global $toolInst;
    if ($toolInst->checkInt($width)) $this->width = $width;
  }

  function setTemplate($template) {
    if (file_exists($template)) $this->template = $template;
  }

  function get() {
    include($this->template);
  }
  /**                                                                 **
  *********************************************************************/

}

/***************************************************************************
 * $Log: box.inc.php,v $
 * Revision 1.1.1.1  2003/07/28 19:22:58  willuhn
 * reimport
 *
 * Revision 1.7  2002/04/17 19:54:43  willuhn
 * @B a lot of fixes for "register_globals=off"
 *
 * Revision 1.6  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/

?>
