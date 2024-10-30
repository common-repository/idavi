

function sm_addFeed(keyword,category,fcategory,changeFreq,lastChanged) { 

	var table = document.getElementById('sm_pageTable').getElementsByTagName('TBODY')[0];
	var ce = function(ele) { return document.createElement(ele) };
	var tr = ce('TR');
												
	
	/*var iUrl = ce('INPUT');
	iUrl.type="text";
	iUrl.style.width='5%';
	iUrl.style.display='none';
	iUrl.name="mf_pages_ur[]";
	if(url) iUrl.value=url;*/
	
	
	var td = ce('TD');
	var ikeyword = ce('INPUT');
	ikeyword.type="text";
	ikeyword.style.width='95%';
	ikeyword.name="idavi_pages_ky[]";
	if(keyword) ikeyword.value=keyword;
	td.appendChild(ikeyword);
	tr.appendChild(td);
	
	td = ce('TD');
	td.style.width='120px';
	var iCat = ce('SELECT');
	iCat.style.width='95%';
	iCat.name="idavi_pages_ct[]";
	for(var i=0; i <bcategories.length; i++) {
		var op = ce('OPTION');
		op.text = bcategories[i];		
		op.value = bcategories[i];
		try {
			iCat.add(op, null); // standards compliant; doesn't work in IE
		} catch(ex) {
			iCat.add(op); // IE only
		}
		if(category && category == op.value) {
			iCat.selectedIndex = i;
		}
	}
	td.appendChild(iCat);
	tr.appendChild(td);
	
	td = ce('TD');
	td.style.width='120px';
	var iCat = ce('SELECT');
	iCat.style.width='95%';
	iCat.name="idavi_pages_fct[]";
	for(var i=0; i <fcatval.length; i++) {
		var op = ce('OPTION');
		op.text = fcatname[i];		
		op.value = fcatval[i];
		try {
			iCat.add(op, null); // standards compliant; doesn't work in IE
		} catch(ex) {
			iCat.add(op); // IE only
		}
		if(fcategory && fcategory == op.value) {
			iCat.selectedIndex = i;
		}
	}
	td.appendChild(iCat);
	tr.appendChild(td);
	
	td = ce('TD');
	td.style.width='120px';
	var iFreq = ce('SELECT');
	iFreq.name="idavi_pages_cf[]";
	iFreq.style.width='95%';
	for(var i=0; i<changeFreqVals.length; i++) {
		var op = ce('OPTION');
		op.text = changeFreqNames[i];		
		op.value = changeFreqVals[i];
		try {
			iFreq.add(op, null); // standards compliant; doesn't work in IE
		} catch(ex) {
			iFreq.add(op); // IE only
		}
		
		if(changeFreq && changeFreq == op.value) {
			iFreq.selectedIndex = i;
		}
	}
	td.appendChild(iFreq);
	tr.appendChild(td);
	
	var td = ce('TD');
	td.style.width='110px';
	var iChanged = ce('INPUT');
	iChanged.type="text";
	iChanged.name="idavi_pages_lm[]";
	iChanged.style.width='95%';
	if(lastChanged) iChanged.value=lastChanged;
	td.appendChild(iChanged);
	tr.appendChild(td);
	
	var td = ce('TD');
	td.style.textAlign="center";
	td.style.width='5px';
	var iAction = ce('A');
	iAction.innerHTML = 'X';
	iAction.href="javascript:void(0);"
	iAction.onclick = function() { table.removeChild(tr); };
	td.appendChild(iAction);
	tr.appendChild(td);
	
	var mark = ce('INPUT');
	mark.type="hidden";
	mark.name="idavi_pages_mark[]"; 
	mark.value="true";
	tr.appendChild(mark);
	
	
	var firstRow = table.getElementsByTagName('TR')[1];
	if(firstRow) {
		var firstCol = (firstRow.childNodes[1]?firstRow.childNodes[1]:firstRow.childNodes[0]);
		if(firstCol.colSpan>1) {
			firstRow.parentNode.removeChild(firstRow);
		}
	}
	var cnt = table.getElementsByTagName('TR').length;
	if(cnt%2) tr.className="alternate";
	
	table.appendChild(tr);										
}

function sm_loadFeeds() {
	for(var i=0; i<pages.length; i++) { 
		sm_addFeed(pages[i].keyword,pages[i].category,pages[i].fcategory,pages[i].changeFreq,pages[i].lastChanged);
	}
}