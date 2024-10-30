<?php 
function idavi_HtmlPrintBoxHeader($id, $title, $right = false) {
	global $mode;
	if($mode == 27) { ?>
			<div id="<?php echo $id; ?>" class="postbox">   
				<h3 >&nbsp;<?php echo $title ?></h3>
				<div class="inside">
<?php } else {
			?>
			<fieldset id="<?php echo $id; ?>" class="dbx-box">
				<?php if(!$right): ?><div class="dbx-h-andle-wrapper"><?php endif; ?>
				<h3 class="dbx-handle"><?php echo $title ?></h3>
				<?php if(!$right): ?></div><?php endif; ?>
				
				<?php if(!$right): ?><div class="dbx-c-ontent-wrapper"><?php endif; ?>
					<div class="dbx-content">
			<?php
	}
}
	
function idavi_HtmlPrintBoxFooter( $right = false) {
	global $mode;
	
	if($mode == 27) {
			?>
				</div>
			</div>
			<?php
	} else {
			?>
				<?php if(!$right): ?></div> <?php endif; ?>
				</div> 
			</fieldset>
			<?php
	}
}

function idavi_simpleXMLToArray($xml,
                    $flattenValues=true,
                    $flattenAttributes = true,
                    $flattenChildren=true,
                    $valueKey='@value',
                    $attributesKey='@attributes',
                    $childrenKey='@children'){

        $return = array();
        if(!($xml instanceof SimpleXMLElement)){return $return;}
        $name = $xml->getName();
        $_value = trim((string)$xml);
        if(strlen($_value)==0){$_value = null;};

        if($_value!==null){
            if(!$flattenValues){$return[$valueKey] = $_value;}
            else{$return = $_value;}
        }

        $children = array();
        $first = true;
        foreach($xml->children() as $elementName => $child){
            $value = idavi_simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);
            if(isset($children[$elementName])){
                if($first){
                    $temp = $children[$elementName];
                    unset($children[$elementName]);
                     $children[$elementName][] = $temp;
                    $first=false;
                }
                 $children[$elementName][] = $value;
            }
            else{
                $children[$elementName] = $value;
            }
        }
        if(count($children)>0){
            if(!$flattenChildren){$return[$childrenKey] = $children;}
            else{$return = array_merge($return,$children);}
        }

        $attributes = array();
        foreach($xml->attributes() as $name=>$value){
            $attributes[$name] = trim($value);
        }
        if(count($attributes)>0){
            if(!$flattenAttributes){$return[$attributesKey] = $attributes;}
            else{$return = array_merge($return, $attributes);}
        }
        return $return;
    }
    
function idavi_get_rss($url) {
	$rss = "";
	if (trim($url) != "") 
	{
		$xmlStr = file_get_contents($url);
		//var_export($xmlStr);
		libxml_use_internal_errors(true);
		$xmlObj = simplexml_load_string($xmlStr);
		$rss = idavi_simpleXMLToArray($xmlObj,false);
	}
		return $rss; 
}

//function idavi_get_rss($url) {
//	$rss = "";
//	if (trim($url) != "") 
//	{
//		$xmlStr = file_get_contents($url);
//		libxml_use_internal_errors(true);
//		$xmlObj = simplexml_load_string($xmlStr);
//		$rss = idavi_simpleXMLToArray($xmlObj,false);
//	}
//		return $rss; 
//}

function idavi_fetchcategory(){
	$cat_url ="http://idavi.com/feed/categories/xml";
	if (trim($cat_url) != "") 
	{
		$xmlStr = file_get_contents($cat_url);
		libxml_use_internal_errors(true);
		$xmlObj = simplexml_load_string($xmlStr);
		$catrss = idavi_simpleXMLToArray($xmlObj,false);
		
		//echo $catrss['category'][0]['@value'];
		$res[]= "'All Categories'";
		$val[]= "'0'";
		$count = count($catrss['category']);
		for($i=0;$i<$count;$i++){
			$res[]= "'".$catrss['category'][$i]['name']['@value']."'";
			$val[]= "'".$catrss['category'][$i]['id']['@value']."'";
			
			//echo $catrss['category'][$i]['name']['@value'] . " / " . $catrss['category'][$i]['id']['@value'] . "\r\n";
  		}
		$cattext = join(',',$res);
		$catval = join(',',$val);
		echo "var fcatname= new Array(".$cattext."); \n ";
		echo "var fcatval= new Array(".$catval."); ";
	}
}

function idavi_addPosts($feed, $Cyclecount, $dayCount, $feedid) 
{
	global $idavi_opts;
		
	$nickname = $idavi_opts['idavi_nickname'];
	$actioncount = 0;
	$maxfeed = $idavi_opts['idavi_maxcyclefeedposts'];
	$maxcycle = $idavi_opts['idavi_maxcycleposts'];
	$maxday = $idavi_opts['idavi_maxposts'];
	$allowpings = ($idavi_opts['idavi_pingback']) ? 'open' : 'closed';
	$allowcomments = ($idavi_opts['idavi_comments']) ? 'open' : 'closed';
	
	if (trim($maxfeed==""))
		$maxfeed='1';
	
	if (trim($maxcycle==""))
		$maxcycle='1';
	
	
	for ($l = 0; $l < count($feed['item']); ++$l)
		{
		$description = html_entity_decode($feed['item'][$l]['image']['@value']);
		$description .= "<br><br>";
		$description .= html_entity_decode($feed['item'][$l]['description']['@value']);
		$title = $feed['item'][$l]['title']['@value'];
		
		// Make sure the word count is sufficient
      	//$wordCount = count(preg_split('@\s+@', $description));
      	//if ($wordCount < $length) { continue; }

      	if (trim($idavi_opts['idavi_excerptformat']) != "") {
      		//we need to do the excerpt re-write because  format has been specified
      		$tmpoutput = $idavi_opts['idavi_excerptformat'];
      		$tmpoutput = str_replace("[TITLE]", $title, $tmpoutput);
			$tmpoutput = str_replace("[DESCRIPTION]", $description, $tmpoutput);
			$tmpoutput = str_replace("[LINK]", $feed['item'][$l]['link']['@value'], $tmpoutput);
			$tmpoutput = str_replace("[IMAGE]", $feed['item'][$l]['image']['@value'], $tmpoutput);
			$tmpoutput = str_replace("[KEYWORDS]", $feed['item'][$l]['keywords']['@value'], $tmpoutput);
			$tmpoutput = str_replace("[PRICE]", $feed['item'][$l]['price']['@value'], $tmpoutput);

			$excerpttext = $tmpoutput;
      	} else 
      		$excerpttext = $feed['item'][$l]['description'];
      		
      	if (trim($idavi_opts['idavi_postformat']) != "") {
      		//we need to do the description re-write because  format has been specified
      		$tmpoutput = $idavi_opts['idavi_postformat'];
      		$tmpoutput = str_replace("[TITLE]", $title, $tmpoutput);
			$tmpoutput = str_replace("[DESCRIPTION]", $description, $tmpoutput);
			$tmpoutput = str_replace("[LINK]", $feed['item'][$l]['link']['@value'], $tmpoutput);
			$tmpoutput = str_replace("[IMAGE]", $feed['item'][$l]['image']['@value'], $tmpoutput);
			$tmpoutput = str_replace("[KEYWORDS]", $feed['item'][$l]['keywords']['@value'], $tmpoutput);
			$tmpoutput = str_replace("[PRICE]", $feed['item'][$l]['price']['@value'], $tmpoutput);

			$description = $tmpoutput;      		
      	}
      
      	//if ($idavi_opts["idavi_linkback"]) {
         	$description .= "\n\n<p>" . 'Read More: <a href="' . $feed['item'][$l]['link']['@value']. '">' . "$title</a></p>\n";
      	//}
      
      	$description .= "\n\n<p>" . "Brought to you by <a href=\"http://idavi.com?a=" . $nickname . "\">iDavi</a></p>\n";

      	//work the whole category stuff here before we go on

      	switch ($idavi_opts['idavi_categoryoverride'])
      	{

      		case 'iDavi':
      			if ( $feed['item'][$l]['category'] != "")
      				$category = $feed['item'][$l]['category'];
      			else
      				$category = $idavi_opts['idavi_pages_ct'][$feedid];
      				
      			break;
      			
      		case 'always':
      			if ( $feed['item'][$l]['frontpagecategory'] != "")
      				$category = $feed['item'][$l]['frontpagecategory'];
      			else if ( $feed['item'][$l]['category'] != "")
      				$category = $feed['item'][$l]['category'];
      			else
      				$category = $idavi_opts['idavi_pages_ct'][$feedid];
      			break;
      			
      		default:		//never
      			$category = $idavi_opts['idavi_pages_ct'][$feedid];
      			break;
      	}
		
      	if ($actioncount < $maxfeed && ($actioncount + $Cyclecount) < $maxcycle && ( ( ($actioncount + $dayCount) < $maxday) || trim($maxday) == "") ) 
      		if (idavi_insert($title, $description, $category, $allowcomments, $allowpings, $excerpttext, $feed['item'][$l]['keywords'], $idavi_opts['idavi_createcategory'], $idavi_opts['idavi_pages_ct'][$feedid])) 
         		++$actioncount;
		
      	
		if ($actioncount >= $maxfeed || ($actioncount + $Cyclecount) >= $maxcycle || ( ( ($actioncount + $dayCount) >= $maxday) && trim($maxday) != "") )
			return $actioncount;
			
	}
	
	return $actioncount;
	
}



function idavi_addTestimonials($feed, $Cyclecount, $dayCount)
{
	global $idavi_opts;
	
	//if testimonials are turned off just return 0 worked items
	if ($idavi_opts['idavi_testimonials'] == "none")
		return 0;
		
	$actioncount = 0;
	$maxfeed = $idavi_opts['idavi_maxcyclefeedtestimonials'];
	$maxcycle = $idavi_opts['idavi_maxcycletestimonials'];
	$maxday = $idavi_opts['idavi_maxtestimonials'];
	
	for ($l = 0; $l < count($feed['channel']['item']); ++$l)
	{
		$title = $feed['channel']['item'][$l]['title'];
		
		for ($l2 = 0; $l2 < count($feed['channel']['item'][$l]['testimonials']['testimonial']); ++$l2)
		{
			//if there is no subarray then the array is the array of values not an array of testimonials
			if ( trim($feed['channel']['item'][$l]['testimonials']['testimonial']['name']) != "") {
				$description = $feed['channel']['item'][$l]['testimonials']['testimonial']['subject'] . " - " . $feed['channel']['item'][$l]['testimonials']['testimonial']['testimonial'] . "<br><br>" . $feed['channel']['item'][$l2]['Testimonials'][$l2]['name'] . "<BR><i>" . $feed['channel']['item'][$l2]['Testimonials'][$l2]['location'] . "</i>";
				$author = $feed['channel']['item'][$l]['testimonials']['testimonial']['name'];
				$author_url = $feed['channel']['item'][$l]['testimonials']['testimonial']['videourl'];
			} else {
				$description = $feed['channel']['item'][$l]['testimonials']['testimonial'][$l2]['subject'] . " - " . $feed['channel']['item'][$l]['testimonials']['testimonial'][$l2]['testimonial'] . "<br><br>" . $feed['channel']['item'][$l2]['Testimonials'][$l2]['name'] . "<BR><i>" . $feed['channel']['item'][$l2]['Testimonials'][$l2]['location'] . "</i>";
				$author = $feed['channel']['item'][$l]['testimonials']['testimonial'][$l2]['name'];
				$author_url = $feed['channel']['item'][$l]['testimonials']['testimonial'][$l2]['videourl'];
			}
			
			if (trim($author_url) == "" || is_array($author_url))
				$author_url = $feed['channel']['item'][$l]['link'];
		
      		if ($actioncount < $maxfeed && ($actioncount + $Cyclecount) < $maxcycle && ( (($actioncount + $dayCount) < $maxday) || trim($maxday) == "") )
      			if ($idavi_opts['idavi_testimonials'] != "none")
      				if (idavi_insert_comment($title, $description, $author, $author_email, $author_url, $idavi_opts['idavi_testimonials'])) 
         				++$actioncount;
      	
			if ($actioncount >= $maxfeed || ($actioncount + $Cyclecount) >= $maxcycle || ((($actioncount + $dayCount) >= $maxday) && trim($maxday) != "") )
				return $actioncount;
				
			//if there is no subarray then stop the loop now.
			if (trim($feed['channel']['item'][$l]['testimonials']['testimonial']['name']) != "")
				$l2 = count($feed['channel']['item'][$l]['testimonials']['testimonial']);
				
		}
	}
	
	return $actioncount;

	
}

function idavi_nextfeed($feedid)
{
	global $idavi_opts;

	switch ($idavi_opts['idavi_feedpriority'])
   	{
   		case "BottomUp":
   			$feedid = $feedid - 1;
   			break;
   			
   		case "Random":
   			$feedid = rand(1,count($idavi_opts['idavi_pages_ur']));
   			break;
   			
   		case "Cycle":
   			$feedid = $feedid + 1;
   			if ($feedid > count($idavi_opts['idavi_pages_ur']))
   				$feedid = "0";
   			break;
   			
   		default:
   			$feedid = $feedid + 1;
   			break;
   	}
   	
   	return $feedid;
}

function idavi_feedoktorun($feedid)
{
	global $idavi_opts;
	
	if (($idavi_opts[idavi_pages_tm][$feedid] + $idavi_opts[idavi_pages_cf][$feedid]) <= time())
		return true;
	else
		return false;

}

function idavi_run($force=false) {
	global $idavi_opts;
   	// Get the frequency of posts, or default to 1 per day
   	$frequency = $idavi_opts['idavi_frequency'];
   	if ($frequency <= 0) { $frequency = 86400; }

   	$lastrun = intval(get_option("idavi_lastrun"));
   	if ($lastrun <= 0) { $lastrun = 0; }

   	// If the cron job was run too soon, don't create new posts
   	$timeElapsed = time() - $lastrun;
   	if ($timeElapsed < $frequency && !$force) { return; }   
   
   	//we are good to make a run
   	//first, lets determine how we figure out what feed to start with
   	switch ($idavi_opts['idavi_feedpriority'])
   	{
   		case "BottomUp":
   			$feedid = count($idavi_opts['idavi_pages_ur']);
   			break;
   			
   		case "Random":
   			$feedid = rand(1,count($idavi_opts['idavi_pages_ur']));
   			break;
   			
   		case "Cycle":
   			$feedid = get_option("idavi_lastfeed") + 1;
   			if ($feedid > count($idavi_opts['idavi_pages_ur']))
   				$feedid = "0";
   			break;
   			
   		default:
   			$feedid = 0;
   			break;
   	}
   	
   	if (!idavi_feedoktorun($feedid))
   		$feedid = idavi_nextfeed($feedid);
   		
   	$StartingFeed = $feedid;
   	
   	//set the current values before we start our work loop
   	$CyclePosts = 0;
   	$CycleTestimonials = 0;
   	//reset if new day, otherwise re-read the day posts and testimonials
   	if (date("d") == date("d", $lastrun)) {
   		$DayPosts = get_option("idavi_dayposts");
   		$DayTestimonials = get_option("idavi_daytestimonials");
   	} else {
   		$DayPosts = 0;
   		$DayTestimonials = 0;
   	}
   	$noworkcount = 0;
   	$LastWorkType = get_option("idavi_lastworktype");
   	
   	do
   		{
   		$worked=false;
   		$keyword  = $idavi_opts['idavi_pages_ky'];
		$feedcat  = $idavi_opts['idavi_pages_fct'];
		$nickname = $idavi_opts['idavi_nickname'];
		
		if(trim($keyword[$feedid])!='')
			$url=$idavi_opts['idavi_pages_ur']."/xml/?e=".$nickname.'&keywords='.$keyword[$feedid].'&category='.$feedcat[$feedid];
		else 
			$url=$idavi_opts['idavi_pages_ur']."/xml/?e=".$nickname.'&category='.$feedcat[$feedid];
		
		
   		//in any case the feed needs to be read into memory before we do anything with it
   			
		//echo $url . "<br>";
		$idavi_feed = idavi_get_rss($url);
			
   		//var_export($idavi_feed);
   		if ($idavi_feed) {
   		
			
	   		//look at what the priority is for posting
   			switch ($idavi_opts['idavi_commentpriority'])
   			{
   				case "onetoone":
					
   					$idavi_stat = idavi_addPosts($idavi_feed, $CyclePosts, $DayPosts, $feedid);
	   				if ($idavi_stat > 0) {
   						$worked = true;
   						$CyclePosts += $idavi_stat;
   						$DayPosts += $idavi_stat;
   					}
   					
	   				$idavi_stat = idavi_addTestimonials($idavi_feed, $CycleTestimonials, $DayTestimonials);
   					if ($idavi_stat > 0) {
   						$worked = true;
   						$CycleTestimonials += $idavi_stat;
   						$DayTestimonials += $idavi_stat;
	   				}
   				
   					break;
   				
   				case "alternate":
   					$LastWorkType = ($LastWorkType == "P") ? 'T' : 'P';
   				
   					if ($LastWorkType == "P") {
   						$idavi_stat = idavi_addPosts($idavi_feed, $CyclePosts, $DayPosts, $feedid);
	   					if ($idavi_stat > 0) {
   							$worked = true;
   							$CyclePosts += $idavi_stat;
   							$DayPosts += $idavi_stat;
   						}
	   				} else {
   						$idavi_stat = idavi_addTestimonials($idavi_feed, $CycleTestimonials, $DayTestimonials);
   						if ($idavi_stat > 0) {
   							$worked = true;
   							$CycleTestimonials += $idavi_stat;
   							$DayTestimonials += $idavi_stat;
	   					}
   					}
   					break;
   				
   				case "postfirst":
   					$idavi_stat = idavi_addPosts($idavi_feed, $CyclePosts, $DayPosts, $feedid);
	   				if ($idavi_stat > 0) {
   						$worked = true;
   						$CyclePosts += $idavi_stat;
   						$DayPosts += $idavi_stat;
   					}

	   				if (!$worked) {
   						$idavi_stat = idavi_addTestimonials($idavi_feed, $CycleTestimonials, $DayTestimonials);
   						if ($idavi_stat > 0) {
   							$worked = true;
   							$CycleTestimonials += $idavi_stat;
   							$DayTestimonials += $idavi_stat;
	   					}
   					}
   					break;
   				case "testimonialsfirst":
	   				$idavi_stat = idavi_addTestimonials($idavi_feed, $CycleTestimonials, $DayTestimonials);
   					if ($idavi_stat > 0) {
   						$worked = true;
   						$CycleTestimonials += $idavi_stat;
   						$DayTestimonials += $idavi_stat;
	   				}
   				
   					if (!$worked) {
   						$idavi_stat = idavi_addPosts($idavi_feed, $CyclePosts, $DayPosts, $feedid);
   						if ($idavi_stat > 0) {
   							$worked = true;
   							$CyclePosts += $idavi_stat;
   							$DayPosts += $idavi_stat;
	   					}
   					
   					}
   					break;
	   			default:
   			  		$WorkType = (rand(1,2) == "1") ? 'T' : 'P';
   					if ($LastWorkType == "P") {
   						$idavi_stat = idavi_addPosts($idavi_feed, $CyclePosts, $DayPosts, $feedid);
   						if ($idavi_stat > 0) {
   							$worked = true;
   							$CyclePosts += $idavi_stat;
	   						$DayPosts += $idavi_stat;
   						}
   					} else {
   						$idavi_stat = idavi_addTestimonials($idavi_feed, $CycleTestimonials, $DayTestimonials);
   						if ($idavi_stat > 0) {
   							$worked = true;
   							$CycleTestimonials += $idavi_stat;
	   						$DayTestimonials += $idavi_stat;
   						}
   					}
   					break;
   			}
   		}
   		
   		if (!$worked)
   			++$noworkcount;
   		else {
   			//set last date feed was worked
   			$idavi_opts['idavi_pages_lm'][$feedid] = date("m-d-Y H:i:m");
   			$idavi_opts['idavi_pages_tm'][$feedid] = time();
   		}
   			
   		$lastfeedworked = $feedid;
   		$feedid = idavi_nextfeed($feedid);
   		
   		//look for obvious reasons to exit the loop
   		//We reached the top of the list on a bottom up run
   		if ($feedid < 0 && $idavi_opts['idavi_feedpriority'] == "BottomUp")
   			break;
   			
		//we reached the bottom of the list on a top to bottom run
   		if ($feedid >= count($idavi_opts['idavi_pages_fct']) && $idavi_opts['idavi_feedpriority'] == "TopDown") 
   			break;
   			
   		//we reached the starting point on a cycle run
   		if ($feedid == $StartingFeed && $idavi_opts['idavi_feedpriority'] == "Cycle")
   			break;
   	
   	} while ((($CyclePosts <= $idavi_opts['idavi_maxcycleposts']) || ($CycleTestimonials <= $idavi_opts['idavi_maxcycletestimonials']) || ($DayPosts <= $idavi_opts['idavi_maxposts']) || ($DayTestimonials <= $idavi_opts['idavi_maxtestimonials'])) && $noworkcount < 10);

   	//save whatever changes we made to the options while running
   	update_option("iDavi", $idavi_opts);
   	
   	//save our state for the next run
   	update_option("idavi_lastworktype", $LastWorkType);
   	update_option("idavi_lastfeed", $lastfeedworked);
   	update_option("idavi_dayposts", $DayPosts);
   	update_option("idavi_daytestimonials", $DayTestimonials);
   	
   	// Log the last run time into our options
   	update_option("idavi_lastrun", time());
  	
}

function idavi_insert_comment($title, $commentBody, $author, $author_email, $author_url, $postmethod) {
   	global $wpdb;

   	$category = htmlspecialchars($category); 

   	$safeTitle = sanitize_title($title);
   	$safeTitle = htmlspecialchars($title);
   	$safeTitle = $wpdb->escape($safeTitle);

   	// Lookup post to make sure it exists
   	if (($pstid = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE post_title = '$safeTitle'"))) {
   	
   		//determine the comment posting method
   		if ($postmethod == "comments") {
   		
   			//comment is added as comment
   			//lookup to make sure comment has not already been entered
   			if ($wpdb->get_row("SELECT comment_ID FROM $wpdb->comments WHERE comment_post_id = '" . $pstid->ID . "' AND comment_content = '" . $commentBody . "'")) {
   				//comment exists already, return false
   				return false;
   			}
   		
   			$data = array(
    		'comment_post_ID' => $pstid->ID,
    		'comment_author' => $author,
    		'comment_author_email' => $author_email,
    		'comment_author_url' => $author_url,
    		'comment_content' => $commentBody,
	    	'comment_approved' => 1,
			);

			$commentid = wp_insert_comment($data);

   		} else {
   		
			$post_data = get_post($pstid->ID); 
			if (!strpos($post_data->post_content, $commentBody))
				{
				//not found, insert it at the bottom of the post
			
				$newpostcontent = $post_data->post_content . "<br><br>======= <b>T E S T I M O N I A L</b> =======<br><br>" . $commentBody . "<br><br>-&nbsp;" . $author . "<br>&nbsp;&nbsp;&nbsp;<a href=\"" . $author_url . "\">" . $author_url . "</a><br>";
				$my_post = array();
  				$my_post['ID'] = $pstid->ID;
  				$my_post['post_content'] = $newpostcontent;
   		
				// Update the post into the database
  				wp_update_post( $my_post );
			} else {
				//comment found, return false
				return false;
			}
   		}
		
   		return true;
   	} else {
      	return false;
   	}
}

function idavi_insert($title, $postBody, $categoryName, $AllowComments, $AllowPingbacks, $excerpt, $tags, $createcategory, $feedcategory) {
   	global $wpdb;
   	
   	$category = htmlspecialchars($category);
	
   	$safeTitle = sanitize_title($title);
   	$safeTitle = htmlspecialchars($title);
   	$safeTitle = $wpdb->escape($safeTitle);

   	// Avoid posting duplicates
   	if ($wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE post_title = '$safeTitle'")) {
      	return false;
   	}

	$category = $wpdb->get_var( "SELECT term_id FROM $wpdb->terms WHERE name = '$categoryName' LIMIT 1");

	if( $category <=0 ) 
	{
		if ($createcategory)
		{
			$my_cat = array('cat_name' => $category, 'category_description' => $category, 'category_nicename' => '', 'category_parent' => '');

			// Create the category
			$my_cat_id = wp_insert_category($my_cat);
		
		} else {
			$category = $feedcategory;
		}
		$category = $wpdb->get_var( "SELECT term_id FROM $wpdb->terms WHERE name = '$category' LIMIT 1");

		if( $category <=0 ) $category = array(1);
	}

	$category = array($category);	
	$post = array(
  		'comment_status' => $AllowComments,
  		'ping_status' => $AllowPingbacks, 
 		"post_title" => $title,
      	"post_content" => $postBody,
      	"post_category" => $category,
      	"post_status" => "publish",			
      	"post_author" => 1,
      	"post_date_gmt" => gmdate("Y-m-d H:i:s"),
    	'post_excerpt' => $excerpt,
  		'post_type' => 'post',
  		'tags_input' => $tags
	);
	
   	$postid = wp_insert_post($post);
   	
   	return true;
}

function idavi_adminPage()
{  
  	idavi_echoAdminPage(); 
   	idavi_echoFooter();
}

function idavi_loadOptions()
{
	global $idavi_opts;
	
	$idavi_opts = get_option("iDavi");
	
	if ($idavi_opts == "") {
		
			$defaultfeedurl = "http://www.idavi.com/feed";
		
		$idavi_opts['idavi_pages_ur']  = array($defaultfeedurl);
		$idavi_opts['idavi_pages_ky']  = array("Enter Keyword");
	 	$idavi_opts['idavi_pages_ct']  = array("uncategorized");
		$idavi_opts['idavi_pages_fct'] = array("uncategorized");
	 	$idavi_opts['idavi_pages_cf']  = array("86400");
		update_option("iDavi", $idavi_opts);
	}
}

function idavi_echoAdminPage()
{
	global $idavi_opts;
	
	if (isset($_POST["idavi_submit"]))
		{

	 	$post = get_magic_quotes_gpc() ? array_map('stripslashes_deep', $_POST ) : $_POST;
	 	
	 	$idavi_opts = "";
	 	$idavi_opts['idavi_linkback'] = "on";
		$idavi_opts['idavi_nickname'] = $post['idavi_nickname'];
	 	$idavi_opts['idavi_frequency'] = $post['idavi_frequency'];
	 	$idavi_opts['idavi_testimonials'] = $post['idavi_testimonials'];
	 	$idavi_opts['idavi_postformat'] = $post['idavi_postformat'];
	 	$idavi_opts['idavi_excerptformat'] = $post['idavi_excerptformat'];
	 	$idavi_opts['idavi_maxtestimonials'] = $post['idavi_maxtestimonials'];
	 	$idavi_opts['idavi_commentpriority'] = $post['idavi_commentpriority'];
	 	$idavi_opts['idavi_maxposts'] = $post['idavi_maxposts'];
	 	$idavi_opts['idavi_feedpriority'] = $post['idavi_feedpriority'];
	 	$idavi_opts['idavi_maxcycleposts'] = $post['idavi_maxcycleposts'];
	 	$idavi_opts['idavi_maxcycletestimonials'] = $post['idavi_maxcycletestimonials'];
	 	$idavi_opts['idavi_categoryoverride'] = $post['idavi_categoryoverride'];
	 	$idavi_opts['idavi_createcategory'] = $post['idavi_createcategory'];
	 	$idavi_opts['idavi_maxcyclefeedposts'] = $post['idavi_maxcyclefeedposts'];
	  	$idavi_opts['idavi_maxcyclefeedtestimonials'] = $post['idavi_maxcyclefeedtestimonials'];
	 	$idavi_opts['idavi_pingback'] = $post['idavi_pingback'];
	 	$idavi_opts['idavi_comments'] = $post['idavi_comments'];
	 	$idavi_opts['idavi_minimumtext'] = $post['idavi_minimumtext'];
	 	$idavi_opts['idavi_pages_ur']  = "http://www.idavi.com/feed";
		$idavi_opts['idavi_pages_ky']  = $_POST['idavi_pages_ky'];
	 	$idavi_opts['idavi_pages_ct']  = $_POST['idavi_pages_ct'];
		//var_export($idavi_opts['idavi_pages_ct']);
		$idavi_opts['idavi_pages_fct'] = $_POST['idavi_pages_fct'];
		
	 	$idavi_opts['idavi_pages_cf']  = $_POST['idavi_pages_cf'];
	 	$idavi_opts['idavi_pages_lm']  = $_POST['idavi_pages_lm'];
	 	
	 	update_option("iDavi", $idavi_opts);
        
		echo '<div class="updated">';
		echo 'Updated!';
		echo '</div>';
   	}
   	
  /* if (isset($_POST["idavi_register"]))
		{

		$modregister = new idavi_ModVersion;
		$success = $modregister->register($_REQUEST['Email'], MFOS_APP_NAME);
		if ($success == '1') {
			saveRegistration($_REQUEST['Email']);
			echo '<div class="updated">';
			echo 'Registered!';
			echo '</div>';
		} else {
			echo '<div class="updated">';
			echo 'UH OH! We had a problem registering.  Are you sure you registered your email <a href=\"http://www.plugins-for-wordpress.com/moneyfeed\">here</a>?';
			echo '</div>';
		}
   	}*/
	include "adminpage.inc.php";
   }
   

function idavi_echoHeader($VersionNumber)
{
   echo "<style type=\"text/css\" >";
   echo "h3 {
   			margin: 0;
   			} 
   			
   		.postbox .inside {
   			margin: 0 10px;
   			}
   			
   		.postbox {
   			max-width: 700px;
   		}
   		</style>";
   
  // echo "<br><img src=\"../wp-content/plugins/moneyfeed/images/moneyfeed.jpg\"><a href=\"http://www.plugins-for-wordpress.com/moneyfeed/documentation\" target=\"_blank\"><img src=\"../wp-content/plugins/moneyfeed/images/book_search.png\" align=\"right\" border=\"0\" alt=\"Documentation and Tutorials\"></a><a href=\"http://www.plugins-for-wordpress.com/moneyfeed/testimonial\" target=\"_blank\"><img src=\"../wp-content/plugins/moneyfeed/images/user_comment.png\" align=\"right\" border=\"0\" alt=\"Give Us a Testimonial\"></a>";
 	echo "<div style=\"float: right\">Ver " . MFOS_APP_VERSION . "</div>";
   //if ($VersionNumber != MFOS_APP_VERSION)
   		//echo "<div style=\"float: right\">There is a new version (V" . $VersionNumber . ") of Moneyfeed available at <a href=\"http://plugins-for-wordpress.com/moneyfeed\" target=\"_blank\">plugins-for-wordpress.com/moneyfeed</a>&nbsp;&nbsp</div>";
   //else
  // 		echo "<div style=\"float: right\"><iframe src=\"http://www.plugins-for-wordpress.com/pluginnews/news2.php\"></iframe></div><br><br><br>";
  
  // echo "<BR><BR>";
   
}
   
function idavi_echoFooter() {
   //echo "<br><br><center>iDavi is available at <a href=\"http://www.plugins-for-wordpress.com/moneyfeed\">plugins-for-wordpress/idavi</a>.  Check out our other plugins for wordpress at <a href=\"http://www.plugins-for-wordpress.com\">plugins-for-wordpress</a>.<br><br>Copyright &copy; 2010, <a href=\"http://www.geniusideastudio.com\">Genius Idea Studio, LLC</a>, All Rights Reserved</center><br><br>";

}

// Run the "idavi_run" function every hour to import RSS.
function idavi_activate() {
   wp_schedule_event(time(), 'hourly', 'idavi_hourly');
}

// Turn off the hourly event if we deactivate the plugin.
function idavi_deactivate() {
   wp_clear_scheduled_hook('idavi_hourly');
}

// Add the link to the settings page in the settings sub-header
function idavi_menu_setup() {
	global $wp_mail_ads;
	
   if (isset($_POST["run"])) {
      // Run the automator if we've hit the "Run Now" button...
      idavi_run(true);
	  //idavifeed_run(true);
	 
	
   }
 	add_menu_page('idavi Settings', 'iDavi', 8, "iDavi", 'idavi_adminPage');
	add_submenu_page("iDavi", 'iDavi: Feeds', 'Feeds', 8, 'iDavi', 'idavi_adminPage');
	add_submenu_page("iDavi", 'iDavi: Hide Categories', 'Hide Categories', 8, 'idavi-hidecategories', 'idavi_cat_options_page');
	add_submenu_page("iDavi", 'iDavi: Content Ads', 'Content Ads', 8, 'idavi-contentads', 'idaviads_options_page');
	add_submenu_page("iDavi", 'iDavi: Email Ads', 'Email Ads', 8, 'wp-mail-ads-settings', array (&$wp_mail_ads, 'mladsoptions_page' )); 
	//add_submenu_page("iDavi", 'iDavi: Mail Ads Options', 'Mail Ads', 8, "idavimail", 'mailadsoptions_page');
   //add_options_page('idavi Settings', 'Idavi Feed', 10, "Idavi", 'idavi_adminPage');
   //add_options_page('idavi Settings', 'Idavi Hide Category', 10, "idavicategoryadmin.php", '');
   //add_options_page('idavi Settings','Idavi Post Ads', 10, 'idaviadsadmin.php', 'idaviads_options_page');
	
    //add_options_page('idavi Settings', 'Manage Adds', 10, "idaviadsadmin.php", 'idaviads_option_menu');
}

function idavi_addpost($url,$feedid){ 
	global $idavi_opts;
	$actioncount = 0;
	$maxfeed = $idavi_opts['idavi_maxcyclefeedposts'];
	$maxcycle = $idavi_opts['idavi_maxcycleposts'];
	$maxday = $idavi_opts['idavi_maxposts'];
	$allowpings = ($idavi_opts['idavi_pingback']) ? 'open' : 'closed';
	$allowcomments = ($idavi_opts['idavi_comments']) ? 'open' : 'closed';
	
	if (trim($url) != "") 
	{
		$xmlStr = file_get_contents($url);
		//var_export($xmlStr);
		libxml_use_internal_errors(true);
		$xmlObj = simplexml_load_string($xmlStr);
		$prss = idavi_simpleXMLToArray($xmlObj,false);
		$cou = $maxfeed;
		for($c=0;$c<=$cou;$c++)
		{
			$items = $prss['item'][$c];
			//var_export($items); 
			$count=count($items);
			for($i=0;$i<$count;$i++){
				$title=$items['title']['@value'];
				$description=$items['description']['@value'];
				$link=$items['link']['@value'];
				$keywords=$items['keywords']['@value']; 
			}
			$excerpttext = $description;
			idavi_insert($title, $description, $idavi_opts['idavi_pages_ct'][$feedid], $allowcomments, $allowpings, $excerpttext, $keywords, $idavi_opts['idavi_createcategory'], $idavi_opts['idavi_pages_ct'][$feedid]);
		}
	}
}

?>
