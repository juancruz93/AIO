<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\General\Misc;

/**
 * Description of EscaperManager
 *
 * @author desarrollo3
 */
class EscaperManager {

  public $character = array(
      32 => "\xE2\x80\xAFy",
      128 => "\xe2\x82\xac",
      130 => "\xe2\x80\x9a",
      131 => "\xc6\x92",
      132 => "\xe2\x80\x9e",
      133 => "\xe2\x80\xa6",
      134 => "\xe2\x80\xa0",
      135 => "\xe2\x80\xa1",
      136 => "\xcb\x86",
      137 => "\xe2\x80\xb0",
      138 => "\xc5\xa0",
      139 => "\xe2\x80\xb9",
      140 => "\xc5\x92",
      142 => "\xc5\xbd",
      145 => "\xe2\x80\x98",
      146 => "\xe2\x80\x99",
      147 => "\xe2\x80\x9c",
      148 => "\xe2\x80\x9d",
      149 => "\xe2\x80\xa2",
      150 => "\xe2\x80\x93",
      151 => "\xe2\x80\x94",
      152 => "\xcb\x9c",
      153 => "\xe2\x84\xa2",
      154 => "\xc5\xa1",
      155 => "\xe2\x80\xba",
      156 => "\xc5\x93",
      158 => "\xc5\xbe",
      159 => "\xc5\xb8"
  );

  public function __construct() {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
  }

  public function toUtf8($text) {
//    $max = $this->strlen($text);
    $buf = str_replace("\xE2\x80\xAF", " ", $text);
    
//    for ($i = 0; $i < $max; $i++) {
//      $c1 = $text{$i};
////      $c2 = $i + 1 >= $max ? "\x00" : $text{$i + 1};
////      $c3 = $i + 2 >= $max ? "\x00" : $text{$i + 2};
////      $c4 = $i + 3 >= $max ? "\x00" : $text{$i + 3};
////      str_replace($c1, $i, $buf);
//      $this->logger->log($c1);
////      if ($c1 >= "\xc0") { //Should be converted to UTF8, if it's not UTF8 already
////        $c2 = $i + 1 >= $max ? "\x00" : $text{$i + 1};
////        $c3 = $i + 2 >= $max ? "\x00" : $text{$i + 2};
////        $c4 = $i + 3 >= $max ? "\x00" : $text{$i + 3};
////        if ($c1 >= "\xc0" & $c1 <= "\xdf") { //looks like 2 bytes UTF8
////          if ($c2 >= "\x80" && $c2 <= "\xbf") { //yeah, almost sure it's UTF8 already
////            $buf .= $c1 . $c2;
////            $i++;
////          } else { //not valid UTF8.  Convert it.
////            $cc1 = (chr(ord($c1) / 64) | "\xc0");
////            $cc2 = ($c1 & "\x3f") | "\x80";
////            $buf .= $cc1 . $cc2;
////          }
////        } elseif ($c1 >= "\xe0" & $c1 <= "\xef") { //looks like 3 bytes UTF8
////          if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf") { //yeah, almost sure it's UTF8 already
////            $buf .= $c1 . $c2 . $c3;
////            $i = $i + 2;
////          } else { //not valid UTF8.  Convert it.
//////            $cc1 = (chr(ord($c1) / 64) | "\xc0");
//////            $cc2 = ($c1 & "\x3f") | "\x80";
//////            $buf .= $cc1 . $cc2;
////          }
////        } elseif ($c1 >= "\xf0" & $c1 <= "\xf7") { //looks like 4 bytes UTF8
//////          if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf") { //yeah, almost sure it's UTF8 already
//////            $buf .= $c1 . $c2 . $c3 . $c4;
//////            $i = $i + 3;
//////          } else { //not valid UTF8.  Convert it.
//////            $cc1 = (chr(ord($c1) / 64) | "\xc0");
//////            $cc2 = ($c1 & "\x3f") | "\x80";
//////            $buf .= $cc1 . $cc2;
//////          }
////        } else { //doesn't look like UTF8, but should be converted
//////          $cc1 = (chr(ord($c1) / 64) | "\xc0");
//////          $cc2 = (($c1 & "\x3f") | "\x80");
//////          $buf .= $cc1 . $cc2;
////        }
////      } elseif (($c1 & "\xc0") == "\x80") { // needs conversion
////        if (isset($this->character[ord($c1)])) { //found in Windows-1252 special cases
//////          $buf .= $this->character[ord($c1)];
////        } else {
//////          $cc1 = (chr(ord($c1) / 64) | "\xc0");
//////          $cc2 = (($c1 & "\x3f") | "\x80");
//////          $buf .= $cc1 . $cc2;
////        }
////      } else { // it doesn't need conversion
////        $buf .= $c1;
////      }
//    }

    return $buf;
  }

  public function strlen($text) {
    return (function_exists('mb_strlen') && ((int) ini_get('mbstring.func_overload')) & 2) ?
            mb_strlen($text, '8bit') : strlen($text);
  }

}
