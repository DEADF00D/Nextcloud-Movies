<?php

namespace OCA\Movies\TMDb;

class TMDb{
    public function HTTPGet($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
    public function Search($search){
        $out=$this->HTTPGet("https://www.themoviedb.org/search?query=".urlencode($search)."&language=en-US");

        #echo $out;

        $matches=[];
        preg_match_all('/class="result" href="\/movie\/([0-9]+).*?"/i', $out, $matches);
        #print_r($matches[1]);
        return $matches[1];
    }
    public function Movie($movieid){
        $out=$this->HTTPGet("https://www.themoviedb.org/movie/".intval($movieid)."?language=en-US");

        $result=[];
        $matches=[];

        preg_match('/<meta property="og:title" content="(.*?)">/i', $out, $matches);

        if(count($matches)>0){
            $result['title']=$matches[1];
        }

        preg_match('/<meta property="og:description" content="(.*?)">/i', $out, $matches);
        if(count($matches)>0){
            $result['description']=$matches[1];
        }

        preg_match('/<meta property="og:image" content="(.*?)">/i', $out, $matches);
        if(count($matches)>0){
            $result['art']=$matches[1];
        }

        preg_match('/<span class="release_date">\((.*?)\)<\/span>/i', $out, $matches);
        if(count($matches)>0){
            $result['releasedate']=$matches[1];
        }

        preg_match('/class="user_score_chart" data-percent="(.*?)"/i', $out, $matches);
        if(count($matches)>0){
            $result['userscore']=$matches[1];
        }

        preg_match('/<p><strong><bdi>Runtime<\/bdi><\/strong> (.*?)<\/p>/i', $out, $matches);
        if(count($matches)>0){
            $result['runtime']=$matches[1];
        }

        return $result;
    }
}
