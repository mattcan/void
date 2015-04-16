<?php

require 'Parsedown.php';

class Page {

    function Page($requestUri, $phpSelf) {
        $reqPage = basename(parse_url($requestUri, PHP_URL_PATH));
        if (trim(dirname(parse_url($phpSelf, PHP_URL_PATH)), '/') === $reqPage) { $reqPage = ""; }     // check if page is home, there should be a better way to do it!

        $type = strpos($reqPage, 'article') ? 'article' : 'page';
        $pages = glob("./" . $type ."/*$reqPage.{txt,md}", GLOB_BRACE);
        if ($pages) {
            $page = $pages[0];
        } else {
            $page = "./page/HIDDEN-404.txt"; $type = 'page';
        }                 // default 404 error page

        $this->parse($page);
    }

    /**
     * Splits page into sections and gets metadata
     */
    function parse($page)
    {
        $pagestr = file_get_contents($page);
        list($pageheader, $pagecontent) = preg_split('~(?:\r?\n){2}~', $pagestr, 2);  // split into 2 parts : before/after the first blank line
  
        preg_match("/^TITLE:(.*)$/m", $pageheader, $matches1);                        // for articles: title, for pages: title displayed in top-menu
        preg_match("/^AUTHOR:(.*)$/m", $pageheader, $matches2);                       // for articles only
        preg_match("/^DATE:(.*)$/m", $pageheader, $matches3);                         // for articles only
        preg_match("/^(NOMENU:1)$/m", $pageheader, $matches4);                        // for pages only: if NOMENU:1, no link in top-menu
        preg_match("/^URL:(.*)$/m", $pageheader, $matches5);                          // for pages only: top-menu's link  (=TITLE if no URL is set)
        return array($pageheader, $pagecontent, $matches1[1], $matches2[1], $matches3[1], $matches4[1], (isset($matches5[1]) ? $matches5[1] : strtolower($matches1[1])));
    }
}
