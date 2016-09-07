<?php
namespace Craft;

class CommerceAusPostLookupController extends BaseController
{
    protected $allowAnonymous = true;

    public function actionLookup()
    {
        //Must be called by POST
        $this->requirePostRequest();

        //Get plugin settings
        $settings = craft()->plugins->getPlugin('commerceAusPostLookup')->getSettings();
        
        //Called via Ajax?
        $ajax = craft()->request->isAjaxRequest();

        //Must be called by Ajax
        if(!$ajax){
            return;
        }

        //We'll return all the POST data to the template, so kick of our return data with that...
        $vars = craft()->request->getPost();
 
         //hold a list of possible errors to log
        $errors = array();

        //build the URL to the auspost API
        $postcodeURL = 'http://' . $settings->urlPrefix . '/api/postcode/search.json?q=' . urlencode($_REQUEST['query']) . '&state=';

        // Lookup domestic parcel types (different kinds of standard boxes etc)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postcodeURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('AUTH-KEY: ' . $settings->apiKey));
        $rawBody = curl_exec($ch);

        // Check the response: if the body is empty then an error occurred
        if(!$rawBody){
          $errors[] = 'Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
        }

        try{
            // All good, lets parse the response into a JSON object if possible
            $pcJSON = json_decode($rawBody);
        }
        catch (Exception $e) {
            $errors[] = "Caught Exception in JSON decode: " . $e->getMessage();
        }

        //@TODO - change this to use DB lookups or will Commerce pre-populate so we can fix these??
        //Auspost will return VIC, SA etc - we need to map these to matching names and Ids for Commerce

        $statesNameMap = array(
            "VIC"  => "Victoria",
            "SA" => "South Australia",
            "WA" => "Western Australia",
            "NT" => "Northern Territory",
            "NSW" => "New South Wales",
            "QLD" => "Queensland",
            "TAS" => "Tasmania",
            "ACT" => "Australian Captial Territory",
        );

        $statesIDMap = array(
            "VIC"  => 1,
            "SA" => 2,
            "WA" => 3,
            "NT" => 4,
            "NSW" => 5,
            "QLD" => 6,
            "TAS" => 7,
            "ACT" => 8
        );

        //Process the results and put into a suggestions array - we just return this empty if there are no results
        $suggestions = array();

        if(is_object($pcJSON->localities)){
            foreach($pcJSON->localities as $results){
                //is there just one result?  If so needs to be in an array for following process
                CommerceAusPostLookupPlugin::log("Results: " . var_export($results,true));     
                if(is_object($results)){
                    CommerceAusPostLookupPlugin::log("Only one result returned");
                    $results = array($results);
                }
                //now set up the data for return
                foreach($results as $locality){
                    array_push($suggestions,
                                    array('value'=> $locality->postcode . " - " . $locality->location . " - " . $locality->state, 
                                          'data' => array('postcode'    => $locality->postcode, 
                                                          'suburb'      => $locality->location, 
                                                          'stateName'   => $statesNameMap[$locality->state], 
                                                          'stateId'     => $statesIDMap[$locality->state],
                                                          'state'       => $locality->state,
                                                          )
                                          )
                                );
                }
            }
        }

        if($errors){
            $allErrors = "";
            foreach($errors as $error){
                CommerceAusPostLookupPlugin::logError("Error: " . $error);
                $allErrors .= "[". $error . "]";
            }
            $this->returnJson(["success"=>false, 'suggestions'=>[], 'pcJSON'=>[], 'error' => $allErrors]);
        }

        //Return no matter what - either results or an empty array which will prompt the not found message
        $this->returnJson(["success"=>true, 'suggestions'=>$suggestions, 'pcJSON'=>$pcJSON]);

    }
}
