<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AppHelper
{
	public function fullTextWildcards($term,$allowedwordlength=3)
    {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);
 
        $words = explode(' ', $term);
 
        foreach($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if(strlen($word) >= $allowedwordlength) {
                $words[$key] = '+' . $word . '*';
            }
        }
 
        $searchTerm = implode( ' ', $words);
 
        return $searchTerm;
	}
	
	public function getPageCurl($url,$headers=array(),$cookie='',$cookie_file_path='',$returnbeforeexec=0,$useragent='',$proxyip='',$proxyport='',$showheader=0,$followlocation=0)
	{
		
		$ch = curl_init();
		if($showheader)
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if($cookie!='')
		{
			curl_setopt($ch, CURLOPT_COOKIE, $cookie); 
		}
		elseif ($cookie_file_path!='')
		{
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
    		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
		}
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8'); 

		if($useragent!='')
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		else
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followlocation);

		//Set the proxy IP.
		if($proxyip!='')
    	curl_setopt($ch, CURLOPT_PROXY, $proxyip);
		//Set the port.
		if($proxyport!='')
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxyport);
		   

		if($returnbeforeexec)
		return $ch;

		$result=curl_exec($ch);
		curl_close($ch);

		return $result;

	}


	public function getCookiesFromCurl($ch)
	{

		$html=curl_exec($ch);

		$skip = intval(curl_getinfo($ch, CURLINFO_HEADER_SIZE)); 
		$requestHeader= substr($html,0,$skip);

		$html = substr($html,$skip);
		$e = 0;
		while(true){
			$s = stripos($requestHeader,'set-cookie: ',$e);
			if (!$s){break;}
			$s += 12;
			$e = strpos($requestHeader,';',$s);
			$cookie = substr($requestHeader,$s,$e-$s);
			$s = strpos($cookie,'=');
			$key = substr($cookie,0,$s);
			$value = substr($cookie,$s);
			$cookies[$key] = $value;
		}

		curl_close($ch);

		return $cookies;

	}

	public function saveCookiesToFile($cookies,$file)
	{

		$fp = fopen($file ,'w');
		fwrite($fp,serialize($cookies));
		fclose($fp);

	}

	public function getCookiesFromFile($file,$returncookiesarray=0)
	{

		$cookies= unserialize(file_get_contents($file));

		if($returncookiesarray)
		{
			foreach ($cookies as $k => $v){
				$cleaned_cookies[$k]=trim($v,"=");
			}
			return $cleaned_cookies;
		}

		$cookiestring = '';
			$show = '';
			$head = '';
			$delim = '';
			foreach ($cookies as $k => $v){

				$cookiestring .= "$delim$k$v";
			$delim = '; ';
			}

			return $cookiestring;

	}

	public function simplePost($url,$customrequesttype='POST',$postdata=array(),$headers=array(),$cookie='',$cookie_file_path='',$returnbeforeexec=0,$timeout=0,$showheader=0,$proxyip='',$proxyport='',$useragent='')
    {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if($timeout!=0)
		{
			curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		}


		if($useragent!='')
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		else
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0");

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customrequesttype);
		if($showheader)
		curl_setopt($ch, CURLOPT_HEADER, 1);
		if($customrequesttype='POST')
		curl_setopt($ch, CURLOPT_POST, 1);
		
		//Set the proxy IP.
		if($proxyip!='')
    	curl_setopt($ch, CURLOPT_PROXY, $proxyip);
		//Set the port.
		if($proxyport!='')
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxyport);

		if($postdata!='')
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);

		if($cookie!='')
		{
			curl_setopt($ch, CURLOPT_COOKIE, $cookie); 
		}
		elseif ($cookie_file_path!='')
		{
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
    		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
		}
		
		if($returnbeforeexec)
		return $ch;

		$result=curl_exec($ch);
		curl_close($ch);

		return $result;


	}

	public function jsonPostRequest($url, $postDataEncoded) {
        
      
        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_ENCODING,"gzip,deflate");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");   
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postDataEncoded);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',     
            'Accept: application/json',     
            'Content-Length: ' . strlen($postDataEncoded) 
        ));
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
        $result =curl_exec($ch);
        $curlError = curl_error($ch);
        
        if ($curlError != "") {
            $this->debout("Network error: $curlError");
            return false;
        }
        curl_close($ch);
        return json_decode($result);
    }
	public function getPageCaptchBalance($anti_captcha_userid)
    {
		$apiurlbalance='https://api.anti-captcha.com/getBalance';

		$createtaskarray=json_encode(
			array
			(
				"clientKey"=>$anti_captcha_userid,
			));


		$requestPage_captcha = $this->jsonPostRequest($apiurlbalance,$createtaskarray);
		

		return $requestPage_captcha;

	}

	public function getPageCaptcha($anti_captcha_userid, $url, $googlekey)
    {

					$apiurl_create='https://api.anti-captcha.com/createTask';
					$apiurl_getresult='https://api.anti-captcha.com/getTaskResult';

				$createtaskarray=json_encode(
				array
				(
					"clientKey"=>$anti_captcha_userid,
					"task"=>
						array(
							"type"=>"NoCaptchaTaskProxyless",
							"websiteURL"=>$url,
							"websiteKey"=>$googlekey
						),
					"softId"=>0,
					"languagePool"=>"en"
				));

				$requestPage_captcha = $this->jsonPostRequest($apiurl_create,$createtaskarray);

				
				$taskid=utf8_decode($requestPage_captcha->taskId);
				
				$count_secs=0;
				$process_status='true';
				while($process_status)
				{
					

				$gettaskresult_array=json_encode(
				array
				(
					"clientKey"=>$anti_captcha_userid,
					"taskId"=>$taskid,
				));

				$requestPage_captcha = $this->jsonPostRequest($apiurl_getresult,$gettaskresult_array);
			
				
				$status=utf8_decode($requestPage_captcha->status);
				
					if($status=='ready'){
						$process_status=false;
					break;
					}

					if($count_secs>100){
						$process_status=false;
					break;
					}
				echo $count_secs;
					sleep(1);
					$count_secs++;
				}

			return utf8_decode($requestPage_captcha->solution->gRecaptchaResponse);

	}



	


      public static function formatUrlsInTexthelper($html, $newwindow = false)
      {
        $uc = 'a-z\x{00a1}-\x{ffff}';
			$url_regex = '#\b((?:https?|ftp)://(?:[0-9'.$uc.'][0-9'.$uc.'-]*\.)+['.$uc.']{2,}(?::\d{2,5})?(?:/(?:[^\s<>]*[^\s<>\.])?)?)#iu';

			// get matches and their positions
			if (preg_match_all($url_regex, $html, $matches, PREG_OFFSET_CAPTURE)) {
				$brackets = array(
					')' => '(',
					'}' => '{',
					']' => '[',
				);

				// loop backwards so we substitute correctly
				for ($i = count($matches[1])-1; $i >= 0; $i--) {
					$match = $matches[1][$i];
					$text_url = $match[0];
					$removed = '';
					$lastch = substr($text_url, -1);

					// exclude bracket from link if no matching bracket
					while (array_key_exists($lastch, $brackets)) {
						$open_char = $brackets[$lastch];
						$num_open = substr_count($text_url, $open_char);
						$num_close = substr_count($text_url, $lastch);

						if ($num_close == $num_open + 1) {
							$text_url = substr($text_url, 0, -1);
							$removed = $lastch . $removed;
							$lastch = substr($text_url, -1);
						}
						else
							break;
					}

					$target = $newwindow ? ' target="_blank"' : '';
					$replace = '<a href="' . $text_url . '" rel="nofollow"' . $target . '>' . $text_url . '</a>' . $removed;
					$html = substr_replace($html, $replace, $match[1], strlen($match[0]));
				}
			}

			return $html;    
		  
    }

     public function formatUrlsInText($html)
     {
         $htmlunlinkeds = array_reverse(preg_split('|<[Aa]\s+[^>]+>.*</[Aa]\s*>|', $html, -1, PREG_SPLIT_OFFSET_CAPTURE)); // start from end so we substitute correctly
							foreach ($htmlunlinkeds as $htmlunlinked)
							{ // and that we don't detect links inside HTML, e.g. <img src="http://...">
								$thishtmluntaggeds = array_reverse(preg_split('/<[^>]*>/', $htmlunlinked[0], -1, PREG_SPLIT_OFFSET_CAPTURE)); // again, start from end
								foreach ($thishtmluntaggeds as $thishtmluntagged)
								{
									$innerhtml = $thishtmluntagged[0];
									if(is_numeric(strpos($innerhtml, '://'))) 
									{ // quick test first
										$newhtml = $this->formatUrlsInTexthelper($innerhtml, true);
										$html = substr_replace($html, $newhtml, $htmlunlinked[1]+$thishtmluntagged[1], strlen($innerhtml));
									}
								}
							}  
								
		return $html;					
     }

	public function RandomString($length) {
			$key = '';
			$keys = array_merge(range(0, 9), range('a', 'z'));

			for ($i = 0; $i < $length; $i++) {
				$key .= $keys[array_rand($keys)];
			}

			return $key;
	}
	
	public function humanTiming ($time)
	{

		$time = time() - $time; // to get the time since that moment
		$time = ($time<1)? 1 : $time;
		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}

	}
	
	public function SerializeAdminRequestSearch($request)
	{
		
	if ($request->input('quotation_mark') == 'no')
		$quotation_mark = 'no';
		else
		$quotation_mark = 'yes';
		
		if ($request->input('fromdate'))
		$fromdate = urldecode($request->input('fromdate'));
		else
		$fromdate = '2005-01-12 00:00:00';
		
		
		$currentdate=date("Y-m-d H:i:s");
		
		if ($request->input('todate'))
		$todate = urldecode($request->input('todate'));
		else
		$todate = $currentdate;
		
		
		if ($request->input('perpage'))
		$perpage = $request->input('perpage');
		else
		$perpage = '50';
		
		if ($request->input('pagenum'))
		$i2 = ($request->input('pagenum')-1)*$perpage;
		else
		$i2 = '0';
	
		if ($request->input('monetize')!='')
		$monetize = $request->input('monetize'); //all, yes, no
		else
		$monetize = 'all';
		
		if($request->input('orderby')!='')
		$orderby = $request->input('orderby');  //viewcount, added, biggest growth(%), viewcount+biggest growth
		else
		$orderby = 'added';
		
		if($request->input('ch')!='')
		$ch = $request->input('ch');
		else
		$ch = 'all';
		
		if($request->input('genre')!='')
		$genre = $request->input('genre');
		else
		$genre = 'all';
		
		if($request->input('title')!='')
		{
			$decodedtitle= urldecode($request->input('title'));
			
			
			$titledecoded = addslashes($decodedtitle);
			
		
						$content = substr(strip_tags($titledecoded), 0, 255);	
						$query_words = str_replace("+", " ", $content);		
						$query_words = str_replace(",", " ", $query_words);
						$query_words = str_replace("       ", " ", $query_words);
						$query_words = str_replace("      ", " ", $query_words);
						$query_words = str_replace("     ", " ", $query_words);
						$query_words = str_replace("    ", " ", $query_words);
						$query_words = str_replace("   ", " ", $query_words);
						$query_words = str_replace("  ", " ", $query_words);
						$query_words = str_replace(" ", " ", $query_words);
					
						$array = $query_words;
						$array = explode(' ', $array );
						foreach ($array as &$value){
							$value = $value.'*';
						}
						$query_words1=implode(' ', $array);
						
			/*if ($quotation_mark == 'yes')
			{
				*/
				$query_words1 = "'".$query_words1."'";
				$query_words = "'".$query_words."'";
			//}
			
						
						
						//$titlesearch1=", MATCH (t1.title) AGAINST ('".$query_words1."' '".$query_words."' IN BOOLEAN MODE) score";
						$titlesearch2=" HAVING score>0";
						
						//$titlesearch3=" AND MATCH (t1.title) AGAINST ('".$query_words1."' '".$query_words."' IN BOOLEAN MODE) > '0'";
		
		}
	
		if($request->input('youtubeid')!='')
		{
		$youtubeid = urldecode($request->input('youtubeid'));
		
		$youtubeidpreserved = $youtubeid;
		
		preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $youtubeid, $youtubeidmatches);
		if(isset($youtubeidmatches[2]) && $youtubeidmatches[2] != ''){
		
			$youtubeid = $youtubeidmatches[2];
											
													}
		
		}
		
		
		if($orderby =="viewcount")
		$realorderby = "t1.views";
		else
		$realorderby = "t1.date_added";
		
		
		if($monetize == "yes")
		$realmonetize = " AND t1.monetize='1'";
		elseif($monetize == "no")
		$realmonetize = " AND t1.monetize='0'";
		else
		$realmonetize="";
		
		if($genre!="all")
		$realgenre = " AND t1.genre='".$genre."'";
		else
		$realgenre = "";
		
		if($ch!="all")
		{
			$ch = str_replace('+',' ',$ch);
		$realch = " AND t1.channel='".$ch."'";
		}
		else
		$realch = "";
		
		if(($fromdate!="2005-01-12 00:00:00") || ($todate!=$currentdate))
		{
			$realfromtodate=" AND date_added>='".$fromdate."' AND date_added<='".$todate."'";
		}
		else
		{
			$realfromtodate="";
		}
		
		
		$anysearch = urldecode($request->input('anysearch'));
		if ($anysearch!='')
					{
			$anysearchpreserved = $anysearch;
		preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $anysearch, $anysearchmatches);
		if(isset($anysearchmatches[2]) && $anysearchmatches[2] != ''){
		
			$anysearch = $anysearchmatches[2];
											
													}
					}
		
		
		return array('fromdate'=>$fromdate,'currentdate'=>$currentdate,'todate'=>$todate,'perpage'=>$perpage,'i2'=>$i2 ,'monetize'=>$monetize,'orderby'=>$orderby,'ch'=>$ch,'genre'=>$genre,'quotation_mark'=>$quotation_mark,'query_words1'=>$query_words1,'query_words'=>$query_words,'titlesearch2'=>$titlesearch2,'youtubeid'=>$youtubeid,'realorderby'=>$realorderby,'realmonetize'=>$realmonetize,'realgenre'=>$realgenre,'realch'=>$realch, 'realfromtodate'=>$realfromtodate,'anysearch'=>$anysearch,'titledecoded'=>$titledecoded);
		
     }
	
	public function CreateAdminPagination($request,$item_count,$perpage,$pagenumcount=7)
	{

		
		$halfofpages=floor($pagenumcount/2);

		$pages = ceil($item_count/$perpage);
		$currentpagenum = $request->input('pagenum');
		$pagination = '';
		if($pages<'1')
		$pagination = '';
		elseif($pages=='1')
		$pagination = '1';
		elseif(($pages>'1') && ($currentpagenum>'1') && ($currentpagenum<$pages))
		$pagination = '1'; //previous+next
		elseif(($pages>'1') && ($currentpagenum>'1') && ($currentpagenum==$pages))
		$pagination = '1'; //previous
		elseif(($pages>'1') && ($currentpagenum=='1'))
		$pagination = '1'; //next
		
		
		//$getrequest = str_replace('/check/','',$_SERVER['REQUEST_URI']);
	
	    $getrequest=str_replace($request->url(), '',$request->fullUrl());
				
		//$getrequest = $adminpage;
		
		if ($getrequest=='')
		$getrequest='?pagenum=1';

		if($request->input('pagenum')=='')
		$currentpagenum='1';
		else
		$currentpagenum = min($pages, $request->input('pagenum'));

		
		$start = ($currentpagenum-1)*$perpage;
		$end = min(($currentpagenum * $perpage), $item_count);
		
					if($pages>=$pagenumcount && $currentpagenum>($pagenumcount-$halfofpages))
				{
					if($currentpagenum>($pages-$halfofpages))
					{
					$startiteration = $pages-($pagenumcount-1);
					$enditeration = $pages;
					}
					else
					{
					$startiteration = $currentpagenum-$halfofpages;
					$enditeration = min(($currentpagenum+$halfofpages),$pages);
					}
				}
				else
				{
				$startiteration = 1;
				$enditeration = min($pages,$pagenumcount);
				}

				for ($i=$startiteration; $i<=$enditeration; $i++) {
					if($currentpagenum==$i)
					{
	
				//$buttoniteration.='<a class="itempagingactive" href="'.str_replace('pagenum='.$currentpagenum,'pagenum='.$i,$getrequest).'">'.$i.' </a>';
				$buttoniteration.='<li class="page-item  active"><a class="page-link" href="'.str_replace('pagenum='.$currentpagenum,'pagenum='.$i,$getrequest).'">'.$i.' </a></li>';
					}
					else
					{
						if (preg_match('/pagenum='.$currentpagenum.'/',$getrequest))
						{
							
				//$buttoniteration.='<a href="'.str_replace('pagenum='.$currentpagenum,'pagenum='.$i,$getrequest).'">'.$i.' </a>';
				$buttoniteration.='<li class="page-item"><a class="page-link" href="'.str_replace('pagenum='.$currentpagenum,'pagenum='.$i,$getrequest).'">'.$i.' </a></li>';
						}
						else
						{
				//$buttoniteration.='<a href="'.$getrequest.'&pagenum='.$i.'">'.$i.' </a>';
				$buttoniteration.='<li class="page-item"><a class="page-link" href="'.$getrequest.'&pagenum='.$i.'">'.$i.' </a></li>';
						}
					}

				} 

						if (preg_match('/pagenum='.$currentpagenum.'/',$getrequest))
						{
							
						// The "back" link
				//$prevlink = ($currentpagenum > 1) ? '<a href="'
				//.str_replace('pagenum='.$currentpagenum,'pagenum=1',$getrequest).
				//'" title="First page">&laquo;</a> <a href="' . 
				//str_replace('pagenum='.$currentpagenum,'pagenum='.($currentpagenum - 1),$getrequest) 
				//. '" title="Previous page">&lsaquo; Previous</a>' : '';
				$prevlink = ($currentpagenum > 1) ? '<li class="page-item"><a class="page-link" href="'
				.str_replace('pagenum='.$currentpagenum,'pagenum=1',$getrequest).
				'" title="First page">&laquo;</a></li> <li class="page-item"><a class="page-link" href="' . 
				str_replace('pagenum='.$currentpagenum,'pagenum='.($currentpagenum - 1),$getrequest) 
				. '" title="Previous page">&lsaquo; Previous</a></li>' : '';

				
				// The "forward" link
				//$nextlink = ($currentpagenum < $pages) ? '<a href="'
				// . str_replace('pagenum='.$currentpagenum,'pagenum='.($currentpagenum + 1),$getrequest) 
				// . '" title="Next page">Next &rsaquo;</a> <a href="' . str_replace('pagenum='
				// .$currentpagenum,'pagenum='.($pages),$getrequest) . '" title="Last page">&raquo;</a>' : '';
				$nextlink = ($currentpagenum < $pages) ? '<li class="page-item"><a class="page-link" href="'
				 . str_replace('pagenum='.$currentpagenum,'pagenum='.($currentpagenum + 1),$getrequest) 
				 . '" title="Next page">Next &rsaquo;</a></li> <li class="page-item"><a class="page-link" href="' . str_replace('pagenum='
				 .$currentpagenum,'pagenum='.($pages),$getrequest) . '" title="Last page">&raquo;</a></li>' : '';

				// Display the paging information
						}
						else //if first page
						{
							
				// The "forward" link
				//$nextlink = ($currentpagenum < $pages) ? '<a href="' 
				//. $getrequest.'&pagenum='.($currentpagenum + 1) 
				//. '" title="Next page">Next &rsaquo;</a> <a href="' 
				//. $getrequest.'&pagenum='.($pages) . '" title="Last page">&raquo;</a>' : '';
				$nextlink = ($currentpagenum < $pages) ? '<li class="page-item"><a class="page-link" href="' 
				. $getrequest.'&pagenum='.($currentpagenum + 1) 
				. '" title="Next page">Next &rsaquo;</a></li> <li class="page-item"><a class="page-link" href="' 
				. $getrequest.'&pagenum='.($pages) . '" title="Last page">&raquo;</a></li>' : '';
				// Display the paging information
						}
						
				//$pagination = '<div class="itempaging">'.$prevlink.$buttoniteration.$nextlink.'</div>';
				$pagination = '<ul class="pagination mt-5">'.$prevlink.$buttoniteration.$nextlink.'</ul>';
				$paginationdisplay = 'Page '.$currentpagenum.' of '.$pages.' pages, displaying '.$start.'-'.$end.' of '.number_format($item_count).' results';
		
		return array('pagination'=>$pagination,'paginationdisplay'=>$paginationdisplay);
		
	}
	
	
	public function check_into_database($url)
    {
		
		
		$video_count = DB::table('videos')
			->select(\DB::raw('COUNT(*) AS item_count'))
			->where('url', 'LIKE', $url.'%')
			->get();
						
						
			$row['found_count']=$video_count[0]->item_count;
		
					if($row['found_count']>'0')
					{
					return true;
					}
				
		
		return false;
	}
	
	
	public function checkurluser($url)
	{
		
		$video_count = DB::table('users')
			->select(\DB::raw('COUNT(*) AS item_count'))
			->where('username', '=', $url)
			->get();
						
						
			$row['found_count']=$video_count[0]->item_count;
		
					if($row['found_count']>'0')
					{
					return true;
					}
				
		
		return false;
		
	}
	
	
	public function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("and", "to", "of", "das", "dos", "I", "II", "III", "IV", "V", "VI"))
	{
		/*
		 * Exceptions in lower case are words you don't want converted
		 * Exceptions all in upper case are any words you don't want converted to title case
		 *   but should be converted to upper case, e.g.:
		 *   king henry viii or king henry Viii should be King Henry VIII
		 */
		$string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
		foreach ($delimiters as $dlnr => $delimiter) {
			$words = explode($delimiter, $string);
			$newwords = array();
			foreach ($words as $wordnr => $word) {
				if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
					// check exceptions list for any words that should be in upper case
					$word = mb_strtoupper($word, "UTF-8");
				} elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
					// check exceptions list for any words that should be in upper case
					$word = mb_strtolower($word, "UTF-8");
				} elseif (!in_array($word, $exceptions)) {
					// convert to uppercase (non-utf8 only)
					$word = ucfirst($word);
				}
				array_push($newwords, $word);
			}
			$string = join($delimiter, $newwords);
	   }//foreach
	   return $string;
	}

	public function remove_utf8_bom($text)
	{
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $text);
		return $text;
	}

	public function url_test( $url ) {
	  $timeout = 10;
	  $ch = curl_init();
	  curl_setopt ( $ch, CURLOPT_URL, $url );
	  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	  curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
	  $http_respond = curl_exec($ch);
	  $http_respond = trim( strip_tags( $http_respond ) );
	  $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	  if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
		return true;
	  } else {
		// return $http_code;, possible too
		return false;
	  }
	  curl_close( $ch );
	}

	public function numberFormat($number, $decimals = 0, $decPoint = '.' , $thousandsSep = ',')
	{
		$negation = ($number < 0) ? (-1) : 1;
		$coefficient = pow(10, $decimals);
		$number = $negation * floor((string)(abs($number) * $coefficient)) / $coefficient;
		return number_format($number, $decimals, $decPoint, $thousandsSep);
	}
	
	public function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
	}

	public function curl_httpstatus($url)
	{
			$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko Firefox/11.0');
		curl_setopt($ch, CURLOPT_REFERER, 'https://www.youtube.com');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$str = curl_exec($ch);
			$int = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			return intval($int);
	}
	
	public function parse_yturl($url)
	{
			$pattern = '#^(?:https?://)?';    # Optional URL scheme. Either http or https.
			$pattern .= '(?:www\.)?';         #  Optional www subdomain.
			$pattern .= '(?:';                #  Group host alternatives:
			$pattern .=   'youtu\.be/';       #    Either youtu.be,
			$pattern .=   '|youtube\.com';    #    or youtube.com
			$pattern .=   '(?:';              #    Group path alternatives:
			$pattern .=     '/embed/';        #      Either /embed/,
			$pattern .=     '|/v/';           #      or /v/,
			$pattern .=     '|/watch\?v=';    #      or /watch?v=,
			$pattern .=     '|/watch\?.+&v='; #      or /watch?other_param&v=
			$pattern .=   ')';                #    End path alternatives.
			$pattern .= ')';                  #  End host alternatives.
			$pattern .= '([\w-]{11})';        # 11 characters (Length of Youtube video ids).
			$pattern .= '(?:.+)?$#x';         # Optional other ending URL parameters.
			preg_match($pattern, $url, $matches);
			return (isset($matches[1])) ? $matches[1] : FALSE;
	}
	

	public function searchInBetween($firststring,$secondstring,$stringtosearchin,$simplereturn=1)
	{
	preg_match_all('/'.$firststring.'(.*?)'.$secondstring.'/i', $stringtosearchin, $matches, PREG_PATTERN_ORDER);

	if($simplereturn=='1')
	return $matches[1][0];
	else
	return $matches;
	}


	public function formatTime($duration,$ismilliseconds=false) //as hh:mm:ss
	{
		if($ismilliseconds)
		$duration=floor($duration/3600);
		//return sprintf("%d:%02d", $duration/60, $duration%60);
		$hours = floor($duration / 3600);
		$minutes = floor( ($duration - ($hours * 3600)) / 60);
		$seconds = $duration - ($hours * 3600) - ($minutes * 60);
		if($hours>0)
		return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
		else
		return sprintf("%02d:%02d", $minutes, $seconds);
	}

	public function generatePasswd($numAlpha=6,$numNonAlpha=2)
	{
	$listAlpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$listNonAlpha = ',;:!?.$/*-+&@_+;./*&?$-!,';
	return str_shuffle(
		substr(str_shuffle($listAlpha),0,$numAlpha) .
		substr(str_shuffle($listNonAlpha),0,$numNonAlpha)
		);
	}

     public static function instance()
     {
         return new AppHelper();
     }
}