<?
  $file = '../../protected/files/'.$_GET['f'];
  if(file_exists($file))
  {
    #-- mime-magic, type will be detected by analyzing the content
    if (!function_exists("mime_content_type")) {
       function mime_content_type($fn) {
    
          static $mime_magic_data;
    
          #-- fallback
          $type = false;
    
          #-- read in first 3K of given file
          if (is_dir($fn)) {
             return("httpd/unix-directory");
          }
          elseif (is_resource($fn) || ($fn = @fopen($fn, "rb"))) {
             $bin = fread($fn, $maxlen=3072);
             fclose($fn);
          }
          elseif (!file_exists($fn)) {
             return false;
          }
          else {
             return("application/octet-stream");   // give up
          }
          
          #-- use the appropriate external tool
          /*
          if (function_exists("exec") && !ini_get("safe_mode")) {     
             $type = exec("file -iL " . escapeshellcmd($fn) . " 2>/dev/null");
             if ($type = trim(strtok(substr(strrchr($type, ":"), 1), ";"))) {
                 return $type;
             }
          }
          */
    
          #-- use PECL::fileinfo when available
          if (function_exists("finfo_buffer")) {
             $fi = new finfo(FILEINFO_MIME_TYPE);
             $type = $fi->buffer($bin);
             return($type);
          }
          
          #-- read in magic data, when called for the very first time
          if (!isset($mime_content_type)) {
          
             if(file_exists($fn = '../../protected/magic.mime'))
             {
                $mime_magic_data = array();
    
                #-- read in file
                $f = fopen($fn, "r");
                $fd = fread($f, 1<<20);
                fclose($f);
                $fd = str_replace("       ", "\t", $fd);
    
                #-- look at each entry
                foreach (explode("\n", $fd) as $line) {
    
                   #-- skip empty lines
                   if (!strlen($line) or ($line[0] == "#") or ($line[0] == "\n")) {
                      continue;
                   }
    
                   #-- break into four fields at tabs
                   $l = preg_split("/\t+/", $line);
                   @list($pos, $typestr, $magic, $ct) = $l;
    #print_r($l);
    
                   #-- ignore >continuing lines
                   if ($pos[0] == ">") {
                      continue;
                   }
                   #-- real mime type string?
                   $ct = strtok($ct, " ");
                   if (!strpos($ct, "/")) {
                      continue;
                   }
    
                   #-- mask given?
                   $mask = 0;
                   if (strpos($typestr, "&")) {
                      $typestr = strtok($typestr, "&");
                      $mask = strtok(" ");
                      if ($mask[0] == "0") {
                         $mask = ($mask[1] == "x") ? hexdec(substr($mask, 2)) : octdec($mask);
                      }
                      else {
                         $mask = (int)$mask;
                      }
                   }
    
                   #-- strip prefixes
                   if ($magic[0] == "=") {
                      $magic = substr($magic, 1);
                   }
    
                   #-- convert type
                   if ($typestr == "string") {
                      $magic = stripcslashes($magic);
                      $len = strlen($magic);
                      if ($mask) { 
                         continue;
                      }
                   }
                   #-- numeric values
                   else {
                      $mask or $mask = 0xFFFFFFFF;
    
                      if ((ord($magic[0]) < 48) or (ord($magic[0]) > 57)) {
    #echo "\nmagicnumspec=$line\n";
    #var_dump($l);
                         continue;  #-- skip specials like  >, x, <, ^, &
                      }
    
                      #-- convert string representation into int
                      if ((strlen($magic) >= 4) && ($magic[1] == "x")) {
                         $magic = hexdec(substr($magic, 2));
                      }
                      elseif ($magic[0]) {
                         $magic = octdec($magic);
                      }
                      else {
                         $magic = (int) $magic;
                         if (!$magic) { continue; }   // zero is not a good magic value anyhow
                      }
    
                      #-- different types               
                      switch ($typestr) {
    
                         case "byte":
                            $len = 1;
                            break;
                            
                         case "beshort":
                            $magic = ($magic >> 8) | (($magic & 0xFF) << 8);
                         case "leshort":
                         case "short":
                            $len = 2;
                            break;
                         
                         case "belong":
                            $magic = (($magic >> 24) & 0xFF)
                                   | (($magic >> 8) & 0xFF00)
                                   | (($magic & 0xFF00) << 8)
                                   | (($magic & 0xFF) << 24);
                         case "lelong":
                         case "long":
                            $len = 4;
                            break;
    
                         default:
                            // date, ldate, ledate, leldate, beldate, lebelbe...
                            continue;
                      }
                   }
                   
                   #-- add to list
                   $mime_magic_data[] = array($pos, $len, $mask, $magic, trim($ct));
                }
             }
    #print_r($mime_magic_data);
          }
          
          
          #-- compare against each entry from the mime magic database
          foreach ($mime_magic_data as $def) {
    
             #-- entries are organized as follows
             list($pos, $len, $mask, $magic, $ct) = $def;
             
             #-- ignored entries (we only read first 3K of file for opt. speed)
             if ($pos >= $maxlen) {
                continue;
             }
    
             $slice = substr($bin, $pos, $len);
             #-- integer comparison value
             if ($mask) {
                $value = hexdec(bin2hex($slice));
                if (($value & $mask) == $magic) {
                   $type = $ct;
                   break;
                }
             }
             #-- string comparison
             else {
                if ($slice == $magic) {
                   $type = $ct;
                   break;
                }
             }
          }// foreach
          
          #-- built-in defaults
          if (!$type) {
          
             #-- some form of xml
             if (strpos($bin, "<"."?xml ") !== false) {
                return("text/xml");
             }
             #-- html
             elseif ((strpos($bin, "<html>") !== false) || (strpos($bin, "<HTML>") !== false)
             || strpos($bin, "<title>") || strpos($bin, "<TITLE>")
             || (strpos($bin, "<!--") !== false) || (strpos($bin, "<!DOCTYPE HTML ") !== false)) {
                $type = "text/html";
             }
             #-- mail msg
             elseif ((strpos($bin, "\nReceived: ") !== false) || strpos($bin, "\nSubject: ")
             || strpos($bin, "\nCc: ") || strpos($bin, "\nDate: ")) {
                $type = "message/rfc822";
             }
             #-- php scripts
             elseif (strpos($bin, "<"."?php") !== false) {
                return("application/x-httpd-php");
             }
             #-- plain text, C source or so
             elseif (strpos($bin, "function ") || strpos($bin, " and ")
             || strpos($bin, " the ") || strpos($bin, "The ")
             || (strpos($bin, "/*") !== false) || strpos($bin, "#include ")) {
                return("text/plain");
             }
    
             #-- final fallback
             else {
                $type = "application/octet-stream";
             }
          }
          
          
    
          #-- done
          return $type;
       }
    }
    $mime = mime_content_type($file);
    $mtime = date('Y-m-d H:i:s \G\M\T',filemtime($file));
    date_default_timezone_set('America/New_York');
    $log = fopen('../../protected/file_access.tsv', 'a');
    fwrite($log, "$file\t".date('Y-m-d H:i:s')."\t".$_SERVER['REMOTE_ADDR']."\n");
    fclose($log);
    
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: $mime");
    header('Content-Length: '. filesize($file));
    header('Content-Disposition: attachment; filename="'.$_GET['f'].'"');
    
    readfile($file);
  }
  else
  {
    header('Location: http://'.$_SERVER[HTTP_HOST].'/'.$_GET['f']);//will 404
  }
?>
