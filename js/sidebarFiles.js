listFileMode = "listFiles";
hiddenFolders = ["/tmp/"];
oldFilePath = null;
var fileTreeListener = {};
$(function() {
    $("#sidebarFileTree").contextMenu({
        selector: 'li.folder',
        callback: function(key, options) {
            var m = "clicked: " + key + " on " + $(this).attr("basepath");
            folderEvents(key, this, options);
        },
        items: {
            "copy": {
                name: "Copy",
                icon: "fa-copy"
            },
            "cut": {
                name: "Cut",
                icon: "fa-cut"
            },
            "paste": {
                name: "Paste",
                icon: "fa-paste"
            },
            "sep1": "---------",
            "refresh": {
                name: "Refresh",
                icon: "fa-refresh"
            },
            "sep2": "---------",
            "rename": {
                name: "Rename",
                icon: "fa-italic"
            },
            "delete": {
                name: "Delete",
                icon: "fa-trash"
            },
            "sep3": "---------",
            "newFile": {
                name: "New File",
                icon: "fa-file"
            },
            "newFolder": {
                name: "New Folder",
                icon: "fa-folder"
            },
            "sep4": "---------",
            "upload": {
                name: "Upload",
                icon: "fa-upload"
            }
        }
    });
    $("#sidebarFileTree").contextMenu({
        selector: 'li.file',
        callback: function(key, options) {
            var m = "clicked: " + key + " on " + $(this).attr("filepath");
            fileEvents(key, this, options);
        },
        items: {
            "copy": {
                name: "Copy",
                icon: "fa-copy"
            },
            "cut": {
                name: "Cut",
                icon: "fa-cut"
            },
            "clone": {
                name: "Clone",
                icon: "fa-clone"
            },
            "sep1": "---------",
            "refresh": {
                name: "Refresh",
                icon: "fa-refresh"
            },
            "sep2": "---------",
            "rename": {
                name: "Rename",
                icon: "fa-italic"
            },
            "delete": {
                name: "Delete",
                icon: "fa-trash"
            }
        }
    });


    $("#sidebarFileTree").delegate("li.file[basepath]>span", "click", function(e) {
        e.preventDefault();

        $("#sidebarFileTree").find("li.active").removeClass('active');
        //$(this).closest("li.folder").addClass('active');

        file = $(this).parent();
        bp = file.attr("filepath");
        ttl = file.text();
        lx = _link("modules/cmsEditor") + "&type=edit&src=" + encodeURIComponent(bp);
        openLinkFrame(ttl, lx);
    });

    loadFileTree();
});

//File Event Functionality
function fileEvents(cmd, eleTag, opts) {
    bpath = $(eleTag).attr("basepath");
    fpath = $(eleTag).attr("filepath");
    bname = bpath.split("/");
    bname = bname[bname.length - 1];

    switch (cmd) {
        case "refresh":
            loadFileTree();
            break;
        case "delete":
            lgksConfirm("Are you sure about deleting this file :" + bname, "Delete File!", function(ans) {
                if (ans) {
                    processAJAXPostQuery(_service("files") + "&action=rm", "path=" + fpath, function(txt) {
                        if (txt != null && txt.length > 0) {
                            if (txt.indexOf("success") >= 0) {
                                $(eleTag).detach();
                                //loadFileTree(txt.substr(5));
                            } else {
                                lgksToast(txt);
                            }
                        } else {
                            lgksToast("Error deleting file.<br>Please check if files are not readonly.");
                        }
                    });
                }
            });
            break;
        case "rename":
            break;
        case "cut":
            break;
        case "copy":
            break;
        case "clone":
            break;

    }
}

function folderEvents(cmd, eleTag, opts) {
    bpath = $(eleTag).attr("basepath");
    bname = bpath.split("/");
    bname = bname[bname.length - 1];

    switch (cmd) {
        case "refresh":
            loadFileTree();
            break;
        case "upload":
            lx = _link("modules/cmsUploader");
            a = openLinkFrame("New Uploads", lx);
            if (a === false) {
                $.each(fileTreeListener, function(k, func) {
                    try {
                        func(getFileTree().find(".branch.active").attr("basepath"));
                    } catch (e) {}
                });
            }
            break;
        case "newFile":
            lgksPrompt("New File @ <smaller>" + bpath + "</smaller>", "New File", function(ans) {
                if (ans != null && ans.length > 1) {
                    if (ans.split(".").length <= 1) {
                        lgksAlert("The new file name <b>" + ans + "</b> does not contain a file type (php,js,html, etc.).");
                    } else {
                        processAJAXPostQuery(_service("files") + "&action=newFile", "path=" + (bpath + ans), function(txt) {
                            if (txt != null && txt.length > 0) {
                                if (txt.indexOf("FILE:") >= 0) {
                                    loadFileTree(txt.substr(5));

                                    lx = _link("modules/cmsEditor") + "&type=autocreate&ext=text&src=" + txt.substr(5);
                                    a = openLinkFrame(ans, lx);
                                } else {
                                    lgksToast(txt);
                                }
                            } else {
                                lgksToast("Error creating folder.<br>Please check if folders are not readonly.");
                            }
                        });
                    }
                }
            });
            break;
        case "newFolder":
            lgksPrompt("New Folder @ <smaller>" + bpath + "</smaller>", "New File", function(ans) {
                if (ans != null && ans.length > 1) {
                    processAJAXPostQuery(_service("files") + "&action=newFolder", "path=" + (bpath + ans), function(txt) {
                        if (txt != null && txt.length > 0) {
                            if (txt.indexOf("FILE:") >= 0) {
                                loadFileTree(txt.substr(5));
                            } else {
                                lgksToast(txt);
                            }
                        } else {
                            lgksToast("Error creating folder.<br>Please check if folders are not readonly.");
                        }
                    });
                }
            });
            //lx=_link("modules/cmsEditor")+"&type=new&ext=text";
            //openLinkFrame("New File",lx);
            break;
        case "delete":
            lgksConfirm("Are you sure about deleting this folder :" + bname, "Delete File!", function(ans) {
                if (ans) {
                    processAJAXPostQuery(_service("files") + "&action=rm", "path=" + bpath, function(txt) {
                        if (txt != null && txt.length > 0) {
                            if (txt.indexOf("success") >= 0) {
                                $(eleTag).detach();
                            } else {
                                lgksToast(txt);
                            }
                        } else {
                            lgksToast("Error deleting folder.<br>Please check if folders are not readonly.");
                        }
                    });
                }
            });
            break;
        case "rename":
            break;

        case "cut":
            break;
        case "copy":
            break;
        case "paste":
            break;

        case "download":
            //Download ZIP
            break;
    }
}
//Tree Functionality
function loadFileTree(file) {
    $('#sidebarFileTree').html("<div class='ajaxloading ajaxloading8'></div>");
    lx = _service("files") + "&action=" + listFileMode + "&format=json";
    processAJAXQuery(lx, function(txt) {
        try {
            json = $.parseJSON(txt);
            $('#sidebarFileTree').html("");
            loadFileTreeObj(json.Data, "/");
            $('#sidebarFileTree').treed({
                openedClass: 'glyphicon-folder-open',
                closedClass: 'glyphicon-folder-close'
            });

            revealFile(file);
        } catch (e) {
            console.error(e);
        }
    });
}

function loadFileTreeObj(obj, basePath) {
    if (hiddenFolders.indexOf(basePath) >= 0) return;
    $.each(obj, function(k, v) {
        if ((typeof v) == "object") {
            newBasePath = basePath + k + "/";
            if (hiddenFolders.indexOf(newBasePath) >= 0) return;
            html = "<li class='folder' basepath='" + newBasePath + "'><i class='indicator glyphicon glyphicon-folder-close'></i>" + k + "<ul></ul></li>";
            if (basePath.length > 1) {
                $("#sidebarFileTree li[basepath='" + basePath + "']>ul").prepend(html);
            } else {
                $('#sidebarFileTree').append(html);
            }

            loadFileTreeObj(v, newBasePath);
        } else {
            html = "<li class='file' basepath='" + basePath + "' filepath='" + ((basePath + "/" + v).replace("//", "/")) + "'><i class='indicator glyphicon glyphicon-file'></i><span>" + v + "</span></li>";
            if (basePath.length > 1) {
                $("#sidebarFileTree li[basepath='" + basePath + "']>ul").prepend(html);
            } else {
                $('#sidebarFileTree').append(html);
            }
        }
    });
    $('#sidebarFileTree>li[basepath="/"]').each(function() {
        $('#sidebarFileTree').append(this);
    });
}

function revealFile(fileS) {
    if (fileS == null) return;
    if (typeof fileS == 'string') {
        file = $("#sidebarFileTree li[filepath='" + fileS + "']");

        if (file == null || file.length <= 0) {
            file = $("#sidebarFileTree li[basepath='" + fileS + "/']");
        }
        if (file == null || file.length <= 0) {
            return;
        }
    } else {
        file = fileS;
    }

    file.addClass("active").parent().find(">li").css("display", "list-item");
    if (!file.closest("ul").hasClass("sidebarTree")) {
        revealFile(file.parent().parent());
    }
}

function registerFileTreeListener(key, func) {
    fileTreeListener[key] = func;
}

function searchFileTree() {

}