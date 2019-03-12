<?php

?>
<style>
  a:link {
    background: transparent !important;
    color: #337ab7 !important;
  }
  td, th {
    font-size: 100% !important;
  }
</style>
<a href='#asdasdasd' style='position: fixed;background: #333 !important;display: block;right: 30px;bottom: 30px;text-align: center;padding: 6px;padding-left: 7px;padding-right: 7px;border-radius: 90px;color: white !important;font-size: 30px;'><i class='fa fa-arrow-up'></i></a>
<div class='navbar navbar-default navbar-fixed-top' style="min-height: 30px;border-bottom: 0px;background: white;">
  <div class='container'>
    <ul class="nav nav-tabs nav-justified">
      <li class="active"><a href="#phpinfo">PHP Info</a></li>
      <li><a href="#menu1">Logiks Info</a></li>
    </ul>
  </div>
</div>
<div id='asdasdasd' style='width:100%;height:20px;'>
  
</div>
<div id='sinfoTabs' class='container' style='margin-top:30px;'>
  <div class="tab-content">
    <div id="phpinfo" class="tab-pane fade in active">
      <?php
        ob_start();
        phpinfo();
        ob_flush();
      ?>
    </div>
    <div id="menu1" class="tab-pane fade">
      <div class="center">
        <table>
          <tbody>
            <tr class="h">
              <td>
                  <a href="http://www.php.net/">
                    <figure style='width: 80px;height: 80px;float: right;'>
                      <img border="0" src="https://avatars0.githubusercontent.com/u/1589427?s=460&v=4" alt="Logiks logo" style='width: 100%;height: 100%;'>
                    </figure>
                  </a>
                  <h1 class="p">Logiks Version <?=Framework_Version?></h1>
              </td>
            </tr>
          </tbody>
        </table>
        <table>
          <tbody>
            <?php
              $infoArr = [
                "Framework_Title"=>Framework_Title,
                "Framework_Author"=>Framework_Author,
                "Framework_Author_EMail"=>Framework_Author_EMail,
                "Framework_Version"=>Framework_Version,
                "Framework_Status"=>Framework_Status,
                "Framework_Site"=>Framework_Site,
                "Framework_INFO"=>Framework_INFO,
                "Framework_CMD"=>Framework_CMD,
                "Framework_LIC"=>Framework_LIC,
                "Github Repo"=>"https://github.com/Logiks"
              ];
              foreach($infoArr as $a=>$b) {
                echo "<tr>";
                echo "<td class='e'>".toTitle(_ling($a))." </td>";
                if(substr($b,0,7)=="http://" || substr($b,0,8)=="https://") {
                  echo "<td class='v'><a href='{$b}' target=_blank>{$b}</a> </td>";
                } else {
                  echo "<td class='v'>{$b} </td>";
                }
                echo "</tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
$(function() {
  $(".nav.nav-tabs li").click(function() {
    href = $(this).find("a").attr("href");
    
    $(".tab-pane.fade.in").removeClass("in").removeClass("active").hide();
    $(".nav-tabs li.active").removeClass("active");

    $(".tab-pane.fade"+href+"").addClass("in").show();
    $(".nav-tabs li a[href='"+href+"']").parent().addClass("active");
  });
});
</script>
<?php
function phpinfo2array() {
    $entitiesToUtf8 = function($input) {
        // http://php.net/manual/en/function.html-entity-decode.php#104617
        return preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $input);
    };
    $plainText = function($input) use ($entitiesToUtf8) {
        return trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));
    };
    $titlePlainText = function($input) use ($plainText) {
        return '# '.$plainText($input);
    };
    
    ob_start();
    phpinfo(-1);
    
    $phpinfo = array('phpinfo' => array());

    // Strip everything after the <h1>Configuration</h1> tag (other h1's)
    if (!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', ob_get_clean(), $matches)) {
        return array();
    }
    
    $input = $matches[1];
    $matches = array();

    if(preg_match_all(
        '#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:<\/a>)?<\/h2>)|'.
        '(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s',
        $input, 
        $matches, 
        PREG_SET_ORDER
    )) {
        foreach ($matches as $match) {
            $fn = strpos($match[0], '<th') === false ? $plainText : $titlePlainText;
            if (strlen($match[1])) {
                $phpinfo[$match[1]] = array();
            } elseif (isset($match[3])) {
                $keys1 = array_keys($phpinfo);
                $phpinfo[end($keys1)][$fn($match[2])] = isset($match[4]) ? array($fn($match[3]), $fn($match[4])) : $fn($match[3]);
            } else {
                $keys1 = array_keys($phpinfo);
                $phpinfo[end($keys1)][] = $fn($match[2]);
            }

        }
    }
    
    return $phpinfo;
}
?>