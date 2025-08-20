<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$githubRepos = [
        "logiks"=>"Logiks/Logiks-Core"
    ];

define("GITHUB_KEY","TG9naWtzOmZhZGYxOTIzZmU5ODkwMGQ1ZGRmOWYyODg0ZDk3NDMwYjY0ZmM3MjA=");

switch($_REQUEST["action"]) {
    case "ajaxsearch":
        printServiceMsg([
                // ["title"=>"Bismay","value"=>"123"],
                // ["title"=>"Mita","value"=>"124"],
                // ["title"=>"Susmay","value"=>"125"],
            ]);
        break;
    case "search":
        if(!isset($_POST['filters'])) $_POST['filters']="github";
        if(!isset($_POST['projectType'])) $_POST['projectType']="logiks";
        if(!isset($_POST['lang'])) $_POST['lang']=false;
        if(!isset($_POST['path'])) $_POST['path']=false;
        
        if(!isset($_POST['term']) || !isset($githubRepos[$_POST['projectType']])) {
            printServiceMsg([]);
        } else {
            $q=$_POST['term'];
            $ans=[];
            switch(strtolower($_POST['filters'])) {
                case "github":
                    $ans=searchGithub($q, $_POST['lang'], $githubRepos[$_POST['projectType']]);
                    break;
                case "local":case "appsource":
                    $ans=searchLocal($q, $_POST['lang'], $_POST['path']);
                    break;  
            }
            if(isset($ans['error'])) {
                if(is_array($ans['error'])) {
                    
                }
                printServiceMsg(["filter"=>$_POST['filters'],"lang"=>$_POST['lang'],"term"=>$q,"max"=>0,"error"=>$ans['error']]);
            } else {
                printServiceMsg(["filter"=>$_POST['filters'],"lang"=>$_POST['lang'],"term"=>$q,"max"=>count($ans['results']),"results"=>$ans['results']]);
            }
        }
        break;
}
function searchGithub($q,$lang=false,$repo="Logiks/Logiks-Core") {
    $url="https://api.github.com/search/code?q=";
    $url.=urlencode('"'.$q.'"');
    if($repo) {
        $repos=explode(",",$repo);
        foreach($repos as $r) {
            $url.="+repo:{$r}";
        }
    }
    if($lang && $lang!="*") {
        $langs=explode(",",$lang);
        foreach($langs as $l) {
            $url.="+language:{$l}";
        }
    }
    $url.="+user:LogiksPlugins+user:LogiksApps+user:Logiks";
    $url.="&sort=stars&order=desc";
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Accept: application/vnd.github.v3.text-match+json",
        "Authorization: Basic ".GITHUB_KEY,
        "Cache-Control: no-cache",
        "User-Agent: Awesome-Octocat-App"
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      return ["error"=>$err];
    } else {
      try {
        $dataArr=json_decode($response,true);
        //printArray($dataArr['items']);
        
        $results=[];
        if(isset($dataArr['items'])) {
            foreach($dataArr['items'] as $item) {
                $ext=explode(".",$item['name']);
                $ext=end($ext);
                $ans=array_merge(getDefaultItem(),[
                                "title"=>$item['name'],
                                "extension"=>$ext,
                                "path"=>$item['path'],
                                "link"=>$item['html_url'],
                                "repo"=>$item['repository']['full_name'],
                                "repo_url"=>$item['repository']['html_url'],
                            ]);
                
                if(isset($item['text_matches']) && isset($item['text_matches'][0]) && isset($item['text_matches'][0]['fragment'])) {
                    $ans["codesnap"]=$item['text_matches'][0]['fragment'];
                }
                if(isset($item['text_matches']) && isset($item['text_matches'][0]) && isset($item['text_matches'][0]['matches'])) {
                    $ans["codetext"]=$item['text_matches'][0]['matches'][0]['text'];
                    if(isset($item['text_matches'][0]['matches'][0]['indices'])) {
                        $ans["lineno"]=$item['text_matches'][0]['matches'][0]['indices'];
                    }
                }
                
                $results[]=$ans;
            }
        }
        
        return ["results"=>$results,"maxcount"=>0];
      } catch(Exception $e) {
        return ["error"=>$e];
      }
    }
}
function searchLocal($q,$lang=false,$relativePath=false) {
    $path=APPROOT;
    if(defined("CMS_APPROOT")) {
        $path=CMS_APPROOT;
    }
    if($relativePath && strlen($relativePath)>1) {
        $path .= "/{$relativePath}";
        $path = str_replace("//", "/", $path);
    }
    $appName = basename($path);
    
    if($lang && $lang!="*") {
        $langs=explode(",",$lang);
    } else {
        $langs=false;
    }
    
    $results=searchDirectory($q,$path,$langs);
    
    $finalResults=[];
    foreach($results['files'] as $f) {
        $temp=array_merge(getDefaultItem(),$f);
        $temp['path']=str_replace($path,"/",$temp['path']);
        $temp['link']="modules/cmsEditor@&type=edit&src=" .urlencode($temp['path']);///plugins/modules/credsRoles/style.css
        $temp['repo']=$appName;
        $temp['repo_url']='#';
        $finalResults[]=$temp;
    }
    
    return ["results"=>$finalResults,"maxcount"=>$results['totalFiles']];
}
function searchDirectory($term, $path, $extension=["php","js","css","htm","html","tpl","json","cfg","md"]) {
    if($extension===false) {
        //Automatic All Extension Detection
        $extension=["php","js","css","htm","html","tpl","json","cfg","md"];
    }
    $dir = new DirectoryIterator($path);
    $files = array();

    $noSearch = ["vendors", "node_modules", "SQL", ".install"];

    $totalFiles = 0;
    foreach ($dir as $file) {
        if (!$file->isDot()) {
            if(in_array($file->getBasename(),$noSearch) || substr($file->getBasename(),0,1)==".") {
                continue;
            }
            if($file->isDir()) {
                $dirScan = searchDirectory($term,$file->getPathname(),$extension);
                $files = array_merge($files,$dirScan['files']);
                $totalFiles+=$dirScan['totalFiles'];
            } else {
                $totalFiles++;
                $fname=$file->getBasename();
                $pathName=$file->getPathname();
                $ext=$file->getExtension();
                
                if(in_array($ext,$extension)) {
                    $content = file_get_contents($file->getPathname());
                    $offset = 0;
                    
                    $lineNo  = true;
                    
                    while($lineNo) {
                        $lineNo = searchCode($content, $term, $offset);
                        
                        if ($lineNo !== false) {
                            $offset= $lineNo+1;
                            // $pathHash = md5($pathName);
                            $files[] = [
                                "title"=>$file->getBasename(),
                                "extension"=>$ext,
                                "path"=>$pathName,
                                "modified"=>date("d/m/Y",$file->getMTime()),
                                "index"=>$lineNo,
                                "codesnap"=>[extractCodeFragment($content,$lineNo)],
                                "codetext"=>$term,
                                ];
                        }
                    }
                    $content="";
                }
            }
        }
    }

    return array('files' => $files, 'totalFiles' => $totalFiles);
}
function searchCode($content, $term, $offset = 0) {
    return stripos($content, $term, $offset);
}
function extractCodeFragment($codeText,$index) {
    $fragLen=80;
    $flen=strlen($codeText);
    $start=(($index-$fragLen)<0)?0:($index-$fragLen);
    $end=(($index+$fragLen)>$flen)?$flen:($index+$fragLen);
    
    $text="$start $end";
    
    return substr($codeText,$start,($end-$start));
}
function getDefaultItem($name="",$filePath="",$urlPath="") {
    return [
            "title"=>$name,
            "extension"=>"",
            "path"=>$filePath,
            "link"=>$urlPath,
            "repo"=>"",
            "repo_url"=>"",
            "codesnap"=>"",
            "codetext"=>"",
            "modified"=>"",
            "lineno"=>[0],
        ];
}
?>