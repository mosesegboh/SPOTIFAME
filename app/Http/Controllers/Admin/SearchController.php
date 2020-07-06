<?php

namespace App\Http\Controllers\Admin;

use \Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;

use App\Helpers\SpotifyHelper;

use Carbon\Carbon;

use GuzzleHttp\Client;


class SearchController extends Controller
{
	private $settings;
	
	private $markets = array();

	private $market;

	private $searchtypes=array();

	private $searchresults=array();

	private $spotifyapi;

	private $responsestatus=200;

	private $searchtype_pl;

	private $range_array_all;

	private $yearfromstartyear;

	private $yeartostartyear;

	private $rangeslidervalues=array();

	private $fromfollowers;

	private $tofollowers;

	private $inprogress=0;
	
	private $user_bearer_token;

	private $hideclaimed;

	private $hidespotifyowned;

	private $iscache;

    public function __construct()
    {
		$this->middleware('auth');

		$this->markets = array(
			'AF' => 'AFGHANISTAN',
			'AL' => 'ALBANIA',
			'DZ' => 'ALGERIA',
			'AS' => 'AMERICAN SAMOA',
			'AD' => 'ANDORRA',
			'AO' => 'ANGOLA',
			'AI' => 'ANGUILLA',
			'AQ' => 'ANTARCTICA',
			'AG' => 'ANTIGUA AND BARBUDA',
			'AR' => 'ARGENTINA',
			'AM' => 'ARMENIA',
			'AW' => 'ARUBA',
			'AU' => 'AUSTRALIA',
			'AT' => 'AUSTRIA',
			'AZ' => 'AZERBAIJAN',
			'BS' => 'BAHAMAS',
			'BH' => 'BAHRAIN',
			'BD' => 'BANGLADESH',
			'BB' => 'BARBADOS',
			'BY' => 'BELARUS',
			'BE' => 'BELGIUM',
			'BZ' => 'BELIZE',
			'BJ' => 'BENIN',
			'BM' => 'BERMUDA',
			'BT' => 'BHUTAN',
			'BO' => 'BOLIVIA, PLURINATIONAL STATE OF',
			'BQ' => 'BONAIRE, SINT EUSTATIUS AND SABA',
			'BA' => 'BOSNIA AND HERZEGOVINA',
			'BW' => 'BOTSWANA',
			'BV' => 'BOUVET ISLAND',
			'BR' => 'BRAZIL',
			'IO' => 'BRITISH INDIAN OCEAN TERRITORY',
			'BN' => 'BRUNEI DARUSSALAM',
			'BG' => 'BULGARIA',
			'BF' => 'BURKINA FASO',
			'BI' => 'BURUNDI',
			'KH' => 'CAMBODIA',
			'CM' => 'CAMEROON',
			'CA' => 'CANADA',
			'CV' => 'CAPE VERDE',
			'KY' => 'CAYMAN ISLANDS',
			'CF' => 'CENTRAL AFRICAN REPUBLIC',
			'TD' => 'CHAD',
			'CL' => 'CHILE',
			'CN' => 'CHINA',
			'CX' => 'CHRISTMAS ISLAND',
			'CC' => 'COCOS (KEELING) ISLANDS',
			'CO' => 'COLOMBIA',
			'KM' => 'COMOROS',
			'CG' => 'CONGO',
			'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
			'CK' => 'COOK ISLANDS',
			'CR' => 'COSTA RICA',
			'CI' => 'COTE DIVOIRE',
			'HR' => 'CROATIA',
			'CU' => 'CUBA',
			'CW' => 'CURACAO',
			'CY' => 'CYPRUS',
			'CZ' => 'CZECH REPUBLIC',
			'DK' => 'DENMARK',
			'DJ' => 'DJIBOUTI',
			'DM' => 'DOMINICA',
			'DO' => 'DOMINICAN REPUBLIC',
			'EC' => 'ECUADOR',
			'EG' => 'EGYPT',
			'SV' => 'EL SALVADOR',
			'GQ' => 'EQUATORIAL GUINEA',
			'ER' => 'ERITREA',
			'EE' => 'ESTONIA',
			'ET' => 'ETHIOPIA',
			'FK' => 'FALKLAND ISLANDS (MALVINAS)',
			'FO' => 'FAROE ISLANDS',
			'FJ' => 'FIJI',
			'FI' => 'FINLAND',
			'FR' => 'FRANCE',
			'GF' => 'FRENCH GUIANA',
			'PF' => 'FRENCH POLYNESIA',
			'TF' => 'FRENCH SOUTHERN TERRITORIES',
			'GA' => 'GABON',
			'GM' => 'GAMBIA',
			'GE' => 'GEORGIA',
			'DE' => 'GERMANY',
			'GH' => 'GHANA',
			'GI' => 'GIBRALTAR',
			'GR' => 'GREECE',
			'GL' => 'GREENLAND',
			'GD' => 'GRENADA',
			'GP' => 'GUADELOUPE',
			'GU' => 'GUAM',
			'GT' => 'GUATEMALA',
			'GG' => 'GUERNSEY',
			'GN' => 'GUINEA',
			'GW' => 'GUINEA-BISSAU',
			'GY' => 'GUYANA',
			'HT' => 'HAITI',
			'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS',
			'VA' => 'HOLY SEE (VATICAN CITY STATE)',
			'HN' => 'HONDURAS',
			'HK' => 'HONG KONG',
			'HU' => 'HUNGARY',
			'IS' => 'ICELAND',
			'IN' => 'INDIA',
			'ID' => 'INDONESIA',
			'IR' => 'IRAN, ISLAMIC REPUBLIC OF',
			'IQ' => 'IRAQ',
			'IE' => 'IRELAND',
			'IM' => 'ISLE OF MAN',
			'IL' => 'ISRAEL',
			'IT' => 'ITALY',
			'JM' => 'JAMAICA',
			'JP' => 'JAPAN',
			'JE' => 'JERSEY',
			'JO' => 'JORDAN',
			'KZ' => 'KAZAKHSTAN',
			'KE' => 'KENYA',
			'KI' => 'KIRIBATI',
			'KP' => 'KOREA, DEMOCRATIC PEOPLES REPUBLIC OF',
			'KR' => 'KOREA, REPUBLIC OF',
			'KW' => 'KUWAIT',
			'KG' => 'KYRGYZSTAN',
			'LA' => 'LAO PEOPLES DEMOCRATIC REPUBLIC',
			'LV' => 'LATVIA',
			'LB' => 'LEBANON',
			'LS' => 'LESOTHO',
			'LR' => 'LIBERIA',
			'LY' => 'LIBYA',
			'LI' => 'LIECHTENSTEIN',
			'LT' => 'LITHUANIA',
			'LU' => 'LUXEMBOURG',
			'MO' => 'MACAO',
			'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
			'MG' => 'MADAGASCAR',
			'MW' => 'MALAWI',
			'MY' => 'MALAYSIA',
			'MV' => 'MALDIVES',
			'ML' => 'MALI',
			'MT' => 'MALTA',
			'MH' => 'MARSHALL ISLANDS',
			'MQ' => 'MARTINIQUE',
			'MR' => 'MAURITANIA',
			'MU' => 'MAURITIUS',
			'YT' => 'MAYOTTE',
			'MX' => 'MEXICO',
			'FM' => 'MICRONESIA, FEDERATED STATES OF',
			'MD' => 'MOLDOVA, REPUBLIC OF',
			'MC' => 'MONACO',
			'MN' => 'MONGOLIA',
			'ME' => 'MONTENEGRO',
			'MS' => 'MONTSERRAT',
			'MA' => 'MOROCCO',
			'MZ' => 'MOZAMBIQUE',
			'MM' => 'MYANMAR',
			'NA' => 'NAMIBIA',
			'NR' => 'NAURU',
			'NP' => 'NEPAL',
			'NL' => 'NETHERLANDS',
			'NC' => 'NEW CALEDONIA',
			'NZ' => 'NEW ZEALAND',
			'NI' => 'NICARAGUA',
			'NE' => 'NIGER',
			'NG' => 'NIGERIA',
			'NU' => 'NIUE',
			'NF' => 'NORFOLK ISLAND',
			'MP' => 'NORTHERN MARIANA ISLANDS',
			'NO' => 'NORWAY',
			'OM' => 'OMAN',
			'PK' => 'PAKISTAN',
			'PW' => 'PALAU',
			'PS' => 'PALESTINE, STATE OF',
			'PA' => 'PANAMA',
			'PG' => 'PAPUA NEW GUINEA',
			'PY' => 'PARAGUAY',
			'PE' => 'PERU',
			'PH' => 'PHILIPPINES',
			'PN' => 'PITCAIRN',
			'PL' => 'POLAND',
			'PT' => 'PORTUGAL',
			'PR' => 'PUERTO RICO',
			'QA' => 'QATAR',
			'RE' => 'REUNION',
			'RO' => 'ROMANIA',
			'RU' => 'RUSSIAN FEDERATION',
			'RW' => 'RWANDA',
			'BL' => 'SAINT BARTH√âLEMY',
			'SH' => 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA',
			'KN' => 'SAINT KITTS AND NEVIS',
			'LC' => 'SAINT LUCIA',
			'MF' => 'SAINT MARTIN (FRENCH PART)',
			'PM' => 'SAINT PIERRE AND MIQUELON',
			'VC' => 'SAINT VINCENT AND THE GRENADINES',
			'WS' => 'SAMOA',
			'SM' => 'SAN MARINO',
			'ST' => 'SAO TOME AND PRINCIPE',
			'SA' => 'SAUDI ARABIA',
			'SN' => 'SENEGAL',
			'RS' => 'SERBIA',
			'SC' => 'SEYCHELLES',
			'SL' => 'SIERRA LEONE',
			'SG' => 'SINGAPORE',
			'SX' => 'SINT MAARTEN (DUTCH PART)',
			'SK' => 'SLOVAKIA',
			'SI' => 'SLOVENIA',
			'SB' => 'SOLOMON ISLANDS',
			'SO' => 'SOMALIA',
			'ZA' => 'SOUTH AFRICA',
			'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
			'SS' => 'SOUTH SUDAN',
			'ES' => 'SPAIN',
			'LK' => 'SRI LANKA',
			'SD' => 'SUDAN',
			'SR' => 'SURINAME',
			'SJ' => 'SVALBARD AND JAN MAYEN',
			'SZ' => 'SWAZILAND',
			'SE' => 'SWEDEN',
			'CH' => 'SWITZERLAND',
			'SY' => 'SYRIAN ARAB REPUBLIC',
			'TW' => 'TAIWAN, PROVINCE OF CHINA',
			'TJ' => 'TAJIKISTAN',
			'TZ' => 'TANZANIA, UNITED REPUBLIC OF',
			'TH' => 'THAILAND',
			'TL' => 'TIMOR-LESTE',
			'TG' => 'TOGO',
			'TK' => 'TOKELAU',
			'TO' => 'TONGA',
			'TT' => 'TRINIDAD AND TOBAGO',
			'TN' => 'TUNISIA',
			'TR' => 'TURKEY',
			'TM' => 'TURKMENISTAN',
			'TC' => 'TURKS AND CAICOS ISLANDS',
			'TV' => 'TUVALU',
			'UG' => 'UGANDA',
			'UA' => 'UKRAINE',
			'AE' => 'UNITED ARAB EMIRATES',
			'GB' => 'UNITED KINGDOM',
			'US' => 'UNITED STATES',
			'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
			'UY' => 'URUGUAY',
			'UZ' => 'UZBEKISTAN',
			'VU' => 'VANUATU',
			'VE' => 'VENEZUELA, BOLIVARIAN REPUBLIC OF',
			'VN' => 'VIET NAM',
			'VG' => 'VIRGIN ISLANDS, BRITISH',
			'VI' => 'VIRGIN ISLANDS, U.S.',
			'WF' => 'WALLIS AND FUTUNA',
			'EH' => 'WESTERN SAHARA',
			'YE' => 'YEMEN',
			'ZM' => 'ZAMBIA',
			'ZW' => 'ZIMBABWE'
		);


		$this->searchtypes=array(
		'artist',
		'album',
		'playlist',
		'track',
		'show',
		'episode',
		);

		/*
		$range_array1=range(0, 1000, 100);
		$range_array2=range(1000, 10000, 1000);
		$range_array3=range(10000, 100000, 2000);
		$range_array4=range(100000, 1000000, 10000);
		$range_array5=range(1000000, 10000000, 100000);
		$range_array6=range(10000000, 100000000, 1000000);

		$this->range_array_all=array_merge(
		$range_array1,
		$range_array2,
		$range_array3,
		$range_array4,
		$range_array5,
		$range_array6
		);
		*/


		
	$this->generateConfig();

	$this->settings=$this->getSettings();


	$this->rangeslidervalues=array(
		$this->settings['sp_range_min'],
		$this->settings['sp_range_max'],
		$this->settings['sp_range_break']

	);


	}


    public function getPage(Request $request)
    {

//$this->getSpotifyTokens();


$this->spotifyapi=SpotifyHelper::instance()->getSpotifySearchTokens();



$ipAddress = $_SERVER['REMOTE_ADDR'];



		$userid = Auth::id();
		
		$channels=array();
		
		$items=array();
		
		$serializedarray=Helperfunctions::instance()->SerializeAdminRequestSearch($request);
		
		
		foreach ($serializedarray as $serializedarray_key => $serializedarray_value)
		{
			$$serializedarray_key=$serializedarray_value;
		}
		
		//$fromdate $currentdate $todate $perpage $i2 $monetize $orderby $ch $genre $quotation_mark $query_words1 $query_words $titlesearch2 $youtubeid $titledecoded
		
		//$realorderby $realmonetize $realgenre $realch $realfromtodate $anysearch
		
		//last logcheck


		if(
		!$request->input('title') || 
		!$request->input('searchtype') || 
		!in_array($request->input('searchtype'),$this->searchtypes))
		{
		return $this->returnEmpty($request);
		}

		
		$requestwoutcache='';
		if($request->input('pagenum')=='1')
		$requestwoutcache=$request->input('requestwoutcache');

		 $this->hideclaimed=$request->input('hideclaimed');
	


		$this->hidespotifyowned=$request->input('hidespotifyowned');


		if($request->input('marketselect')!='')
		$this->market=urldecode($request->input('marketselect'));
		else
		$this->market='';

		

		if($request->input('searchtype') !='')
		$searchtype=$request->input('searchtype');
		else
		$searchtype='artist';

		$searchtype_pl=$searchtype.'s';


		if ($request->input('pagenum'))
		$offset = ($request->input('pagenum')-1)*$this->settings['sp_perpage'];
		else
		$offset = '0';


		$searchgenre='';
		if($request->input('genres') !='')
		{
			//$thegenrearray=sort(explode(',',urldecode($request->input('genres'))));
			$thegenrearray=explode(',',urldecode($request->input('genres')));
			sort($thegenrearray);
			
			foreach ($thegenrearray as $singlegenres)
			{

				$allgenres[]=' genre:"'.$singlegenres.'"';
				
				//$allgenres[]=str_replace(' ','-',$singlegenres);


			}

		$searchgenre=join("", array_filter($allgenres));


		}

		if($ipAddress=='88.91.243.154')
						{
							//echo '<br>Genre:'.$searchgenre;
						}
		
		$isnew='';
		if($request->input('isnew')=='on')
		{
		$isnew=' tag:new';
		}

		$this->yearfromstartyear =1900;
		$this->yeartostartyear=date('Y');

		$yearfromto='';
		if($request->input('yearfrom')!='' && $request->input('yearto'))
		{
			$this->yearfromstartyear = urldecode($request->input('yearfrom'));
			$this->yeartostartyear= urldecode($request->input('yearto'));

		$yearfromto=' year:'.urldecode($request->input('yearfrom')).'-'.urldecode($request->input('yearto'));
		}


		$inputfolmin_cache='';
		$inputfolmax_cache='';
		if($searchtype=='artist' || $searchtype=='playlist')
		{
			if(urldecode($request->input('followers'))!='')
			$followerstringinput=urldecode($request->input('followers'));
			else
			$followerstringinput=$this->rangeslidervalues[0].';'.$this->rangeslidervalues[1];

			$followerstring=explode(';',$followerstringinput);
			$inputfolmin=SpotifyHelper::instance()->transformRangeValue($followerstring[0],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);
			$inputfolmax=SpotifyHelper::instance()->transformRangeValue($followerstring[1],$this->rangeslidervalues[2],$this->rangeslidervalues[1]);


			//$inputfolmin_cache='folmin:'.$inputfolmin;
			//$inputfolmax_cache='folmax:'.$inputfolmax;
		}


		$search_cache_results=array();
		if($searchtype=='artist' || $searchtype=='playlist') //artist or playlist
		{
			
				$search_string=str_replace('_','',$searchtype).
				'_'.str_replace('_','',urldecode($request->input('title'))).
				'_'.str_replace('_','',$yearfromto).
				'_'.str_replace('_','',$this->market).
				'_'.str_replace('_','',trim($searchgenre)).
				'_'.str_replace('_','',$inputfolmin_cache).
				'_'.str_replace('_','',$inputfolmax_cache);


				$row_searchstring_get = DB::table('spotify_search_cache')
					->where('searchstring', '=', base64_encode($search_string))
					->limit(1)
					->get();

					
					foreach ($row_searchstring_get as $row) {
						$search_cache_results[] = $row;
					}
		}
		
		if(!is_null($search_cache_results) && 
		!empty($search_cache_results) &&
		Carbon::now()->timestamp-Carbon::createFromFormat('Y-m-d H:i:s', $search_cache_results[0]->dt)->timestamp<=$this->settings['searchcachekeep'] && $requestwoutcache!='on') //if not in db or expired
		{
		
			
			$this->iscache=1;


			$this->inprogress=$search_cache_results[0]->inprogress;


							$thekeywordid=$search_cache_results[0]->id;

							$thecacheid=$search_cache_results[0]->id;
						
							
			$theresultset = 
					DB::table('spotify_items AS t1')
					->select('t1.*')
					->leftJoin('spotify_itemkeyword_fk AS t2', function($join)
							{
							$join->on('t2.item_id', '=', 't1.id');
							})
					->where('t2.keyword_id', '=', $thekeywordid)
					->where('t1.followercount', '>=', $inputfolmin)
					->where('t1.followercount', '<=', $inputfolmax)
					->where(function($query) use ($searchtype)
						{
							if ($searchtype=='artist' && $this->hideclaimed=='on') {
								$query->where('t1.claimed','!=','1');
							}
						})
						->where(function($query) use ($searchtype)
						{
							if ($searchtype=='playlist' && $this->hidespotifyowned=='on') {
								$query->where('t1.ownername','!=','Spotify');
							}
						})
					->orderByRaw('t1.id ASC')
					->offset($offset)
					->limit($this->settings['sp_perpage'])
					->get();
					

							$s_c=0;
					foreach($theresultset as $theresultset_s)
								{

									$theresultset[$s_c]->followers=new \stdClass();
									$theresultset[$s_c]->followers->total=$theresultset_s->followercount;

									$theresultset[$s_c]->external_urls=new \stdClass();
									$theresultset[$s_c]->external_urls->spotify=$theresultset_s->url;

									$theresultset[$s_c]->images=array();
									$theresultset[$s_c]->images[2]=new \stdClass();
									$theresultset[$s_c]->images[2]->url=$theresultset_s->imageurl;

									$theresultset[$s_c]->mydbid=$theresultset[$s_c]->id;
									$theresultset[$s_c]->id=$theresultset[$s_c]->itemid;

									if($searchtype=='playlist')
									{
									$theresultset[$s_c]->owner=new \stdClass();
									$theresultset[$s_c]->owner->external_urls=new \stdClass();
									$theresultset[$s_c]->owner->external_urls->spotify=$theresultset[$s_c]->ownerurl;
									$theresultset[$s_c]->owner->display_name=$theresultset[$s_c]->ownername;
									}


									

									$s_c++;
								}

								$this->searchresults=$theresultset;

								
								$item_count = 
								DB::table('spotify_items as t1')
								->leftJoin('spotify_itemkeyword_fk AS t2', function($join)
										{
										$join->on('t2.item_id', '=', 't1.id');
										})
									->where('t2.keyword_id', '=', $thekeywordid)
									->where('t1.followercount', '>=', $inputfolmin)
									->where('t1.followercount', '<=', $inputfolmax)
									->where(function($query) use ($searchtype)
										{
											if ($searchtype=='artist' && $this->hideclaimed=='on') {
												$query->where('t1.claimed','!=','1');
											}
										})
										->where(function($query) use ($searchtype)
										{
											if ($searchtype=='playlist' && $this->hidespotifyowned=='on') {
												$query->where('t1.ownername','!=','Spotify');
											}
										})
									->count();
								

								//print_r($item_count);

				
			if(empty($theresultset))
			return $this->returnEmpty($request);



			if($searchtype=='artist') //artist
								{
      SpotifyHelper::instance()->getArtistClaimState($this->searchresults,$this->hideclaimed,$this->user_bearer_token);
			
								}


		

		


		}
		else
		{

			

		$options=new \stdClass();
		$options->limit=$this->settings['sp_perpage'];
		$options->offset=$offset;

		if (in_array($this->market,$this->markets))
		$options->market=$this->market;
		

		$try=true;
		while($try)
		{
			try {
				$apisearchobject=$this->spotifyapi
				->search(urldecode($request->input('title'))
				.$yearfromto.$isnew.$searchgenre, $searchtype,$options);

				$try=false;
			break;
			} catch (\Exception $e) {


					if ($e->getCode() == 429) {

						$responseobject = $this->spotifyapi->getRequest()->getLastResponse();
						$this->responsestatus=$responseobject['status'];
						$retryAfter = $responseobject['headers']['Retry-After'];

					if($ipAddress=='88.91.243.154')
					{
						echo $this->responsestatus;
					}

						if($ipAddress=='88.91.243.154')
						{
							echo $retryAfter;
						}
						sleep($retryAfter);
						
					}
					else
					{

						$responseobject = $this->spotifyapi->getRequest()->getLastResponse();
						$this->responsestatus=$responseobject['status'];

						if($ipAddress=='88.91.243.154')
							{
								echo $retryAfter;
							}
						
						$try=false;
					}
					
			}
		}

	
	



		
		$this->searchresults=$apisearchobject->$searchtype_pl->items;

		
		$item_count=$apisearchobject->$searchtype_pl->total;
	
		if($item_count==0)
		return $this->returnEmpty($request);


/*
// this function is silenced, so to troubleshoot, need to check catch place!
if($searchtype=='playlist') //playlist
{
	SpotifyHelper::instance()->getExtraInformation($this->spotifyapi,'playlist',$itemsfolresult);
}
// this function is silenced, so to troubleshoot, need to check catch place!
*/

		
				if($searchtype=='artist' || $searchtype=='playlist') //artist or playlist
				{
		
			$curoffset=(int) 0;
			$allfolloweritemsobject=array();
		
			$try=true;
			$pagenotfound=0;
			while($curoffset<=$item_count && $try)
			{

				

				$options=new \stdClass();
					$options->limit=$this->settings['sp_perpage'];
					$options->offset=$curoffset;
					if (in_array($this->market,$this->markets))
					$options->market=$this->market;
					
					$try2=true;
					$itemsfolresult=array();
					while($try2)
						{
							try {
								$itemsfolresult=$this->spotifyapi->search(urldecode($request->input('title')).$yearfromto.$isnew.$searchgenre, $searchtype,$options)->$searchtype_pl->items;
								$try2=false;
							} catch (\Exception $e) {

								if ($e->getCode() == 429) {

									$responseobject = $this->spotifyapi->getRequest()->getLastResponse();
									$this->responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];
									

									if($ipAddress=='88.91.243.154')
									{
										echo $this->responsestatus.$retryAfter;
									}


									sleep($retryAfter);

									

								}
								else
								{
									$responseobject = $this->spotifyapi->getRequest()->getLastResponse();
									$this->responsestatus=$responseobject['status'];
									$retryAfter = $responseobject['headers']['Retry-After'];

									if($ipAddress=='88.91.243.154')
									{
										echo $this->responsestatus.$retryAfter.' offset:'.$curoffset;
									}
									$pagenotfound++;
									$try2=false;
								}
							}

						}

					foreach($itemsfolresult as $itemsfolresult_s)
					{
						$itemsfolresult_s_followers=0;

						if(!$itemsfolresult_s->followers)
						{
						$itemsfolresult_s->followers=new \stdClass();
						$itemsfolresult_s->followers->total=0;
						}
						// this function is silenced, so to troubleshoot, need to check catch place!
						if($searchtype=='playlist') //playlist
						{

							$itemsfolresult_s_followers=$itemsfolresult_s
							->followers
							->total=SpotifyHelper::instance()->getSingleFollowerCount($this->spotifyapi,'playlist',$itemsfolresult_s->id);
							
						}
						// this function is silenced, so to troubleshoot, need to check catch place!

						$itemsfolresult_s_followers=$itemsfolresult_s->followers->total;

$sorthidespotifyowned=1;  //allow
if($this->hidespotifyowned=='on' && $searchtype=='playlist' && $itemsfolresult_s->owner->display_name =='Spotify')
$sorthidespotifyowned=0;  //do not allow

					if($itemsfolresult_s_followers>=$inputfolmin && $itemsfolresult_s_followers<=$inputfolmax && $sorthidespotifyowned)
					$allfolloweritemsobject[]=$itemsfolresult_s;

					if($itemsfolresult_s_followers>=0 && $itemsfolresult_s_followers<=100000000)
					$allfolloweritemsobject_insertable[]=$itemsfolresult_s;
					
					}
		
		//print_r($allfolloweritemsobject);exit;
				$curoffset+=$this->settings['sp_perpage'];

				if($pagenotfound>5 || $curoffset>=$this->settings['max_spotify_'.$searchtype.'_cache_request'])
				$try=false;
			}
			

			$item_count=count(array_filter($allfolloweritemsobject_insertable));

			$this->searchresults = array_slice($allfolloweritemsobject, $offset, $this->settings['sp_perpage']);

			

		//}

					if(!empty($allfolloweritemsobject_insertable))
								{
						
									

	if($item_count>=($this->settings['max_spotify_'.$searchtype.'_cache_request']-$this->settings['sp_perpage']))
			$this->inprogress=1;
							

		if(!empty($search_cache_results) && $search_cache_results[0]->id>0) //in database
		{

				$this->inprogress=$search_cache_results[0]->inprogress;
		
				if($this->settings['reset_spotify_searches'])
				{
				$this->inprogress=1;
				
				}
				else
				{
					$item_count=$search_cache_results[0]->item_count;
				}

		}
		
	



				//'resultset' => serialize($allfolloweritemsobject_insertable)

				
					DB::table('spotify_search_cache')
						->updateOrInsert(
					['searchstring' => base64_encode($search_string)],
					['item_count'=> $item_count,
					'userid'=>$userid,
					'dt' => Carbon::now(),
					'inprogress'=> $this->inprogress]
							);


					$last_id='';
					$last_id = DB::getPdo()->lastInsertId();

					if($last_id>0)
							{
								DB::table('spotify_search_cache')
								->where('id', '=', $last_id)
								->update(['timestamp' => Carbon::now()->timestamp]);


							}


							

							$updatedOrInsertedRecord='';

$updatedOrInsertedRecord = DB::table('spotify_search_cache')
					->where('searchstring', '=', base64_encode($search_string))
					->first();

					$keyword_id=$updatedOrInsertedRecord->id;

					$thecacheid=$updatedOrInsertedRecord->id;

//$insertable_spoti_result=array_reverse($allfolloweritemsobject_insertable);
$insertable_spoti_result=$allfolloweritemsobject_insertable;
foreach ($insertable_spoti_result as $single_item)
{

	$imageurl='';
if($single_item->images[2]->url)
$imageurl=$single_item->images[2]->url;
elseif($single_item->images[0]->url)
$imageurl=$single_item->images[0]->url;

	if($searchtype=='artist') //artist
				{

					
					DB::table('spotify_items')
					->updateOrInsert(
				['type' => $searchtype,
				 'itemid' => $single_item->id],
				[
					'name' => mb_substr($single_item->name,0, 500,'UTF-8'),
					'followercount' => $single_item->followers->total,
					'genres' => implode(', ', $single_item->genres),
					'popularity' => $single_item->popularity,
					'imageurl' => $imageurl,
					'url' => $single_item->external_urls->spotify,
				'dt' => Carbon::now()]
						);
						

						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
	
						if($last_id>0)
								{
									DB::table('spotify_items')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
	
	
								}
				
	
				
				}
	elseif($searchtype=='playlist') //playlist
				 { 



					DB::table('spotify_items')
					->updateOrInsert(
				['type' => $searchtype,
				 'itemid' => $single_item->id],
				[
					'name' => mb_substr($single_item->name,0, 500,'UTF-8'),
					'followercount' => $single_item->followers->total,
					'imageurl' => $imageurl,
					'url' => $single_item->external_urls->spotify,
					'ownerurl' => $single_item->owner->external_urls->spotify,
					'ownername' => mb_substr($single_item->owner->display_name,0, 500,'UTF-8'),
					'description' => $single_item->description,
					'collaborative' => $single_item->collaborative,
				'dt' => Carbon::now()]
						);

						
						$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
	
						if($last_id>0)
								{
									DB::table('spotify_items')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
	
	
								}


				 }



				 $updatedOrInsertedRecord2='';

				 $updatedOrInsertedRecord2 = DB::table('spotify_items')
					->where('type', '=', $searchtype)
					->where('itemid', '=', $single_item->id)
					->first();

					$item_id=$updatedOrInsertedRecord2->id;

				 DB::table('spotify_itemkeyword_fk')
					->updateOrInsert(
				['item_id' => $item_id,
				 'keyword_id' => $keyword_id],
						);




						//genres
			if($searchtype=='artist') //artist
				{
						foreach($single_item->genres as $singlegenre)
						{
			
							$singlegenre=strtolower(trim($singlegenre));
			
			
									$getGenreRecord = DB::table('spotify_genres')
								->where('name', '=', $singlegenre)
								->first();
			
								$genreid='';
								if($getGenreRecord->id >0)
								{
			
									$genreid=$getGenreRecord->id;
			
								}
								else
								{
			
			
									
									DB::table('spotify_genres')
								->insert([
								'name' => $singlegenre,
								'firstoccured_type' => $searchtype,
							'dt' => Carbon::now()
							]);

							

							$last_id='';
						$last_id = DB::getPdo()->lastInsertId();
			
						if($last_id>0)
								{
									DB::table('spotify_genres')
									->where('id', '=', $last_id)
									->update(['timestamp' => Carbon::now()->timestamp]);
			
			
								}



									$genreid= $last_id;
			
								}
								
			
								DB::table('spotify_itemgenre_fk')
								->updateOrInsert(
							['item_id' => $item_id,
							 'genre_id' => $genreid],
									);
			
			
			
						}
					}

					//genres




}


						


								}



								
								if($searchtype=='artist') //artist
								{

	SpotifyHelper::instance()->getArtistClaimState($this->searchresults,$this->hideclaimed,$this->user_bearer_token);
								}
								


				} //artist or playlist


			} //if not in db


			


				if($searchtype=='track' || $searchtype=='episode')
				{
					
	$this->searchresults=SpotifyHelper::instance()->formatDurations($this->searchresults);
//$this->formatDurations($this->searchresults);
				}



		$paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,$item_count,$this->settings['sp_perpage']);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}

		
		$meta=array(

		'title' => 'Search - Admin Panel | '.config('myconfig.config.sitename_caps'),

		'description' => 'Search - Admin Panel',

		'keywords' => '',

	);


		return view('admin/search', [
		'responsestatus'=>$this->responsestatus,
		'retryafter'=>$retryafter,
		'searchresults'=>$this->searchresults,
		'meta' => $meta,
		'item_count'=> number_format($item_count),
		'title'=>urldecode($request->input('title')),
		'orderby'=>$orderby,'i2'=>$i2,'perpage'=>$this->settings['sp_perpage'],
		'pagination'=>$pagination,
		'paginationdisplay'=>$paginationdisplay,
		'markets'=>$this->markets,
		'yearfromstartyear'=>$this->yearfromstartyear,
		'yeartostartyear'=>$this->yeartostartyear,
		'rangeslidervalues'=>$this->rangeslidervalues,
		'fromfollowers'=>$this->fromfollowers,
		'tofollowers'=>$this->tofollowers,
		'inprogress'=>$this->inprogress,
		'iscache'=>$this->iscache,
		'thecacheid'=>$thecacheid,
		]);
		
	}
	

	public function returnEmpty(Request $request)
	{

		$meta=array(

			'title' => 'Search:'.urldecode($request->input('title')).' - No Results - Admin Panel | '.config('myconfig.config.sitename_caps'),
	
			'description' => 'Search:'.urldecode($request->input('title')).' - No Results - Admin Panel',
	
			'keywords' => '',
	
		);

		$paginationarray=Helperfunctions::instance()->CreateAdminPagination($request,0,10);
		
		
		foreach ($paginationarray as $paginationarray_key => $paginationarray_value)
		{
			$$paginationarray_key=$paginationarray_value;
		}
		
		return view('admin/search', [
			'responsestatus'=>$this->responsestatus,
			'retryafter'=>'','searchresults'=>'',
			'meta' => $meta,
			'item_count'=> 0,
			'title'=>urldecode($request->input('title')),
			'orderby'=>'','i2'=>0,'perpage'=>0,
			'pagination'=>'',
			'paginationdisplay'=>$paginationdisplay,
			'markets'=>$this->markets,
			'yearfromstartyear'=>$this->yearfromstartyear,
			'yeartostartyear'=>$this->yeartostartyear,
			'rangeslidervalues'=>$this->rangeslidervalues,
			'fromfollowers'=>$this->fromfollowers,
			'tofollowers'=>$this->tofollowers,
			'inprogress'=>$this->inprogress,
			'iscache'=>$this->iscache,
			'thecacheid'=>$thecacheid,
		]);
		

	}
	
	
	
}
