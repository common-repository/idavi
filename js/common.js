 function wpInsertToggleNotSavedAlert() {
 document.getElementById("message").style.display = "block";
 jQuery("#message").animate({opacity: 1, height: "100%"}, 1500);
 }
 
 function wpInsertToggleAdWidget(sender, pointerid) {
  if(jQuery(sender).attr("value") == "Click to Activate") {
  jQuery(pointerid).attr("checked", true);
  jQuery(sender).animate({color: "red"}, 1500, "linear", function() { wpInsertToggleNotSavedAlert(); } );
  jQuery(sender).attr("value", "Click to Deactivate");
  }
  else {
  jQuery(pointerid).attr("checked", false);
  jQuery(sender).animate({color: "#2f9303"}, 1500, "linear", function() { wpInsertToggleNotSavedAlert(); } );
  jQuery(sender).attr("value", "Click to Activate");
  }
 }
 
 function SwitchAds(adBox0,adBox1,adBox2) {
	if(document.getElementById(adBox0).style.display == "block") {
		jQuery("#"+adBox0).animate({backgroundColor: "#FFFBCC"}, 500, "linear",  function() { jQuery("#"+adBox0).animate({backgroundColor: "#FFFFFF"}, 500, "linear"); } );
	}
	else {
		if(document.getElementById(adBox1).style.display == "block") {
			jQuery("#"+adBox1+"_button").animate({color: "red"}, 1500, "linear");
			jQuery("#"+adBox0+"_button").animate({color: "#2f9303"}, 1500, "linear");
			jQuery("#"+adBox1).animate({height: "0px"}, 500, "linear", function() { document.getElementById(adBox0).style.display = "block";jQuery("#"+adBox0).animate({height: "200px"}, 1000, "linear");document.getElementById(adBox1).style.display = "none"; } );
		}
		else {
			jQuery("#"+adBox2+"_button").animate({color: "red"}, 1500, "linear");
			jQuery("#"+adBox0+"_button").animate({color: "#2f9303"}, 1500, "linear");
			jQuery("#"+adBox2).animate({height: "0px"}, 500, "linear", function() { document.getElementById(adBox0).style.display = "block";jQuery("#"+adBox0).animate({height: "200px"}, 1000, "linear");document.getElementById(adBox2).style.display = "none"; } );
		}
	}
 }
 
 /* PopUps */
 
 //Code to find Y coordinates of any element starts here

function YPos(obj) {
    var curtop = 0;
    if (obj.offsetParent) {
        while (1) {
            curtop += obj.offsetTop;
            if (!obj.offsetParent)
                break;
            obj = obj.offsetParent;
        }
    }
    else if (obj.y) {
        curtop += obj.y;
    }
    return curtop;
}

//Code to find Y coordinates of any element ends here

/****************************************/

//Code to find X coordinates of any element starts here

function XPos(obj) {
    var curtop = 0;
    if (obj.offsetParent) {
        while (1) {
            curtop += obj.offsetLeft;
            if (!obj.offsetParent)
                break;
            obj = obj.offsetParent;
        }
    }
    else if (obj.x) {
        curtop += obj.x;
    }
    return curtop;
}

//Code to find X coordinates of any element ends here
 
 //Code to find page Height/Width starts here

function GetPageHeight() {
	if (window.innerHeight && window.scrollMaxY) {// Firefox
		yWithScroll = window.innerHeight + window.scrollMaxY;
		xWithScroll = window.innerWidth + window.scrollMaxX;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		yWithScroll = document.body.scrollHeight;
		xWithScroll = document.body.scrollWidth;
	} else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari
		yWithScroll = document.body.offsetHeight;
		xWithScroll = document.body.offsetWidth;
  	}
  	return yWithScroll;

}

function GetPageWidth() {
		var IE=document.all;
		var iXPos;
		if (!IE) {
			iXPos = self.innerWidth;
		}
		else {
			iXPos = (document.body.clientWidth);
		}
		return iXPos;
}

//Code to find page Height/Width starts here


 function vIE() {
    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) { //test for MSIE x.x;
        var ieversion = new Number(RegExp.jQuery1) // capture x.x portion and store as a number
        return ieversion;
    }
    else
        return 0; // this is for other browsers
}

function InitialisePopUP(width, height, ypos, xpos) {

    if (width > 749) { alert('Please Use a smaller value for the Popup (less than 749)'); }
    if (height > 540) { alert('Please Use a smaller value for the Popup (less than 540)'); }
    jQuery("#popUpMaster").draggable({
        containment: ['parent'],
        effect: ['fade', 'fade']
    });
    var top;
    var bottom;
    var left;
    var right;
    var contentHeight;
    var contentWidth;
    left = height - 40;
    right = left - 16;
    top = width - 24;
    bottom = top - 19;
    contentWidth = top;
    if (vIE()) {
        right = left - 16;
        contentHeight = height - 44;

        if (vIE() >= 7) {
            contentHeight = height - 46;
            right = right;
            bottom = bottom;
        }
    }
    else if (vIE() == 0) {
        contentHeight = height - 45;
        right = right;
        bottom = bottom;
    }
    jQuery("#popUpMaster").css('width', width);
    jQuery("#popUpMaster").css('height', height);
    jQuery("#popUpTop").css('width', top);
    jQuery("#popUpLft").css('height', left);
    jQuery("#popUpRgt").css('height', right);
    jQuery("#popUpBtm").css('width', bottom);
    jQuery("#content").css('width', contentWidth);
    jQuery("#content").css('height', contentHeight);

    jQuery("#popUpMaster").show();
    jQuery("#popUpMaster").css('top', ypos);
    jQuery("#popUpMaster").css('left', xpos);

    if (vIE() == 6) {
        jQuery('img[@srcjQuery=.png], div#popUpMaster').ifixpng();
    }
}

function GreyOutScreen() {
    var divGreyOut = document.createElement("div");
    divGreyOut.id = "GreyOut";
    divGreyOut.className = "GreyOutLayer";
    divGreyOut.style.visibility = "visible";
    divGreyOut.style.height = GetPageHeight().toString(10) + "px";
    document.body.appendChild(divGreyOut);
}

function CreatePopUp(headerText, width, height, top, left) {
    var divTemp = document.createElement("div");

    divTemp.style.padding = "0";
    divTemp.style.zIndex = "103";
    divTemp.style.position = "absolute";
    divTemp.id = "popUpMaster";

    var headingText = document.createElement("h4");
    headingText.innerHTML = headerText;

    var closeButton = document.createElement("a");
    closeButton.innerHTML = "&nbsp&nbsp&nbsp&nbsp";
    closeButton.href = "javascript:;";
    closeButton.className = "closePopUp";
    closeButton.onclick = ClosePopup;
    closeButton.id = "closeButton";

    var img1 = document.createElement("img");
    img1.src ="../wp-content/plugins/wp-insert/images/popUpTopLft.png";
    img1.id = "popUpTopLft";

    var img2 = document.createElement("img");
    img2.src = "../wp-content/plugins/wp-insert/images/popUpTop.png";
    img2.id = "popUpTop";

    var img3 = document.createElement("img");
    img3.src = "../wp-content/plugins/wp-insert/images/popUpTopRgt.png";
    img3.id = "popUpTopRgt";

    var img4 = document.createElement("img");
    img4.src = "../wp-content/plugins/wp-insert/images/popUpLft.png";
    img4.id = "popUpLft";

    var img5 = document.createElement("img");
    img5.src = "../wp-content/plugins/wp-insert/images/popUpRgt.png";
    img5.id = "popUpRgt";

    var img6 = document.createElement("img");
    img6.src = "../wp-content/plugins/wp-insert/images/popUpBtmLft.png";
    img6.id = "popUpBtmLft";

    var img7 = document.createElement("img");
    img7.src = "../wp-content/plugins/wp-insert/images/popUpBtmRgt.png";
    img7.id = "popUpBtmRgt";

    var img8 = document.createElement("img");
    img8.src = "../wp-content/plugins/wp-insert/images/popUpBtm.png";
    img8.id = "popUpBtm";

    var divContentArea = document.createElement("div");
    divContentArea.id = "content";

    divTemp.appendChild(headingText);
    divTemp.appendChild(closeButton);
    divTemp.appendChild(img1);
    divTemp.appendChild(img2);
    divTemp.appendChild(img3);
    divTemp.appendChild(img4);
    divTemp.appendChild(img5);
    divTemp.appendChild(img6);
    divTemp.appendChild(img7);
    divTemp.appendChild(img8);
    divTemp.appendChild(divContentArea);


    document.body.appendChild(divTemp);

    try {
        InitialisePopUP(width, height, top, left);
    }
    catch (err) {
    }
    return divContentArea; ;

}

function ClosePopup() {
    if (document.getElementById("popUpMaster")) {
        document.body.removeChild(document.getElementById("popUpMaster"));
    }
    if (document.getElementById("GreyOut")) {
        document.body.removeChild(document.getElementById("GreyOut"));
    }
}
 
 /* End PopUps */