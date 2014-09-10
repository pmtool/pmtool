/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/js/common.js,v $
 * $Revision: 1.1.1.1 $
 * $Date: 2003/07/28 19:22:38 $
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

function update(msg) {
  if (!msg)
    msg = "";
  changeHTML('pmmsg', msg);
}

function Check() {
  chk = window.confirm("Are You sure ?");
  return chk;
}

function openwindow(url,width,height) {
  // create a random number for window name to open separate windows every time
  var windowname = Math.round(Math.random() * 1000000);
  mywindow = window.open(url, windowname, "width=" + width + ",height=" + height + ",scrollbar=auto,scrollbars=yes,menubar=no,toolbar=no,status=no,resizable=no");
  mywindow.focus();
}

function reportwindow(url) {
  reportwindow = window.open(url, "reportwindow", "width=900,height=800,scrollbar=auto,scrollbars=yes,menubar=yes,toolbar=yes,status=no,resizable=yes");
  reportwindow.focus();
}

function changeHTML(idOrPath, html) {
  if (document.layers) {
    var l = idOrPath.indexOf('.') != -1 ? eval(idOrPath)
             : document[idOrPath];
    if (!l.ol) {
      var ol = l.ol = new Layer (l.clip.width, l);
      ol.clip.width = l.clip.width;
      ol.clip.height = l.clip.height;
      ol.bgColor = l.bgColor;
      l.visibility = 'hide';
      ol.visibility = 'show';
    }
    var ol = l.ol;
    ol.document.open();
    ol.document.write(html);
    ol.document.close();
  }
  else if (document.all || document.getElementById) {
    var p = idOrPath.indexOf('.');
    var id = p != -1 ?
              idOrPath.substring(idOrPath.lastIndexOf('.') + 1)
              : idOrPath;
    if (document.all)
      document.all[id].innerHTML = html;
    else {
      var l = document.getElementById(id);
      var r = document.createRange();
      r.setStartAfter(l);
      var docFrag = r.createContextualFragment(html);
      while (l.hasChildNodes())
        l.removeChild(l.firstChild);
      l.appendChild(docFrag);
    }
  }
}

/***************************************************************************
 * $Log: common.js,v $
 * Revision 1.1.1.1  2003/07/28 19:22:38  willuhn
 * reimport
 *
 * Revision 1.6  2002/03/30 14:14:39  willuhn
 * @N added plugin loader
 *
 * Revision 1.5  2002/02/27 21:02:52  willuhn
 * @N openwindow() creates now a random window name to open separate windows every time
 *
 * Revision 1.4  2002/02/09 19:38:28  willuhn
 * @N added CVS log
 * @N added french language file
 *
 *
 ***************************************************************************/
