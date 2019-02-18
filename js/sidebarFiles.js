listFileMode = "listFiles";
hiddenFolders = ["/tmp/"];
oldFilePath = null;
var fileTreeListener = {};
$(function() {
    $("#sidebarFileTree").contextMenu({
        selector: 'li.folder',
        callback: function(key, options) {
            var m = "clicked: " + key + " on " + $(this).attr("basepath");
            $("#sidebarFileTree .focused").removeClass("focused");
            $(this).addClass("focused");
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
            "collapseall":{
                  name: "Collapse All",
                  icon: "fa-compress"
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
            },
            "generate": {
                  name: "Generate !",
                  icon: "fa-code"
            }
        }
    });
    $("#sidebarFileTree").contextMenu({
        selector: 'li.file',
        callback: function(key, options) {
            var m = "clicked: " + key + " on " + $(this).attr("filepath");
            $("#sidebarFileTree .focused").removeClass("focused");
            $(this).addClass("focused");
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
            "collapseall":{
                  name: "Collapse All",
                  icon: "fa-compress"
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
//             "download": {
//                 name: "Download",
//                 icon: "fa-download"
//             }
        }
    });
  
    $("#sidebarFileTree").parent().contextMenu({
        selector: '#sidebarFileTree',
        callback: function(key, options) {
            var m = "clicked: " + key + " on " + $(this).attr("filepath");
            folderEvents(key, this, options);
        },
        items: {
            "rootdir": {
                  name: "Make Master Folder",
                  icon: "fa-copy"
            },
            "collapseall": {
                  name: "Collapse All",
                  icon: "fa-compress"
            },
            "generate": {
                  name: "Generate !",
                  icon: "fa-code"
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
  
    $("#searchField input").keydown(function(e) {
      if(e.keyCode==13) {
        searchFile();
      }
    });

    loadFileTree();
});

//File Event Functionality
function fileEvents(cmd, eleTag, opts) {
    bpath = $(eleTag).attr("basepath");
    fpath = $(eleTag).attr("filepath");
    bname = fpath.split("/");
    bname = bname[bname.length - 1];

    switch (cmd) {
        case "refresh":
            loadFileTree();
            break;
        case "collapseall":
            collapseAll();
            break;
        case "delete":
            lgksConfirm("You are about to delete file '"+bname+"'. Are you sure?", "Delete File!", function(ans) {
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
            lgksPrompt("Please give a new name for the selected file", "Rename File!", function(ans) {
                if (ans && ans.length>0) {
                    processAJAXPostQuery(_service("files") + "&action=rename", "path=" + fpath+"&newname="+ans, function(txt) {
                        if (txt != null && txt.length > 0) {
                            if (txt.indexOf("success") >= 0) {
                                loadFileTree(txt.substr(5));
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
        case "cut":
            oldFilePath = ["cut",fpath,"file"];
            break;
        case "copy":
            oldFilePath = ["copy",fpath,"file"];
            break;
        case "clone":
            processAJAXPostQuery(_service("files") + "&action=clone", "path=" + fpath, function(txt) {
                    if (txt != null && txt.length > 0) {
                        if (txt.indexOf("success") >= 0) {
                            loadFileTree(txt.substr(5));
                        } else {
                            lgksToast(txt);
                        }
                    } else {
                        lgksToast("Error coping file.<br>Please check if files are not readonly.");
                    }
                });
            break;
    }
}

function folderEvents(cmd, eleTag, opts) {
    bpath = $(eleTag).attr("basepath");
    bname = bpath;
    if(bname.substr(bname.length-1)=="/") {
        bname=bname.substr(0,bname.length-1);
    }
    bname = bname.split("/");
    bname = bname[bname.length - 1];

    switch (cmd) {
        case "refresh":
            loadFileTree();
            break;
        case "collapseall":
            collapseAll();
            break;
        case "rootdir":
            lgksPrompt("New Folder @ <smaller>" + bpath + "</smaller>", "New File", function(ans) {
                if (ans != null && ans.length > 1) {
                    processAJAXPostQuery(_service("files") + "&action=newtopdir", "fname=" + ans, function(txt) {
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
        case "download"://Download ZIP
            processAJAXPostQuery(_service("files") + "&action=download", "path=" + fpath, function(data) {
                    if(data.Data.uri!=null) {
                      window.open(data.uri);
                    } else {
                      if(typeof data.Data == "string") {
                        lgksToast(data.Data);
                      } else {
                        lgksToast("Error downloading file");
                      }
                    }
                },"json");
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
            lgksConfirm("You are about to delete folder '"+bname+"'. Are you sure?", "Delete File!", function(ans) {
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
            lgksPrompt("Please give a new name for the selected folder", "Rename Folder!", function(ans) {
                if (ans && ans.length>0) {
                    processAJAXPostQuery(_service("files") + "&action=rename", "path=" + bpath+"&newname="+ans, function(txt) {
                        if (txt != null && txt.length > 0) {
                            if (txt.indexOf("success") >= 0) {
                                loadFileTree(txt.substr(5));
                            } else {
                                lgksToast(txt);
                            }
                        } else {
                            lgksToast("Error deleting folder.<br>Please check if files are not readonly.");
                        }
                    });
                }
            });
            break;

        case "cut":
            oldFilePath = ["cut",bpath,"dir"];
            break;
        case "copy":
            oldFilePath = ["copy",bpath,"dir"];
            break;
        case "paste":
            if(oldFilePath!=null) {
              switch(oldFilePath[2]) {
                case "dir":
                  switch(oldFilePath[0]) {
                    case "copy":
                      processAJAXPostQuery(_service("files") + "&action=cpdir", "path=" + oldFilePath[1] +"&newpath="+bpath, function(txt) {
                            if (txt != null && txt.length > 0) {
                                lgksAlert(txt);
                            } else {
                                loadFileTree(txt.substr(5));
                                lgksToast("Copy Successfull");
                            }
                        });
                      break;
                    case "cut":
                      processAJAXPostQuery(_service("files") + "&action=mvdir", "path=" + oldFilePath[1] +"&newpath="+bpath, function(txt) {
                            if (txt != null && txt.length > 0) {
                                lgksAlert(txt);
                            } else {
                                loadFileTree(txt.substr(5));
                                lgksToast("Move Successfull");
                            }
                        });
                      break;
                  }
                  break;
                case "file":
                  switch(oldFilePath[0]) {
                    case "copy":
                      processAJAXPostQuery(_service("files") + "&action=cp", "path=" + oldFilePath[1] +"&newpath="+bpath, function(txt) {
                            if (txt != null && txt.length > 0) {
                                if (txt.indexOf("success") >= 0) {
                                    loadFileTree(txt.substr(5));
                                } else {
                                    lgksToast(txt);
                                }
                            } else {
                                lgksToast("Error coping file.<br>Please check if files are not readonly.");
                            }
                        });
                      break;
                    case "cut":
                      processAJAXPostQuery(_service("files") + "&action=mv", "path=" + oldFilePath[1] +"&newpath="+bpath, function(txt) {
                            if (txt != null && txt.length > 0) {
                                if (txt.indexOf("success") >= 0) {
                                    loadFileTree(txt.substr(5));
                                } else {
                                    lgksToast(txt);
                                }
                            } else {
                                lgksToast("Error move file.<br>Please check if files are not readonly.");
                            }
                        });
                      break;
                  }
                  break;
                default:
                  lgksToast("File type not supported");
              }
            } else lgksToast("Nothing copied to clipboard");
          break;
        case "generate":
          generateCodeAutomatically(bpath);
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
//     if (hiddenFolders.indexOf(basePath) >= 0) return;
    $.each(obj, function(k, v) {
        if ((typeof v) == "object") {
            newBasePath = basePath + k + "/";
//             if (hiddenFolders.indexOf(newBasePath) >= 0) return;
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

function collapseAll() {
  getFileTree().find("ul>li").removeClass("active").parent().find(">li").css("display", "none");
	getFileTree().find(">li").removeClass("active").css("display", "list-item");
}

function searchFile() {
  q = $("#searchField input").val();
  if(q==null || q.length<=0) {
    collapseAll();
    return;
  }
  q = q.toLowerCase();

  qHTML = [];
  getFileTree().find("li.file").each(function() {
    if($(this).attr("filepath").toLowerCase().indexOf(q)>0) {
      revealFile($(this).attr("filepath"));
    }
  });
}

function registerFileTreeListener(key, func) {
    fileTreeListener[key] = func;
}

function generateCodeAutomatically(basePath) {
  processAJAXQuery(_service("logiksGenerators","listforpath")+"&path="+basePath, function(data) {
      if(data == null || data.length<=0) {
        lgksToast("No generator defined for selected path, <br>or<br> logiksGenerator is not installed !!!");
      } else {
        data = JSON.parse(data);
        if(typeof data.Data == "string") {
          lgksToast(data.Data);
        } else {
          if(data.Data.generators==null || data.Data.generators.length<=0) {
            lgksToast("No generator found for the selected path");
          } else if(data.Data.generators.length==1) {
            lgksPrompt("Provide a new name", "Generator Generates", function(ans) {
                        if(ans && ans.length>0) {
                            processAJAXPostQuery(_service("logiksGenerators","generate"),"&path="+basePath+"&src="+data.Data.generators[0]+"&name="+ans, function(ansData) {
                                if(ansData.Data.status == "ok") {
                                  loadFileTree();
                                } else {
                                  if(ansData.Data.msg==null || ansData.Data.msg.length<=0) ansData.Data.msg = "Error generating source code"; 
                                  lgksToast(ansData.Data.msg);
                                }
                            }, "json");
                        }
                    });
          } else {
            lgksToast("Multiple generators are not supported yet");
          }
        }
      }
    });
}