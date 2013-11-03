// SpryAutoSuggest.js - version 0.91 - Spry Pre-Release 1.6.1
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

var Spry;if(!Spry)Spry={};if(!Spry.Widget)Spry.Widget={};Spry.Widget.BrowserSniff=function(){var b=navigator.appName.toString();var a=navigator.platform.toString();var c=navigator.userAgent.toString();this.mozilla=this.ie=this.opera=this.safari=false;var d=/Opera.([0-9\.]*)/i;var e=/MSIE.([0-9\.]*)/i;var f=/gecko/i;var g=/(applewebkit|safari)\/([\d\.]*)/i;var r=false;if((r=c.match(d))){this.opera=true;this.version=parseFloat(r[1])}else if((r=c.match(e))){this.ie=true;this.version=parseFloat(r[1])}else if((r=c.match(g))){this.safari=true;this.version=parseFloat(r[2])}else if(c.match(f)){var h=/rv:\s*([0-9\.]+)/i;r=c.match(h);this.mozilla=true;this.version=parseFloat(r[1])}this.windows=this.mac=this.linux=false;this.Platform=c.match(/windows/i)?"windows":(c.match(/linux/i)?"linux":(c.match(/mac/i)?"mac":c.match(/unix/i)?"unix":"unknown"));this[this.Platform]=true;this.v=this.version;if(this.safari&&this.mac&&this.mozilla){this.mozilla=false}};Spry.is=new Spry.Widget.BrowserSniff();Spry.Widget.AutoSuggest=function(a,b,c,d,e){if(!this.isBrowserSupported())return;e=e||{};this.init(a,b,c,d);Spry.Widget.Utils.setOptions(this,e);if(Spry.Widget.AutoSuggest.onloadDidFire)this.attachBehaviors();else Spry.Widget.AutoSuggest.loadQueue.push(this);this.dataset.addObserver(this);var f=Spry.Widget.Utils.getElementID(b);var g=this;this._notifyDataset={onPostUpdate:function(){g.attachClickBehaviors()},onPreUpdate:function(){g.removeClickBehaviours()}};Spry.Data.Region.addObserver(f,this._notifyDataset);Spry.Widget.Utils.addEventListener(window,'unload',function(){g.destroy()},false);this.attachClickBehaviors();this.handleKeyUp(null);this.showSuggestions(false)};Spry.Widget.AutoSuggest.prototype.init=function(a,b,c,d){this.region=Spry.Widget.Utils.getElement(a);if(!this.region)return;this.minCharsType=false;this.containsString=false;this.loadFromServer=false;this.urlParam='';this.suggestionIsVisible=false;this.stopFocus=false;this.hasFocus=false;this.showSuggestClass='showSuggestClass';this.hideSuggestClass='hideSuggestClass';this.hoverSuggestClass='hoverSuggestClass';this.movePrevKeyCode=Spry.Widget.AutoSuggest.KEY_UP;this.moveNextKeyCode=Spry.Widget.AutoSuggest.KEY_DOWN;this.textElement=Spry.Widget.Utils.getFirstChildWithNodeNameAtAnyLevel(this.region,"INPUT");this.textElement.setAttribute('AutoComplete','off');this.suggestRegion=Spry.Widget.Utils.getElement(b);Spry.Widget.Utils.makePositioned(this.suggestRegion);Spry.Widget.Utils.addClassName(this.suggestRegion,this.hideSuggestClass);this.timerID=null;if(typeof c=="string"){this.dataset=window[c]}else{this.dataset=c}this.field=d;if(typeof d=='string'&&d.indexOf(',')!=-1){d=d.replace(/\s*,\s*/ig,',');this.field=d.split(',')}};Spry.Widget.AutoSuggest.prototype.isBrowserSupported=function(){return Spry.is.ie&&Spry.is.v>=5&&Spry.is.windows||Spry.is.mozilla&&Spry.is.v>=1.4||Spry.is.safari||Spry.is.opera&&Spry.is.v>=9};Spry.Widget.AutoSuggest.prototype.getValue=function(){if(!this.textElement)return'';return this.textElement.value};Spry.Widget.AutoSuggest.prototype.setValue=function(a){if(!this.textElement)return;this.textElement.value=a;this.showSuggestions(false)};Spry.Widget.AutoSuggest.prototype.focus=function(){if(!this.textElement)return;this.textElement.focus()};Spry.Widget.AutoSuggest.prototype.showSuggestions=function(a){if(this.region&&this.isVisibleSuggestion()!=a){if(a&&this.hasFocus){Spry.Widget.Utils.addClassName(this.region,this.showSuggestClass);if(Spry.is.ie&&Spry.is.version<7)this.createIframeLayer(this.suggestRegion)}else{if(Spry.is.ie&&Spry.is.version<7)this.removeIframeLayer();Spry.Widget.Utils.removeClassName(this.region,this.showSuggestClass)}}this.suggestionIsVisible=Spry.Widget.Utils.hasClassName(this.region,this.showSuggestClass)};Spry.Widget.AutoSuggest.prototype.isVisibleSuggestion=function(){return this.suggestionIsVisible};Spry.Widget.AutoSuggest.prototype.onDataChanged=function(a){var b=a.getData(true);var c=this.getValue();this.showSuggestions(b&&(!this.minCharsType||c.length>=this.minCharsType)&&(b.length>1||(b.length==1&&this.childs[0]&&this.childs[0].attributes.getNamedItem("spry:suggest").value!=this.getValue())))};Spry.Widget.AutoSuggest.prototype.nodeMouseOver=function(e,a){var l=this.childs.length;for(var i=0;i<l;i++)if(this.childs[i]!=a&&Spry.Widget.Utils.hasClassName(this.childs[i],this.hoverSuggestClass)){Spry.Widget.Utils.removeClassName(this.childs[i],this.hoverSuggestClass);break}};Spry.Widget.AutoSuggest.prototype.nodeClick=function(e,a){if(a)this.setValue(a)};Spry.Widget.AutoSuggest.prototype.handleKeyUp=function(e){if(this.timerID){clearTimeout(this.timerID);this.timerID=null}if(e&&this.isSpecialKey(e)){this.handleSpecialKeys(e);return}var a=this;var b=function(){a.timerID=null;a.loadDataSet()};if(!this.loadFromServer)b=function(){a.timerID=null;a.filterDataSet()};this.timerID=setTimeout(b,200)};Spry.Widget.AutoSuggest.prototype.scrollVisible=function(a){if(typeof this.scrolParent=='undefined'){var b=a;this.scrolParent=false;while(!this.scrolParent){var c=Spry.Widget.Utils.getStyleProp(b,'overflow');if(!c||c.toLowerCase()=='scroll'){this.scrolParent=b;break}if(b==this.region)break;b=b.parentNode}}if(this.scrolParent!=false){var h=parseInt(Spry.Widget.Utils.getStyleProp(this.scrolParent,'height'),10);if(a.offsetTop<this.scrolParent.scrollTop)this.scrolParent.scrollTop=a.offsetTop;else if(a.offsetTop+a.offsetHeight>this.scrolParent.scrollTop+h){this.scrolParent.scrollTop=a.offsetTop+a.offsetHeight-h+5;if(this.scrolParent.scrollTop<0)this.scrolParent.scrollTop=0}}};Spry.Widget.AutoSuggest.KEY_UP=38;Spry.Widget.AutoSuggest.KEY_DOWN=40;Spry.Widget.AutoSuggest.prototype.handleSpecialKeys=function(e){switch(e.keyCode){case this.moveNextKeyCode:case this.movePrevKeyCode:if(!(this.childs.length>0)||!this.getValue())return;var a=this.childs.length-1;var b=false;var c=false;var d=this.dataset.getData();if(this.childs.length>1||(d&&d.length==1&&this.childs[0]&&this.childs[0].attributes.getNamedItem('spry:suggest').value!=this.getValue())){this.showSuggestions(true)}else return;var f=Spry.Widget.Utils;for(var k=0;k<this.childs.length;k++){if(b){f.addClassName(this.childs[k],this.hoverSuggestClass);this.scrollVisible(this.childs[k]);break}if(f.hasClassName(this.childs[k],this.hoverSuggestClass)){f.removeClassName(this.childs[k],this.hoverSuggestClass);c=true;if(e.keyCode==this.moveNextKeyCode){b=true;continue}else{f.addClassName(this.childs[a],this.hoverSuggestClass);this.scrollVisible(this.childs[a]);break}}a=k}if(!c||(b&&k==this.childs.length)){f.addClassName(this.childs[0],this.hoverSuggestClass);this.scrollVisible(this.childs[0])}f.stopEvent(e);break;case 27:this.showSuggestions(false);break;case 13:if(!this.isVisibleSuggestion())return;for(var k=0;k<this.childs.length;k++)if(Spry.Widget.Utils.hasClassName(this.childs[k],this.hoverSuggestClass)){var g=this.childs[k].attributes.getNamedItem('spry:suggest');if(g){this.setValue(g.value);this.handleKeyUp(null)}Spry.Widget.Utils.stopEvent(e);return false}break;case 9:this.showSuggestions(false)}return};Spry.Widget.AutoSuggest.prototype.filterDataSet=function(){var e=this.containsString;var f=this.field;var g=this.getValue();if(this.previousString&&this.previousString==g)return;this.previousString=g;if(!g||(this.minCharsType&&this.minCharsType>g.length)){this.dataset.filter(function(a,b,c){return null});this.showSuggestions(false);return}var h=Spry.Widget.Utils.escapeRegExp(g);if(!e)h="^"+h;var j=new RegExp(h,"ig");if(this.maxListItems>0)this.dataset.maxItems=this.maxListItems;var k=function(a,b,c){if(a.maxItems>0&&a.maxItems<=a.data.length)return null;if(typeof f=='object'){var l=f.length;for(var i=0;i<l;i++){var d=b[f[i]];if(d&&d.search(j)!=-1)return b}}else{var d=b[f];if(d&&d.search(j)!=-1)return b}return null};this.dataset.filter(k);var m=this.dataset.getData();this.showSuggestions(m&&(!this.minCharsType||g.length>=this.minCharsType)&&(m.length>1||(m.length==1&&this.childs[0]&&this.childs[0].attributes.getNamedItem('spry:suggest').value!=g)))};Spry.Widget.AutoSuggest.prototype.loadDataSet=function(){var a=this.getValue();var b=this.dataset;b.cancelLoadData();b.useCache=false;if(!a||(this.minCharsType&&this.minCharsType>a.length)){this.showSuggestions(false);return}if(this.previousString&&this.previousString==a){var c=b.getData();this.showSuggestions(c&&(c.length>1||(c.length==1&&this.childs[0].attributes.getNamedItem("spry:suggest").value!=a)));return}this.previousString=a;var d=Spry.Widget.Utils.addReplaceParam(b.url,this.urlParam,a);b.setURL(d);b.loadData()};Spry.Widget.AutoSuggest.prototype.addMouseListener=function(a,b){var c=this;var d=Spry.Widget.Utils.addEventListener;d(a,"click",function(e){return c.nodeClick(e,b);c.handleKeyUp(null)},false);d(a,"mouseover",function(e){Spry.Widget.Utils.addClassName(a,c.hoverSuggestClass);c.nodeMouseOver(e,a)},false);d(a,"mouseout",function(e){Spry.Widget.Utils.removeClassName(a,c.hoverSuggestClass);c.nodeMouseOver(e,a)},false)};Spry.Widget.AutoSuggest.prototype.removeMouseListener=function(a,b){var c=this;var d=Spry.Widget.Utils.removeEventListener;d(a,"click",function(e){c.nodeClick(e,b);c.handleKeyUp(null)},false);d(a,"mouseover",function(e){Spry.Widget.Utils.addClassName(a,c.hoverSuggestClass);c.nodeMouseOver(e,a)},false);d(a,"mouseout",function(e){Spry.Widget.Utils.removeClassName(a,c.hoverSuggestClass);c.nodeMouseOver(e,a)},false)};Spry.Widget.AutoSuggest.prototype.attachClickBehaviors=function(){var c=this;var d=Spry.Utils.getNodesByFunc(this.region,function(a){if(a.nodeType==1){var b=a.attributes.getNamedItem("spry:suggest");if(b){c.addMouseListener(a,b.value);return true}}return false});this.childs=d};Spry.Widget.AutoSuggest.prototype.removeClickBehaviours=function(){var c=this;var d=Spry.Utils.getNodesByFunc(this.region,function(a){if(a.nodeType==1){var b=a.attributes.getNamedItem("spry:suggest");if(b){c.removeMouseListener(a,b.value);return true}}return false})};Spry.Widget.AutoSuggest.prototype.destroy=function(){this.removeClickBehaviours();Spry.Data.Region.removeObserver(Spry.Widget.Utils.getElementID(this.suggestRegion),this._notifyDataset);if(this.event_handlers)for(var i=0;i<this.event_handlers.length;i++){Spry.Widget.Utils.removeEventListener(this.event_handlers[i][0],this.event_handlers[i][1],this.event_handlers[i][2],false)}for(var k in this){if(typeof this[k]!='function'){try{delete this[k]}catch(err){}}}};Spry.Widget.AutoSuggest.onloadDidFire=false;Spry.Widget.AutoSuggest.loadQueue=[];Spry.Widget.AutoSuggest.processLoadQueue=function(a){Spry.Widget.AutoSuggest.onloadDidFire=true;var q=Spry.Widget.AutoSuggest.loadQueue;var b=q.length;for(var i=0;i<b;i++)q[i].attachBehaviors()};Spry.Widget.AutoSuggest.addLoadListener=function(a){if(typeof window.addEventListener!='undefined')window.addEventListener('load',a,false);else if(typeof document.addEventListener!='undefined')document.addEventListener('load',a,false);else if(typeof window.attachEvent!='undefined')window.attachEvent('onload',a)};Spry.Widget.AutoSuggest.addLoadListener(Spry.Widget.AutoSuggest.processLoadQueue);Spry.Widget.AutoSuggest.prototype.attachBehaviors=function(){this.event_handlers=[];var a=this;var b=function(e){a.handleKeyUp(e)};this.event_handlers.push([this.textElement,"keydown",b]);this.event_handlers.push([this.textElement,"focus",function(e){if(a.stopFocus){a.handleKeyUp(e)}a.hasFocus=true;a.stopFocus=false}]);this.event_handlers.push([this.textElement,"drop",b]);this.event_handlers.push([this.textElement,"dragdrop",b]);var c=false;if(Spry.is.opera){c=function(e){setTimeout(function(){if(!a.clickInList){a.showSuggestions(false)}else{a.stopFocus=true;a.textElement.focus()}a.clickInList=false;a.hasFocus=false},100)}}else{c=function(e){if(!a.clickInList){a.showSuggestions(false)}else{a.stopFocus=true;a.textElement.focus()}a.clickInList=false;a.hasFocus=false}}this.event_handlers.push([this.textElement,"blur",c]);this.event_handlers.push([this.suggestRegion,"mousedown",function(e){a.clickInList=true}]);for(var i=0;i<this.event_handlers.length;i++)Spry.Widget.Utils.addEventListener(this.event_handlers[i][0],this.event_handlers[i][1],this.event_handlers[i][2],false)};Spry.Widget.AutoSuggest.prototype.createIframeLayer=function(a){if(typeof this.iframeLayer=='undefined'){var b=document.createElement('iframe');b.tabIndex='-1';b.src='javascript:"";';b.scrolling='no';b.frameBorder='0';b.className='iframeSuggest';a.parentNode.appendChild(b);this.iframeLayer=b}this.iframeLayer.style.left=a.offsetLeft+'px';this.iframeLayer.style.top=a.offsetTop+'px';this.iframeLayer.style.width=a.offsetWidth+'px';this.iframeLayer.style.height=a.offsetHeight+'px';this.iframeLayer.style.display='block'};Spry.Widget.AutoSuggest.prototype.removeIframeLayer=function(){if(this.iframeLayer)this.iframeLayer.style.display='none'};if(!Spry.Widget.Utils)Spry.Widget.Utils={};Spry.Widget.Utils.specialSafariNavKeys=",63232,63233,63234,63235,63272,63273,63275,63276,63277,63289,";Spry.Widget.Utils.specialCharacters=",9,13,27,38,40,";Spry.Widget.Utils.specialCharacters+=",33,34,35,36,37,39,45,46,";Spry.Widget.Utils.specialCharacters+=",16,17,18,19,20,144,145,";Spry.Widget.Utils.specialCharacters+=",112,113,114,115,116,117,118,119,120,121,122,123,";Spry.Widget.Utils.specialCharacters+=Spry.Widget.Utils.specialSafariNavKeys;Spry.Widget.AutoSuggest.prototype.isSpecialKey=function(a){return Spry.Widget.Utils.specialCharacters.indexOf(","+a.keyCode+",")!=-1||this.moveNextKeyCode==a.keyCode||this.movePrevKeyCode==a.keyCode};Spry.Widget.Utils.getElementID=function(a){if(typeof a=='string'&&a)return a;return a.getAttribute('id')};Spry.Widget.Utils.getElement=function(a){if(a&&typeof a=="string")return document.getElementById(a);return a};Spry.Widget.Utils.addReplaceParam=function(a,b,c){var d='';var e='';var i=a.indexOf('?');if(i!=-1){d=a.slice(0,i);e=a.slice(i+1)}else d=a;e=e.replace('?','');var f=e.split("&");if(b.lastIndexOf('/')!=-1)b=b.slice(b.lastIndexOf('/')+1);for(i=0;i<f.length;i++){var k=f[i].split('=');if((k[0]&&k[0]==decodeURI(b))||f[i]==decodeURI(b))f[i]=null}f[f.length]=encodeURIComponent(b)+'='+encodeURIComponent(c);e='';for(i=0;i<f.length;i++)if(f[i])e+='&'+f[i];e=e.slice(1);a=d+'?'+e;return a};Spry.Widget.Utils.addClassName=function(a,b){if(!a)return;if(!a.className)a.className='';if(!a||a.className.search(new RegExp("\\b"+b+"\\b"))!=-1)return;a.className+=' '+b};Spry.Widget.Utils.removeClassName=function(a,b){if(!a)return;if(!a.className){a.className='';return}a.className=a.className.replace(new RegExp("\\s*\\b"+b+"\\b","g"),'')};Spry.Widget.Utils.hasClassName=function(a,b){if(!a||!b)return false;if(!a.className)a.className='';return a.className.search(new RegExp("\\s*\\b"+b+"\\b"))!=-1};Spry.Widget.Utils.addEventListener=function(a,b,c,d){try{if(a.addEventListener)a.addEventListener(b,c,d);else if(a.attachEvent)a.attachEvent("on"+b,c,d)}catch(e){}};Spry.Widget.Utils.removeEventListener=function(a,b,c,d){try{if(a.removeEventListener)a.removeEventListener(b,c,d);else if(a.detachEvent)a.detachEvent("on"+b,c,d)}catch(e){}};Spry.Widget.Utils.stopEvent=function(a){a.cancelBubble=true;a.returnValue=false;try{this.stopPropagation(a)}catch(e){}try{this.preventDefault(a)}catch(e){}};Spry.Widget.Utils.stopPropagation=function(a){if(a.stopPropagation)a.stopPropagation();else a.cancelBubble=true};Spry.Widget.Utils.preventDefault=function(a){if(a.preventDefault)a.preventDefault();else a.returnValue=false};Spry.Widget.Utils.setOptions=function(a,b,c){if(!b)return;for(var d in b){if(typeof c!='undefined'&&c&&typeof b[d]=='undefined')continue;a[d]=b[d]}};Spry.Widget.Utils.firstValid=function(){var a=null;for(var i=0;i<Spry.Widget.Utils.firstValid.arguments.length;i++)if(typeof Spry.Widget.Utils.firstValid.arguments[i]!='undefined'){a=Spry.Widget.Utils.firstValid.arguments[i];break}return a};Spry.Widget.Utils.camelize=function(a){var b=a.split('-');var c=true;var d='';for(var i=0;i<b.length;i++){if(b[i].length>0){if(c){d=b[i];c=false}else{var s=b[i];d+=s.charAt(0).toUpperCase()+s.substring(1)}}}return d};Spry.Widget.Utils.getStyleProp=function(a,b){var c;var d=Spry.Widget.Utils.camelize(b);try{c=a.style[d];if(!c){if(document.defaultView&&document.defaultView.getComputedStyle){var f=document.defaultView.getComputedStyle(a,null);c=f?f.getPropertyValue(b):null}else if(a.currentStyle)c=a.currentStyle[d]}}catch(e){}return c=='auto'?null:c};Spry.Widget.Utils.makePositioned=function(a){var b=Spry.Widget.Utils.getStyleProp(a,'position');if(!b||b=='static'){a.style.position='absolute';if(window.opera){a.style.top=0;a.style.left=0}}};Spry.Widget.Utils.escapeRegExp=function(a){return a.replace(/([\.\/\]\[\{\}\(\)\\\$\^\?\*\|\!\=\+\-])/g,'\\$1')};Spry.Widget.Utils.getFirstChildWithNodeNameAtAnyLevel=function(a,b){var c=a.getElementsByTagName(b);if(c)return c[0];return null};