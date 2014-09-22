<?php

class GithubSearchResultsCrowler
{
    function __construct()
    {
        $language = 'javascript';
        $location = 'moscow';
        $emails = array();
        
        $search_url = "https://github.com/search?q=language%3A".$language."+location%3A".$location."&type=Users&ref=advsearch&l=".$language;
        $profile_url = "https://github.com"; 
        
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        
	$file = fopen( 'github-'.$language.'-'.$location.'.txt', "a+" );

	$page = 1;

        while(true)
        {
		echo $search_url.'&p='.$page.chr(13).chr(10);

            curl_setopt($this->curl, CURLOPT_URL, $search_url.'&p='.$page);
            
            $search_page = curl_exec($this->curl);
        
	    if ( strpos($search_page, 'Page not found') !== false ) break;
    
            $users = array();
            
            preg_match_all('/\<a\s+href="([^"]+)"\>\<img\s+class=\"gravatar\"/', $search_page, $users);

		if ( count($users[1]) < 1 ) break;

            foreach ( $users[1] as $user )
            {
		echo $profile_url.$user.chr(13).chr(10);

                curl_setopt($this->curl, CURLOPT_URL, $profile_url.$user);
                
                $profile_page = curl_exec($this->curl);
                
                $matches = array();
                
                if ( preg_match('/data-email="([^"]+)"/', $profile_page, $matches) )
                {
			fwrite($file, mb_convert_encoding(urldecode($matches[1]), "cp1251", "utf-8").chr(13).chr(10));
                }
            }

	    flush();

	    $page++;
        }

	fclose($file);
    }
}

$go = new GithubSearchResultsCrowler();
