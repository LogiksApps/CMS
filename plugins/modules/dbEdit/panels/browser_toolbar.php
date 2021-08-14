<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$dbData=_db($dbKey)->_selectQ($src[1],"count(*) as max")->_GET();
if(!$dbData) $dbData = [["max"=>0]];
?>
<div class='container-fluid'>
    <div class='row'>
        <div class='col-md-6 col-md-offset-3'>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-secondary btn-browse-prevpage" <?=($_GET['page']==0?"disabled":"")?>><i class='fa fa-arrow-left'></i></button>
                <select id='per_page_data' class='select btn btn-secondary' data-page='<?=$_GET['page']?>' data-limit='<?=$_GET['limit']?>' data-max='<?=$dbData[0]['max']?>'>
                    <?php
                        $pageDataArr = [10,25,50,100];
                        $startRecord =  $_GET['page']*$_GET['limit'];
                        $endRecord = $startRecord + $_GET['limit'];
                        
                        foreach($pageDataArr as $d) {
                            if($d==$_GET['limit']) {
                                echo "<option value='{$d}' selected>{$d} Per Page</option>";
                            } else {
                                echo "<option value='{$d}'>{$d} Per Page</option>";
                            }
                        }
                    ?>
                </select>
                <label class='btn btn-secondary'>
                    Displaying <?=$startRecord?>-<?=$endRecord?> of <?=$dbData[0]['max']?> records
                </label>
                <button type="button" class="btn btn-secondary btn-browse-nextpage" <?=(($_GET['page']+1)*$_GET['limit']>$dbData[0]['max'])?"disabled":""?>><i class='fa fa-arrow-right'></i></button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $(".btn-browse-prevpage").click(showDataPrevPage);
    $(".btn-browse-nextpage").click(showDataNextPage);
    $("#per_page_data").change(updateDataPage);
});
function showDataNextPage() {
    var p = $("#per_page_data").data("page");
    var l = $("#per_page_data").val();
    var m = $("#per_page_data").data("max");
    
    p++;
    if(p*l>m) {
        p--;
        lgksToast("All records have been displayed");
        return;
    }
    
    var q = "&page="+p+"&limit="+l;
    loadDataContent(currentDBQueryPanel, q);
}
function showDataPrevPage() {
    var p = $("#per_page_data").data("page");
    var l = $("#per_page_data").val();
    var m = $("#per_page_data").data("max");
    
    p--;
    if(p<0) {
        p++;
        lgksToast("All records have been displayed");
        return;
    }
    
    var q = "&page="+p+"&limit="+l;
    loadDataContent(currentDBQueryPanel, q);
}
function updateDataPage() {
    var p = $("#per_page_data").data("page");
    var l = $("#per_page_data").val();
    var m = $("#per_page_data").data("max");
    
    var q = "&page="+p+"&limit="+l;
    loadDataContent(currentDBQueryPanel, q);
}
</script>