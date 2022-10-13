<?php
if(!defined('ROOT')) exit('No direct script access allowed');

?>
<style>
.table-creator tfoot .fa {
    color:white !important;
}
.table-creator tbody tr td, .table-creator tbody tr th {
    padding: 0px !important;
}
.table-creator tbody tr td input[type=checkbox], .table-creator tbody tr td input[type=radio]{
    margin: auto;
    border: 0px;
    width: auto;
}
.table-creator tbody tr .remove-row {
    margin: 9px;
}
.table-creator tbody tr th:first-child {
    cursor: pointer;
}
.table-creator input[required], .table-creator select[required] {
    border:1px solid rgba(177, 92, 92, 0.8);
}
</style>
<div class='container-fluid' style='margin:auto;width:90%;margin-top:20px;'>
    <form id='tableCreatorForm'>
    <table class="table table-bordered table-creator">
        <thead>
            <tr><th colspan=100 class='text-right'>
                    <div class='pull-left' style='width:35%;'>
                        <input name="tbl_name" type='text' required class='form-control' placeholder='Table Name' />
                    </div>
                    <button type='button' class='btn btn-default' onclick="addColumn()"><i class='fa fa-plus'></i></button>
                </th>
            </tr>
            <tr>
                <th>SL#</th>
                <th>Column Name</th>
                <th>Type</th>
                <th>Length/Value</th>
                <th>Default</th>
                <th>Collation</th>
                <th>Attributes</th>
                <th>Null</th>
                <th>A.I.</th>
                <th>Index</th>
                <th>Comments</th>
                
                <th>--</th>
            </tr>
        </thead>
        <tbody>
          
        </tbody>
        <tfoot class='buttonbar hidden'>
            <tr><th colspan=100 class='text-right'>
                    <button type='button' class='btn btn-danger pull-left' onclick="resetTableCreatorForm(this)"><i class='fa fa-trash'></i> Reset</button>
                    
                    <button type='button' class='btn btn-success pull-right' onclick="submitCreateTable(this)" style='margin-right: 10px;'><i class='fa fa-save'></i> Save</button>
                    <button type='button' class='btn btn-info pull-right' onclick="previewCreateTable(this)" style='margin-right: 10px;'><i class='fa fa-file-alt'></i> Preview</button>
                </th>
            </tr>
        </tfoot>
        <tfoot class=''>
            <tr>
                <th colspan=3>
                    <select name='tbl_storage_engine' id='tbl_storage_engine' class='form-control'></select>
                </th>
                <th colspan=3>
                    <select name='tbl_collation' id='tbl_collation' class='form-control'></select>
                </th>
                <th colspan=10>
                </th>
            </tr>
        </tfoot>
    </table>
    </form>
</div>
<script>
collationData = "";
columnType = "";
$(function() {
    processAJAXQuery(_service("dbEdit","data")+"&src=column_type", function(data) {
        columnType = data;
    });
    processAJAXQuery(_service("dbEdit","data")+"&src=collation_engine", function(data) {
        collationData = data;
        $("#tbl_collation").html(collationData);
        $("#tbl_collation").val("utf8_general_ci");
    });
    
    $("#tableCreatorForm tbody")
        .sortable({
            connectWith: "#tableCreatorForm tbody",
            items: "tr",//> tr:not(:first)
            helper: "clone",
            zIndex: 999990,
            stop: function() {
                $("#tableCreatorForm tbody tr").each(function(a,b) {
                    $(this).find("th:first-child").html(a+1);
                });
            }
        })
        .disableSelection();
    
    $("#tbl_storage_engine").load(_service("dbEdit","data")+"&src=storage_engine");
});
function addColumn() {
    html = `
        <tr>
            <th class='text-center'>`+($("#tableCreatorForm tbody tr").length+1)+`</th>
            <td><input type='text' name='name[]' class='form-control' required /></td>
            <td><select name='type[]' class='form-control'>`+columnType+`</select></td>
            <td><input type='text' name='length[]' class='form-control' /></td>
            <td><input type='text' name='default[]' class='form-control' /></td>
            <td><select name='collation[]' class='form-control'>`+collationData+`</select></td>
            <td><select name='attributes[]' class='form-control'><option value=""></option>
                    <option value="BINARY">BINARY        </option>
                    <option value="UNSIGNED">UNSIGNED        </option>
                    <option value="UNSIGNED ZEROFILL">UNSIGNED ZEROFILL        </option>
                    <option value="on update CURRENT_TIMESTAMP">on update CURRENT_TIMESTAMP        </option>
                </select></td>
            <td><select name='null[]' class='form-control'><option value='no'>No</option><option value='yes'>Yes</option></select></td>
            <td><select name='ai[]' class='form-control'><option value='no'>No</option><option value='yes'>Yes</option></select></td>
            <td><select name='index[]' class='form-control'><option value="none_0">---</option>
                    <option value="primary" title="Primary">PRIMARY</option>
                    <option value="unique" title="Unique">UNIQUE</option>
                    <option value="index" title="Index">INDEX</option>
                    <option value="fulltext" title="Fulltext">FULLTEXT</option>
                    <option value="spatial" title="Spatial">SPATIAL</option>
                </select></td>
            <td><input type='text' name='comments[]' class='form-control' /></td>
            
            <td class='action'><i class='fa fa-times remove-row' onclick='removeColumn(this)'></i></td>
          </tr>
    `;
    $("#tableCreatorForm tbody").append(html);
    $("#tableCreatorForm tfoot.buttonbar").removeClass("hidden");
}
function removeColumn(ele) {
    $(ele).closest("tr").detach();
    $("#tableCreatorForm tbody tr").each(function(a,b) {
        $(this).find("th:first-child").html(a+1);
    });
    if($("#tableCreatorForm tbody tr").length<=0) {
        $("#tableCreatorForm tfoot.buttonbar").addClass("hidden");
    }
}
function resetTableCreatorForm() {
    lgksConfirm("Are you sure about clearing the column structure?","Corfirm !", function(ans) {
        if(ans) {
            $("#tableCreatorForm tbody tr").detach();
        }
    });
}
function submitCreateTable(btn) {
    $("#tableCreatorForm tfoot.buttonbar").addClass("hidden");
    
    //validate structure
    emptyFields = 0;
    for(i=0;i<$("#tableCreatorForm input[required]").length;i++) {
        if($($("#tableCreatorForm input[required]")[i]).val().length<=0) {
            emptyFields++;
        }
    }
    if(emptyFields>0) {
        lgksAlert("All required fields are not filled.");
        return;
    }
    //ajax submit
    lx=_service("dbEdit","createTable")+"&dkey="+dkey;
    qData = $("#tableCreatorForm").serialize();
    processAJAXPostQuery(lx,qData, function(data) {
        if(data==null || data.length<=0) data = "Unknown Error Occured While Creating Table";
        
        if(data=="success") {
            loadTableList('pages');
            lgksConfirm("Database Table Created Successfully.<br><br>Reset Schema Form ?","Success", function(ans) {
                if(ans) {
                    $("#tableCreatorForm input[required]").val("");
                    $("#tableCreatorForm tbody tr").detach();
                }
            })
        } else {
            lgksAlert(data);
            $("#tableCreatorForm tfoot.buttonbar").removeClass("hidden");
        }
    });
}
function previewCreateTable(btn) {
    $("#tableCreatorForm tfoot.buttonbar").addClass("hidden");
    
    //validate structure
    emptyFields = 0;
    for(i=0;i<$("#tableCreatorForm input[required]").length;i++) {
        if($($("#tableCreatorForm input[required]")[i]).val().length<=0) {
            emptyFields++;
        }
    }
    if(emptyFields>0) {
        lgksAlert("All required fields are not filled.");
        return;
    }
    //ajax submit
    lx=_service("dbEdit","createTable")+"&preview=true&dkey="+dkey;
    qData = $("#tableCreatorForm").serialize();
    processAJAXPostQuery(lx,qData, function(data) {
        if(data==null || data.length<=0) data = "Unknown Error Occured While Creating Table";
        
        lgksAlert(data);
        $("#tableCreatorForm tfoot.buttonbar").removeClass("hidden");
    });
}
</script>