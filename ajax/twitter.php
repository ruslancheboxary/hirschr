<?php
//error_reporting(0);
//set_time_limit(60);
$vx_perem=$_GET['screen_name'];
if(strlen($vx_perem>0))
{
//echo '<meta charset="utf-8" />';
//С сайта http://www.niceseo.ru/pishem-php-vidzhet-vyivoda-poslednih-tvitov-twitter-api-1-1.html
function niceseotwitter($niceseotwitter1,$niceseotwitter2,$niceseotwitter3,$niceseotwitter4,$niceseotwitter5,$niceseotwitter6) {
 require_once('functions-TwitterAPIExchange.php');
 $twitter = new TwitterAPIExchange(array('oauth_access_token'=>$niceseotwitter1,'oauth_access_token_secret'=>$niceseotwitter2,'consumer_key'=>$niceseotwitter3,'consumer_secret'=>$niceseotwitter4));
 $niceseotwitter = json_decode($twitter->setGetfield('?screen_name='.$niceseotwitter6.'&count='.$niceseotwitter5)->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json','GET')->performRequest());
 $i=0;while ($i<$niceseotwitter5) {
 $tweetsm=(string)$niceseotwitter[$i]->text;
 $tweetsm=str_replace(array('.',',',';','','-'),array(' ',' ',' ',' ',' '),$tweetsm);
 $tweetsm=mb_strtolower($tweetsm,'utf8');
 $tweetsm = preg_replace("|[^абвгдеёжзийклмнопрстуфхцчшщьыъэюя1234567890\- ]|i", NULL, $tweetsm );
 //$tweetsm = str_replace(0xFFFD,'',$tweetsm);
 //$tweetsm  = preg_replace('@\x{FFFD}@u','',$tweetsm);
 $tweets[]=$tweetsm;
 $i++;
 }
 return $tweets;
}
//
$tweets=niceseotwitter("181556048-bwpH0CcHwtrIZ0shvHHth0dtVcfHSPGCUgGQB2Ww","aQ0b2RFmgNTF5px84ozHdJMo4HO7hA31if4ToGOyOaw","ndxTRnCNh6F3JQSyCie11Q","3Pvf5BHLUHiFzZhSxgl2wKg8GYtD5UWBVXheu3vkA", 20, $vx_perem);
// Поиск книг на сервере amazon.com
$texts=$tweets;
$textr='';
foreach ($texts as $k=>$v)
{
$textr.='text='.urlencode(''.$v.'').'&';
}
$textr=substr($textr,0,-1);
//Curl -проверка орфографии
$url = "http://speller.yandex.net/services/spellservice/checkTexts?".$textr; 
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL,$url); // set url to post to 
//curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects 
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // times out after 4s 
//curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
//curl_setopt($ch, CURLOPT_POSTFIELDS, "url=index%3Dbooks&field-keywords=PHP+MYSQL"); // add POST fields 
$result = curl_exec($ch); // run the whole process 

curl_close($ch);  
$movies = new SimpleXMLElement($result);
$movies=(array)$movies;
$str_replace_1=array();
$str_replace_2=array();
foreach($movies["SpellResult"] as $kx=>$vx)
{
$vx=(array)$vx->error;
//print_r($vx);
if(strlen((string)$vx['s'])>0)
{
$word=(string)$vx['word'];
$s=(string)$vx['s'];
}
else
{
$word='';
$s='';
}

$str_replace_1[]=$word;
$str_replace_2[]=(string)$vx['s'];
}
foreach ($texts as $k=>$v)
{
$texts[$v]=str_replace($str_replace_1,$str_replace_2,$v);
}
$text_obs=implode(' ',$texts);
$text_obs= preg_replace('/([ !();:,.?])\\1+/','$1',$text_obs);
$slova=split(' ',$text_obs);

$строка=$slova;
######Postgress#################
function sort_p($a, $b)
{
return $b['price'] - $a['price'];
//return strcasecmp ($b["price"], $a["price"]);
}
// database access parameters
// alter this as per your configuration

$host = "localhost";
$user = "hirschr";
$pass = "hirschr";
$db = "hirschr";
$tablename = "books";

//Подключение к postreges
$connection = pg_connect ("host=$host dbname=$db user=$user password=$pass");
if (!$connection)
{
die("Could not open connection to database server");
}

for($il=0;isset($строка[$il]);$ill++)
{
	//1-ый вариант
	//sql-запрос с первым и вторым словом
	$string_query=$строка[$il].' '.$строка[$il+1];
        $query = "select id from books where to_tsvector('russian', text) @@ plainto_tsquery('russian', '".$string_query."');";
		$result= pg_query($connection, $query) or die("Error in query: $query. " .pg_last_error($connection));
		$rows = pg_num_rows($result);
		//Если словосочетание найдено
		if ($rows > 0)
		{
		// Тут будут id-шники текстов совпадений - задача для каждого id -шника текста сохранить
		//общее количество совпадений больших 2
		for ($i=0; $i<$rows; $i++)
		{
		$row = pg_fetch_row($result,$i);
		$exit='n';
		$id=$row['id'];
		$title=$row['title'];
		
		//Надо чтобы спикок найденных ключей сохранялся
		 for($ifm=1;$exit!='y';$ifm++){

				$string_query.=' '.$строка[$il+1+$ifm];
				$queryt = "SELECT id, title FROM ".$tablename." WHERE id=".intval($id).";";
				$resultt2= pg_query($connection, $queryt) or die("Error in query: $query. " .pg_last_error($connection));
				// get the number of rows in the resultset
				// this is PG-specific
				$rowsm = pg_num_rows($resultt2);
				if ($rowsm > 0)
				{
				//Если найдены => три ,четыре слова ничего не делать
				}
				}
				else
				{
					/*Перенести вниз и записывать результат там*/
					$rowbrid=$id;
					$rowbrtitle=$title;
					if(strlen($result_arr[$rowbrid]['price'])<1)
					{
					$result_arr[$rowbrid]['price']=0;
					$sum=0;
					};
					$sum=$ifm;
					$result_arr[$rowbrid]['price']=$result_arr[$rowbrid]['price']+$ifm;//Повышаем цену в зависимсти от количества слов
					$result_arr[$rowbrid]['title']=$rowbrtitle;
					$exit='y';
				}
		 }
		}
}
uasort($result_arr,"sort_p");
$i=0;
foreach($result_arr as $k=>$v)
{
$i++;
if($i<10)
{
$result_arrb[$i]=$v;
}
else
{
break;
}
}
$result_arr=array();
$echo=json_encode($result_arrb);
echo $echo;
}
/*
// generate and execute a query
$query = "SELECT id FROM ".$tablename.""; 
$result= pg_query($connection, $query) or die("Error in query: $query. " .pg_last_error($connection));
// get the number of rows in the resultset
// this is PG-specific
$rows = pg_num_rows($result);

// if records present
if ($rows > 0)
{
// iterate through resultset
for ($i=0; $i<$rows; $i++)
{
$row = pg_fetch_row($result, $i);
}
}
*/
?>