<?php
/*
LICENSE: See "license.php" located at the root installation

This is a modified version of the SpryPagedView file, for use to display the list of users.
*/

//Header functions
	require_once('../../server/index.php');
	
//Export data to XML
	header("Content-type: text/javascript");
?>
// SpryPagedView.js - version 0.7 - Spry Pre-Release 1.6.1
//
// Copyright (c) 2006. Adobe Systems Incorporated.
// All rights reserved.
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//
//   * Redistributions of source code must retain the above copyright notice,
//     this list of conditions and the following disclaimer.
//   * Redistributions in binary form must reproduce the above copyright notice,
//     this list of conditions and the following disclaimer in the documentation
//     and/or other materials provided with the distribution.
//   * Neither the name of Adobe Systems Incorporated nor the names of its
//     contributors may be used to endorse or promote products derived from this
//     software without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
// ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
// LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
// CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
// SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
// INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
// CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
// POSSIBILITY OF SUCH DAMAGE.

eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('b 4;a(!4)4={};a(!4.5)4.5={};4.5.6=8(h,1D){4.5.D.O(3);3.h=h;3.9=10;3.z=0;3.1s=1j;3.w=0;3.15=1j;3.1p=1j;4.1t.1z(3,1D);3.p=1;a(!3.15)3.p=0;3.Y=3.z+3.9;3.h.1d(3);3.12();a(3.9>0)3.11(3.16())};4.5.6.7=L 4.5.D();4.5.6.7.1r=4.5.6;4.5.6.7.17=8(B){a(3.h)3.h.17(B)};4.5.6.7.14=8(q){a(3.h)3.h.14(q)};4.5.6.7.1v=8(l,t){a(!l)d;a(1R l=="1Q")l=[l,"u"];Q a(l.m<2&&l[0]!="u")l.1h("u");a(!t)t="1q";a(t=="1q"){a(3.18.m>0&&3.18[0]==l[0]&&3.1G=="1A")t="1O";Q t="1A"}b 19={1P:3.18,1S:3.1G,1T:l,1V:t};3.G("1U",19);3.1W();4.5.D.7.1v.O(3,l,t);3.E();3.1H();3.1N();3.G("1m",19)};4.5.6.7.F=8(){a(!3.h||3.h.20())d;a(!3.h.1l()){3.h.F();d}4.5.D.7.F.O(3)};4.5.6.7.N=8(P,r){3.H(0);3.12()};4.5.6.7.1L=8(P,r){b s=3;28(8(){s.G("1L",r)},0)};4.5.6.7.1m=4.5.6.7.N;4.5.6.7.E=8(){b f=3.C(y);a(!f||f.m<1)d;b e=f.m;b 9=(3.9>0)?3.9:e;b v=1;b j=v+9-1;j=(j<v)?v:(j>e?e:j);b o=1;b 1w=I((e+9-1)/9);b Z=1B.1K(e,9);1y(b i=0;i<e;i++){R=i+1;a(R>j){v=R;j=v+3.9-1;j=(j>e)?e:j;Z=1B.1K(j-v+1,9);++o}b c=f[i];a(c){c.1i=o;c.T=3.9;c.2a=i;c.2d=R;c.1c=v;c.1e=j;c.1k=Z;c.1f=1w;c.1n=e}}};4.5.6.7.12=8(){a(!3.h||!3.h.1l())d;3.G("1J");3.1M=W;3.r=[];3.U={};b f=3.h.C();a(f){b e=f.m;1y(b i=0;i<e;i++){b c=f[i];b g=L 1o();4.1t.1z(g,c);3.r.1h(g);3.U[g.u]=g}a(e>0)3.1a=f[0].u;3.E()}3.F()};4.5.6.7.16=8(){b s=3;d 8(h,c,q){a(q<s.z||q>=s.Y)d W;d c}};4.5.6.7.H=8(k){b e=3.C(y).m;3.w=(k<0)?0:k;a(3.1s&&k>(e-3.9))k=e-3.9;a(k<0)k=0;3.z=k;3.Y=k+3.9};4.5.6.7.1b=8(k){a(3.9<1)d;3.H(k);b f=3.C(y);a(f&&f.m&&f[3.w])3.1a=f[3.w].u;a(3.1p)3.h.17(3.1a);3.11(3.16())};4.5.6.7.S=8(){d I((3.C(y).m+3.9-1)/3.9)};4.5.6.7.V=8(){d I((((3.w!=3.z)?3.w:3.z)+3.9)/3.9)-3.p};4.5.6.7.x=8(o){o=I(o);b 1g=3.S();a((o+3.p)<1||(o+3.p)>1g)d;b 1I=(o-1+3.p)*3.9;3.1b(1I)};4.5.6.7.2b=8(B){3.13(3.1E(3.1F(B),y))};4.5.6.7.13=8(q){3.x(3.J(q))};4.5.6.7.2c=8(M){3.13(M-1)};4.5.6.7.1H=8(){3.x(1-3.p)};4.5.6.7.29=8(){3.x(3.S()-3.p)};4.5.6.7.1X=8(){3.x(3.V()-1)};4.5.6.7.26=8(){3.x(3.V()+1)};4.5.6.7.27=8(B){d 3.J(3.1E(3.1F(B),y))};4.5.6.7.J=8(q){d I(q/3.9)+1-3.p};4.5.6.7.1Y=8(M){d 3.J(M-1)};4.5.6.7.21=8(){d 3.9};4.5.6.7.22=8(9){a(3.9==9)d;a(9<1){3.9=0;3.H(0);3.E();3.11(W)}Q a(3.9<1){3.9=9;3.H(0);3.E();3.1b(3.z)}Q{3.9=9;3.E();3.x(3.J(3.w))}};4.5.6.7.24=8(){d L 4.5.6.n(3)};4.5.6.n=8(K){4.5.D.O(3);3.K=K;K.1d(3)};4.5.6.n.7=L 4.5.D();4.5.6.n.7.1r=4.5.6.n;4.5.6.n.7.N=8(P,r){3.1C()};4.5.6.n.7.1m=4.5.6.n.7.N;4.5.6.n.7.1C=8(){b A=3.K;a(!A||!A.1l())d;3.G("1J");3.1M=W;3.r=[];3.U={};b f=A.C(y);a(f){b e=f.m;b 1g=A.S();b i=0;b 1x=0;23(i<e){b c=f[i];b g=L 1o();g.u=1x++;3.r.1h(g);3.U[g.u]=g;g.1i=c.1i;g.T=c.T;g.1f=c.1f;g.1c=c.1c;g.1e=c.1e;g.1k=c.1k;g.1n=c.1n;i+=g.T}a(e>0){b s=3;b X=8(1u,P,r){a(1u!="25")d;s.1Z(X);s.14(A.V()-(A.15?0:1))};3.1d(X)}}3.F()};',62,138,'|||this|Spry|Data|PagedView|prototype|function|pageSize|if|var|row|return|numRows|rows|newRow|ds||lastItem|offset|columnNames|length|PagingInfo|pageNum|adjustmentValue|rowNumber|data|self|sortOrder|ds_RowID|firstItem|pageFirstItemOffset|goToPage|true|pageOffset|pv|rowID|getData|DataSet|updatePagerColumns|loadData|notifyObservers|setPageOffset|parseInt|getPageForRowNumber|pagedView|new|itemNumber|onDataChanged|call|notifier|else|itemIndex|getPageCount|ds_PageSize|dataHash|getCurrentPage|null|func|pageStop|pageItemCount||filter|preProcessData|goToPageContainingRowNumber|setCurrentRowNumber|useZeroBasedIndexes|getFilterFunc|setCurrentRow|lastSortColumns|nData|curRowID|filterDataSet|ds_PageFirstItemNumber|addObserver|ds_PageLastItemNumber|ds_PageCount|numPages|push|ds_PageNumber|false|ds_PageItemCount|getDataWasLoaded|onPostSort|ds_PageTotalItemCount|Object|setCurrentRowOnPageChange|toggle|constructor|forceFullPages|Utils|notificationType|sort|pageCount|id|for|setOptions|ascending|Math|extractInfo|options|getRowNumber|getRowByID|lastSortOrder|firstPage|newOffset|onPreLoad|min|onCurrentRowChanged|unfilteredData|enableNotifications|descending|oldSortColumns|string|typeof|oldSortOrder|newSortColumns|onPreSort|newSortOrder|disableNotifications|previousPage|getPageForItemNumber|removeObserver|getLoadDataRequestIsPending|getPageSize|setPageSize|while|getPagingInfo|onPostLoad|nextPage|getPageForRowID|setTimeout|lastPage|ds_PageItemRowNumber|goToPageContainingRowID|goToPageContainingItemNumber|ds_PageItemNumber'.split('|'),0,{}))

function filterData(searchByID, fieldID, containsID, dataSet) {
	var input = document.getElementById(fieldID);
	
	if (!input.value) {
		dataSet.filter(null);
		return;
	}

	var replacementString = input.value;
	
	if (!document.getElementById(containsID).checked) {
		replacementString = "^" + replacementString;
	}

	var replacement = new RegExp(replacementString, "i");
	var filterSet = document.getElementById(searchByID).value;
	
	var filter = function(dataInput, row, rowNumber) {
		var data = row[filterSet];
		
		if (data && data.search(replacement) != -1) {
			return row;
		}
		
		return null;
	};

	dataSet.filter(filter);
}

function startFilterTimer(searchByID, fieldID, containsID, dataSet) {
	if (startFilterTimer.timerID) {
		clearTimeout(startFilterTimer.timerID);
	}
	
	startFilterTimer.timerID = setTimeout(
		function() {
			startFilterTimer.timerID = null;
			filterData(searchByID, fieldID, containsID, dataSet);
		}
	, 100);
}

function toggleClass(object) {
	var link = document.getElementById(object);
	var currentClass = link.className;
	document.getElementsByClassName("ascending").className = "";
	document.getElementsByClassName("descending").className = "";
	
	if (currentClass == "") {
    	Spry.Utils.addClassName(object, "descending");
	} else if (currentClass == "descending") {
		Spry.Utils.addClassName(object, "ascending");
	} else {
		Spry.Utils.addClassName(object, "descending");
	}
}

function formatLine(region, lookupFunc) {
	if (lookupFunc("{pvUsers::organization}") == "None") {
		return "<span class=\"notAssigned\">" + lookupFunc("{pvUsers::organization}") + "</span>";
	} else {
		return "<a href=\"../organizations/profile.htm?id=" + lookupFunc("{pvUsers::organizationID}") + "\">" + lookupFunc("{pvUsers::organization}") + "</a>";
	}
}

function noDelete(region, lookupFunc) {
	if (lookupFunc("{pvUsers::id}") == <?php echo $userData['id']; ?>) {
		return "<span class=\"action noDelete\" onmouseover=\"Tip('You may not delete yourself')\" onmouseout=\"UnTip()\"></span>";
	} else {
		return "<a href=\"index.htm?action=delete&id=" + lookupFunc("{pvUsers::id}") + "\" class=\"action delete\" onmouseover=\"Tip('Delete <strong>" + lookupFunc("{pvUsers::firstName}") + " " + lookupFunc("{pvUsers::lastName}") + "</strong>')\" onmouseout=\"UnTip()\" onclick=\"return confirm('This action cannot be undone. Continue?');\"></a>";
	}
}

function email(region, lookupFunc) {
	if (lookupFunc("{pvOrganizations::email}") == "None") {
		return "<span class=\"notAssigned\">Awaiting Information</span>";
	} else {
		return "<a href=\"../communication/send_email.htm?type=organization&id=" + lookupFunc("{pvOrganizations::id}") + "&limit=billing\">" + lookupFunc("{pvOrganizations::email}") + "</a>";
	}
}

function phone(region, lookupFunc) {
	if (lookupFunc("{pvOrganizations::phone}") == "None") {
		return "<span class=\"notAssigned\">Awaiting Information</span>";
	} else {
		return lookupFunc("{pvOrganizations::phone}");
	}
}