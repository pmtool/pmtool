<?PHP
/***************************************************************************
 * $Source: /cvsroot/pmtool/pmtool/index.php,v $
 * $Revision: 1.11 $
 * $Date: 2005/02/21 07:31:39 $
 * $Author: genghishack $
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

// enable this to disable some things ;)
// CMW - HELLO, LIKE WHAT??  Comments would be helpful.
define("DEMO_MODE",false);

// Include the file that includes everything else.
if (!file_exists("inc/includer.inc.php")) {
    echo "error: inc/includer.inc.php doesn't exist <br />";
    echo "Please make sure this file is in place and then run PMTool again.";
    exit;
} 
require("inc/includer.inc.php");

/////////////////////
// first set to system language
$lang = array();
// set to default language first
if (!file_exists("lang/".$config['language'].".inc.php")) {
    echo "<b>error, no system language file found</b>.<br/>\n";
    echo "Please make sure system language files are in place and then run PMTool again.";
    exit;
}
require("lang/{$config['language']}.inc.php");
/////////////////////

if (! isset($_SESSION['loginid'])) {
    // session is not set -> authenticate

    // try to authenticate by IP
    if ($loginInst->authByIp()) {
        $_SESSION['loginid'] = $loginInst->authByIp();
    }
    // try to authenticate by username/password
    elseif (tool::securePost('loginname') &&
           $loginInst->authByPassword(tool::securePost('loginname'),tool::securePost('password')))
    {
        $_SESSION['loginid'] = $loginInst->authByPassword(tool::securePost('loginname'),tool::securePost('password'));
    }

    if (isset($_SESSION['loginid']) && $_SESSION['loginid'] != "" && ! isset($_SESSION['loginid'])) {
        $loginid = $_SESSION['loginid'];
        if (! isset($_SESSION['loginid'] )) {
            echo "<b>".$lang['common_unableToSaveLoginInSession']."</b><br>";
            // could not save session -> give up
            exit;
        }
    }

    elseif (! isset($_SESSION['loginid'] ) && (tool::securePost('loginname') || tool::securePost('password'))) {
        // show error message only, if username/password was submitted
        tool::errorStatus($lang['common_userUnknownOrPasswordWrong']);
    }
}

// Choose content for page.
if ( isset( $_SESSION['loginid'] ) ) {

   if (! isset($_SESSION['loginid']) || $_SESSION['loginid'] == "") {
        echo "<b>".$lang['common_unableToFindloginInSession']."</b><br>";
        // could not save session -> give up
        exit;
    }

    // activate user
    $loginInst->activate($_SESSION['loginid']);

    // determine actual page
    if (tool::secureGet("content") &&
        eregi("php$",tool::secureGet("content")) &&
        ! eregi("http",tool::secureGet("content"))) {
        if (!file_exists(tool::secureGet("content"))) {
            // given file does not exist
            $content = "404.php";
        } 
        else {
            // given file is okay
            $content = tool::secureGet("content");
        }
    }
    elseif (tool::secureGet('plugin')) {
        if (!$toolInst->loadPlugin(tool::secureGet('plugin'))) {
            $content = "404.php";
        }
    }
    elseif ($loginInst->isCustomer()) {
        //default page for customers
        $content = "requests.php";
    }
    else {
        // default page for users
        $content = $config['defaultpage'];
    }
}
else {
    // user has not logged in until now
    $content = "login.php";
}

// check for user specific language
if (isset($loginInst->language) && $loginInst->language != "" && file_exists("lang/".$loginInst->language.".inc.php")) {
    require("lang/".$loginInst->language.".inc.php");
}

// The only reason I'm using ob here is because the content files aren't written like this one (yet).
// Whereas this file now figures out all its output and then spits it out at the end, like a good little PHP script,
// The content files would still spit their output straight to the screen if we didn't keep control of 'em.  
// So here, the content file gets its output stuck into a string variable for output later.  Nice.
ob_start();
include($content);
$content_include = ob_get_clean();

// This is the HTML that gets displayed if we're inside the details window.
if (tool::secureGet('view') && tool::secureGet('view') == "details") {

    $content_view = <<<EOT
    <br><br>
    <table border="0" cellpadding="0" cellspacing="0" width="96%">
        <tr>
            <td>
                <div><a href="javascript:history.go(-1)">&laquo; {$lang['common_back']}</a></div>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="1" bgcolor="#ffffff" width="100%">
                    <tr>
                        <td background="grafx/bg_logo.gif">
                            <div id="pmmsg">
                            <script>
                                document.write('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                            </script>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" bgcolor="#e6e6e6">
$content_include
                        </td>
                    </tr>
                    <tr>
                        <script language="javascript">
                            update('{$global_status}');
                        </script>
                        <td background="grafx/bg_logo.gif">&nbsp;{$global_status}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
EOT;

} else {
    
    // Here's where we start building the HTML for the main screen.

    // Set up an array with menu items and their properties.
    $menu_items_arr = array( 'home'     => array( 'restricted' => false,
                                                  'label'      => $lang['common_home'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=home.php') ),

                             'user'     => array( 'restricted' => true,
                                                  'label'      => $lang['common_users'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=users.php') ),
                                                  
                             'group'    => array( 'restricted' => true,
                                                  'label'      => $lang['common_groups'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=groups.php') ),
                                              
                             'right'    => array( 'restricted' => true,
                                                  'label'      => $lang['common_rights'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=rights.php') ),
                                              
                             'prefs'    => array( 'restricted' => false,
                                                  'label'      => $lang['common_preferences'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=password.php') ),

                             'customer' => array( 'restricted' => true,
                                                  'label'      => $lang['common_customers'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=customers.php') ),
                                              
                             'project'  => array( 'restricted' => true,
                                                  'label'      => $lang['common_projects'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=projects.php') ),
                                              
                             'task'     => array( 'restricted' => true,
                                                  'label'      => $lang['common_tasks'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=tasks.php') ),
                                              
                             'report'   => array( 'restricted' => true,
                                                  'label'      => $lang['common_reports'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=reports.php') ),
                                              
                             'request'  => array( 'restricted' => true,
                                                  'label'      => $lang['common_requests'],
                                                  'href'       => $toolInst->encodeUrl('index.php?content=requests.php') ) );
                             

    // Write the HTML for the menu items.
    $content_menu_th_arr = array();
    foreach ($menu_items_arr as $key => $item) {
        if ( $loginInst->hasAccess($key) || !$item['restricted'] ) { 
            $content_menu_th_arr[] = <<<EOT
            
                        <th onmouseover="style.backgroundColor='#C4D9E8';" 
                            onmouseout="style.backgroundColor='#70A7CE';" 
                            onclick="javascript:location.href='{$item['href']}'"
                            class="navi"
                        ><a class="navi"
                            href="{$item['href']}">&nbsp;&nbsp;{$item['label']} &nbsp;&nbsp;</a>
                        </th>
EOT;
        }
    }
    $content_menu_th_str = "";
    if (isset( $content_menu_th_arr )) {
        foreach ($content_menu_th_arr as $th) {
            $content_menu_th_str .= $th;
        }
    }

    // Write the html for the plugins option list.
    if ( isset($lang['common_plugins']) ) {
        $content_plugins_label = $lang['common_plugins'];
    } else {
        $content_plugins_label = 'plugins';
    }
    $content_plugins_options = '';
    $content_plugins_options_arr = array();
    $dir = $toolInst->getDir("./plugins");
    while ($file = current($dir)) {
        if (file_exists("./plugins/$file/index.php")) {
            if (file_exists("./plugins/$file/config.inc.php")) {
                include("./plugins/$file/config.inc.php");
                $content_plugins_options_arr[] =<<<EOT

                                    <option value="$file">{$pluginconfig['title']}
EOT;
            } else {
                $content_plugins_options_arr[] =<<<EOT

                                    <option value="$file">$file
EOT;
            }
        }
        next($dir);
    }
    if (tool::secureGet('plugin') && tool::secureGet('plugin') != "" && file_exists("./plugins/".tool::secureGet('plugin')."/config.inc.php")) {
        // we need to overwrite the included config file if a plugin was choosen
        include("./plugins/".tool::secureGet('plugin')."/config.inc.php");
    }
    foreach ($content_plugins_options_arr as $option) {
        $content_plugins_options .= $option;
    }


    // Write the html for the full menu row.
    $content_menu_tr = '';
    if (isset($loginInst->id) && $loginInst->id != "") {
        $content_menu_tr = <<<EOT
                    <form method="post" name="plugins">
                    <tr>
$content_menu_th_str
                        <td background="grafx/fade.gif" align="right">
                            <nobr>
                            $content_plugins_label:
                            <select onchange="javascript:loadplugin(this.form.plugins.options[this.form.plugins.options.selectedIndex].value);" 
                                    name="plugins">
                                    <option value="">{$lang['common_choose']}
$content_plugins_options
                            </select>
                            </nobr>
                        </td>
                    </tr>
                    </form>
EOT;
    }
    
    // CMW - This really should be in the config file.  I mean, really.
    $content_release = file_get_contents('release.inc.php');

    // Write the HTML for the 'logged in as' line.
    $content_loggedinas_top = '';
    $content_loggedinas_bot = '';
    $content_loggedinas_grouplist = '';
    if (isset($loginInst->id) && $loginInst->id != "") {
        $list = $loginInst->getGroups();
        if ($list) {
            $content_loggedinas_grouplist .= ' (' . implode(',',$list) . ')';
        }
        $content_loggedinas_top = "<span class=small>{$lang['common_loggedInAs']} {$loginInst->username} "
                                . " | <a href=\"logout.php\" class=\"comment\">{$lang['common_logout']}</a>&nbsp;</span>";
        $content_loggedinas_bot = "<span class=small>{$lang['common_loggedInAs']} {$loginInst->username} "
                                . $content_loggedinas_grouplist
                                . " | <a href=\"logout.php\" class=\"comment\">{$lang['common_logout']}</a>&nbsp;</span>";
    }
    
    $display_status="";
    if (isset($global_status) && $global_status != "") {
        $display_status = $global_status;
    }

    // This is the main part of the HTML.
    $content_view = <<<EOT
    <table border="0" cellpadding="0" cellspacing="10" width="96%">
        <tr>
            <td>
                <table border="0" cellpadding="0" cellspacing="1" width="100%" bgcolor="#ffffff">
                    <tr>
                        <td background="grafx/bg_logo.gif" colspan="11">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td><img src="grafx/logo.gif" width="291" height="38" border="0"></td>
                                    <td align="right">release $content_release &nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
$content_menu_tr
                    <tr>
                        <td colspan="11" background="grafx/bg_logo.gif">
                            <table border="0" cellpadding="0" cellspacing="1" width="100%">
                                <tr>
                                    <td>
                                        <div id="pmmsg">
                                        <script>
                                            document.write('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                                        </script>
                                        </div>
                                    </td>
                                    <td align="right">&nbsp;
                                        $content_loggedinas_top
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" bgcolor="#e6e6e6" align="center">
                            <table border="0" cellpadding="0" cellspacing="0" bgcolor="#e6e6e6" width="100%">
                                <tr>
                                    <td colspan="3" bgcolor="#e6e6e6"><img src="grafx/dummy.gif" width="1" height="10" border="0"></td>
                                </tr>
                                <tr>
                                    <td bgcolor="#e6e6e6"><img src="grafx/dummy.gif" width="10" height="1" border="0"></td>
                                    <td style="background-image:url(grafx/bg_main.gif); background-repeat:no-repeat;">
$content_include
                                    </td>
                                    <td bgcolor="#e6e6e6"><img src="grafx/dummy.gif" width="10" height="1" border="0"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" bgcolor="#e6e6e6"><img src="grafx/dummy.gif" width="1" height="10" border="0"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" background="grafx/bg_logo.gif">
                            <script language="javascript">
                            <!--
                                update('$display_status');
                            //-->
                            </script>
                            <table border="0" cellpadding="0" cellspacing="1" width="100%">
                                <tr>
                                    <td>&nbsp;$display_status</td>
                                    <td align="right">&nbsp;
                                        $content_loggedinas_bot
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

EOT;

}

$content_plugin_href = $toolInst->encodeUrl("index.php?plugin=' + plugin + '");

// This is the complete HTML for the page.
$content_full = <<<EOT
<html>
<head>
    <title>PMtool</title>
    <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset={$lang['charset']}" />
    <link rel=stylesheet type="text/css" href="styles.css" />
    <script language="JavaScript" src="js/common.js" type="text/javascript"></script>
    <script language="JavaScript">
    <!--
        function loadplugin(plugin) {
            location.href='$content_plugin_href';
        }
    //-->
    </script>
    <style>
    <!--
        #pmmsg { position: relative;}
    -->
    </style>
</head>
<body>
<div align="center">

$content_view

</div>
</body>
</html>

EOT;

// Output the whole thing.
echo $content_full;

/***************************************************************************
 * $Log: index.php,v $
 * Revision 1.11  2005/02/21 07:31:39  genghishack
 * I rewrote the HTML part of the code without changing the way the display looks.  This was in order to separate the PHP code from the HTML, as it should be.  The result is a much cleaner file that will be easier to make changes to later when we decide to change the interface or want to port it to version 2.
 *
 *
 *
 ***************************************************************************/
?>
