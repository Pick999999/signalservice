<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);   
     $data = json_decode(file_get_contents('php://input'), true);
     if ($data) {
       
        //require_once("newutil2.php");		
        if ($data['Mode'] == 'getAction') { MaingetAction($data); }
        return;
     } else {
        echo 'No Data' ;
	 }
  
  function MaingetAction($data) { 
/*


*/
  
	       $requestActionCode = $data['algo'] ;
		   $transCode = $requestActionCode;
		   //$transCode = $data['transCode'];
		   
		   //echo "--" . $requestActionCode ;
		   $candleData = $data['rawData']['candles'] ;
    
		   for ($i=0;$i<=count($candleData)-1;$i++) {
              $sObj = new stdClass();
			  $sObj->close = $candleData[$i]['close'] ;
			  $sObj->open = $candleData[$i]['open']  ;
              $sObj->high = $candleData[$i]['high']  ;
			  $sObj->low = $candleData[$i]['low']  ;
			  $sObj->time = $candleData[$i]['epoch']  ;
			  $candleDataA[] = $sObj ;		     
		   }

           $lastIndex = count($candleData)-1;
		   $timeCandle = $candleData[$lastIndex]['epoch'] ;

		   $candleData2 = JSON_DECODE(JSON_ENCODE($candleDataA),true);
		   //echo JSON_ENCODE($candleData2);
		   //return;
		   //echo $requestActionCode  . ' Len Data = ' . count($rawData);
		   //return;
		   
		   if ($requestActionCode === 'Claude') {
			   $result =  getSuggestByClaude($candleData2);               
		   }
		   if ($requestActionCode === 'ChatGPT') {
               $result = getSuggestByCHATGPT($candleData2) ;
		   }

		   if ($requestActionCode === 'DeepSeek') {
               $result = getSuggestByDeepSeek($candleData2)  ;
		   }
		   if ($requestActionCode === 'Class Trade') {
               $result = getSuggestByClassTrade($candleData2) ;
		   }
		   if ($requestActionCode === 'alterColor') {
             
		   } 
 
           if ($result === 'Green') { $action = 'CALL'; }
           if ($result === 'Red') { $action = 'PUT'; }

		   $sObj = new stdClass();
           $sObj->requestActionCode = $requestActionCode ;
           $sObj->timeCandle = $timeCandle ;
           $sObj->timeDisplay = date('Y-m-d H:i:s',$timeCandle) ;
		   $sObj->action = $action ;


           $str_json=json_encode($sObj, JSON_UNESCAPED_UNICODE  | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
 

		   echo $str_json ;



  
  } // end function


function getSuggestByClaude($candleData) {


require_once("api/Claude/candleAnalyzerClaude.php"); 


$analyzer = new CandlestickAnalyzerClaude($candleData);

// Get all analyses
$completeAnalysis = $analyzer->getCompleteAnalysis(); 
$prediction = $analyzer->getNextCandlePrediction();
//print_r($prediction);
//$recommendedIndicators = $analyzer->getRecommendedIndicators();
$greenPercent = $prediction['green'] ;
$RedPercent = $prediction['red'] ;
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';



return $suggestColor;


} // end function


function getSuggestByDeepSeek($candleData) {


require_once("api/DeepSeek/CandlestickAnalyzer_DeepSeek.php"); 
$analyzer = new AdvancedCandlestickAnalyzer($candleData);


// 1. ตัวชี้วัด
	//$indicators = $analyzer->getIndicators();
	// 2. วิเคราะห์ลักษณะแท่งเทียน
	//$patterns = $analyzer->analyzeCandlestickPatterns();
	// 3. วิเคราะห์แนวโน้ม
    //$trend = $analyzer->analyzeTrend();
    // 4. ทำนายแท่งถัดไป
    $prediction = $analyzer->predictNextCandle();
    // 5. แนะนำ Indicator เพิ่มเติม
    //$suggestions = $analyzer->suggestAdditionalIndicators();

    //print_r($prediction);


$greenPercent = $prediction['green'] ;
$RedPercent = $prediction['red'] ;
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';
/*
echo '<hr><hr><h2> By Deep Seek </h2>';

echo "Green=" . $greenPercent . " Red=" . $RedPercent  . '<br>';
$suggestColor =   ($greenPercent > $RedPercent) ? 'Green' : 'Red';
echo 'Suggest Color= ' . $suggestColor;
echo '<br>**********  end DeepSeek ***********<hr>';
*/


return $suggestColor;


} // end function


function getSuggestByCHATGPT($candleData) {


require_once("api//Chatgpt4/candleAnalyzerChatGPT.php"); 

$tradeAnalyzer = new TradeAnalyzer($candleData);

//print_r($tradeAnalyzer->getIndicators());
$prediction = $tradeAnalyzer->predictNextCandle();
if ($prediction['green'] > $prediction['red']) {
	$suggestColor = 'Green';
} else {
	$suggestColor = 'Red';
}
return $suggestColor;
/*
echo '<h2> By CHATGPT </h2>';
echo "Probability of Green: " . $prediction['green'] . "<br>";
echo "Probability of Red: " . $prediction['red'] . "<br>";
*/

} // end function


function getSuggestByClassTrade($candleData) { 

echo "ALGO Class Trade"; 
require_once('api/phpCandlestickIndy.php');
$clsStep1 = new TechnicalIndicators();   

require_once('api/phpAdvanceIndy.php');
$clsStep2 = new AdvancedIndicators();   
$result = $clsStep1->calculateIndicators($candleData);
$result2= $clsStep2->calculateAdvancedIndicators($result);
$result2= Final_AdvanceIndy($result2)  ;



$stAnaly = JSON_ENCODE($result2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ;
$myfile = fopen("newDerivObject/AnalyData2.json", "w") or die("Unable to open file!");
fwrite($myfile, $stAnaly);
fclose($myfile); 

$sAr = array();
for ($i=0;$i<=count($result2)-1;$i++) {
  $sObj = new stdClass();
  $sObj->candleID   = $result2[$i]["candleID"] ;
  $sObj->timefrom_unix   = $result2[$i]["timefrom_unix"] ;
  //$sObj->thisColor = $result2["$"]

   
}



require_once("api/newutil2.php"); 
require_once("iqlab/clsTradeVer0/clsTradeVer_Object.php"); 
$clsTrade = new clsTrade();
require_once('iqlab/clsTradeVer0/getActionFromIDVerObject.php');


$lastIndex = count($result2) -1 ;
$AnalyObj = $result2[$lastIndex] ;
/*
list($thisAction,$actionReason) = getActionFromIDVerObject($AnalyObj ,$macdThershold=0.5,$lastMacdHeight=0);
*/

list($thisAction,$actionReason,$CaseNo) = getActionFromIDVerObject_Sorted($AnalyObj ,$macdThershold=0.5,$lastMacdHeight=0);


$suggestColor = ($thisAction == 'CALL') ? 'Green' : 'Red';
//echo $AnalyObj['timefrom_unix'] . '@#';
$thisColor = $AnalyObj['thisColor'] ;
return $suggestColor ;
return array($suggestColor,$AnalyObj['timefrom_unix'],$thisColor,$actionReason,$CaseNo) ;



} // end function


?>
