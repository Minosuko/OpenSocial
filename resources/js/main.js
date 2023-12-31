/*! Z Template | (c) Daniel Sevcik | MIT License | https://github.com/webdevelopers-eu/z-template | build 2023-07-29T14:11:19+00:00 */window.zTemplate=function(){var _Mathmax=Math.max;function tokenize(a){const b=new Tokenizer(a);return b.tokenize(),b}function prepare(a,b){return new Preparator(a,b).prepare()}function zTemplate(a,b,c={}){const d=new Map(Object.entries(c||{})),e=new Map([...zTemplate.callbacks.entries(),...d.entries()]),f=new Template(a instanceof Document?a.documentElement:a);return f.render(b,e)}class Tokenizer extends Array{#operatorChars=["!","=","<",">","~","|","&"];#quoteChars=["'","\""];#blockChars={"{":"}","[":"]","(":")"};#hardSeparatorChars=[","];#softSeparatorChars=[" ","\t","\r","\n"];#input;#pointer=0;constructor(a){super(),this.#input=(a||"")+""}tokenize(){for(;!this.#endOfInput();){const a=this.#tokenizeUntil();if(null===a)break;this.push(a)}}#endOfInput(){return this.#pointer>=this.#input.length}#next(){return this.#endOfInput()?null:this.#input[this.#pointer++]}#prev(){return 0>=this.#pointer?null:this.#input[--this.#pointer]}#tokenizeUntil(a=","){const b=new Tokenizer(this.#input);let c={type:"generic",value:""},d=!1;for(let e=this.#next();null!==e;e=this.#next())if(d||"text"==c.type&&e!=c.delimiter)c.value+=e,d=!1;else if("\\"===e)d=!0;else if("text"==c.type&&e==c.delimiter)this.#pushSmart(b,c),c={type:"generic",value:""};else if(e===a)break;else e in this.#blockChars?(this.#pushSmart(b,c),b.push({type:"block",value:this.#tokenizeUntil(this.#blockChars[e]),start:e,end:this.#blockChars[e]}),c={type:"generic",value:""}):this.#quoteChars.includes(e)?(this.#pushSmart(b,c),c={type:"text",value:"",delimiter:e}):"generic"==c.type&&this.#hardSeparatorChars.includes(e)?(this.#pushSmart(b,c),this.#pushSmart(b,{type:"separator",value:e}),c={type:"generic",value:""}):"generic"==c.type&&this.#softSeparatorChars.includes(e)?(this.#pushSmart(b,c),c={type:"generic",value:""}):"operator"!==c.type&&this.#operatorChars.includes(e)?(this.#pushSmart(b,c),c={type:"operator",value:e}):"operator"!==c.type||this.#operatorChars.includes(e)?c.value+=e:(this.#pushSmart(b,c),c={type:"generic",value:""},this.#prev());return this.#pushSmart(b,c),b}#pushSmart(a,b){if("generic"==b.type){if(b.value=b.value.trim(),!b.value.length)return;isNaN(b.value)||(b.type="text",b.value=new Number(b.value))}a.push(b)}}class Preparator{#vars;#tokens;#paramShortcuts={"@*":"attr",":*":"event","**":"call",".*":"class","+":"html",".":"text","=":"value","?":"toggle","!":"remove","`":"debugger"};#operatorsCompare=["==","!=",">",">=","<","<="];#operatorsBoolean=["!","&&","||"];#data={negateValue:0,variable:null,value:null,valueBool:null,action:"",param:null,arguments:[],condition:null};constructor(a,b){if("object"!=typeof b)throw new Error(`The variables must be an object. Current argument: ${typeof b}.`);this.#vars=b,this.#tokens=a,this.#normalize()}#normalize(){const a=Array.from(this.#tokens);let b=this.#nextToken(a,["operator","generic","block","text"]);"operator"===b.type&&["!","!!"].includes(b.value)&&(this.#data.negateValue=b.value.length,b=this.#nextToken(a,["generic","block","text"])),"generic"===b.type?(this.#data.variable=b.value,this.#data.value=this.#getVariableValue(b.value)):"block"===b.type?this.#data.value=this.#prepareBlock(b.value):this.#data.value=b.value,b=this.#nextToken(a,["generic","operator"]);const c=b.value.substr(0,1)+(1<b.value.length?"*":"");if("undefined"!=typeof this.#paramShortcuts[c])this.#data.action=this.#paramShortcuts[c],a.unshift({type:"text",value:b.value.substr(1),info:"Extracted from shortcut"});else if(Object.values(this.#paramShortcuts).includes(b.value))this.#data.action=b.value;else throw new Error(`Invalid action: ${b.value} . Supported actions: ${Object.keys(this.#paramShortcuts).join(", ")}`);b=this.#nextToken(a,["text","generic",null],["block","operator"]),this.#data.param=b?.value,b=this.#nextToken(a,["block","operator",null]),"block"===b?.type&&"("==b.start&&(this.#data.arguments=this.#prepareArguments(b.value),b=this.#nextToken(a,["block","operator",null]));let d=0;if("operator"===b?.type&&["!","!!"].includes(b.value)&&(d=b.value.length,b=this.#nextToken(a,["block",null])),"block"===b?.type&&"{"==b.start?(this.#data.condition=this.#prepareBlock(b.value),b=this.#nextToken(a,["block",null])):this.#data.condition=!0,this.#data.condition=this.#negate(this.#data.condition,d),"debugger"===this.#data.action&&this.#data.valueBool)debugger}#getTokenValue(a){return"generic"===a.type?this.#getVariableValue(a.value):"block"===a.type?this.#prepareBlock(a.value):a.value}#getVariableValue(a){const b=a.split(".");switch(b[0]){case"true":case"always":return!0;case"false":case"never":return!1;case"null":case"none":return null;case"undefined":case"z":return;}let c=this.#vars;for(let d=0;d<b.length;d++){if("undefined"==typeof c[b[d]])return console.warn("Can't find variable "+a+" in data source %o",this.#vars),null;c=c[b[d]]}return c}prepare(){const a={...this.#data};if("special"===a.condition.type)return a.action=a.condition.value,a.condition=!0,a;switch(a.value=null===a.value&&"generic"==a.condition.type?this.#toValue(a.condition,a.negateValue):"object"==typeof a.value?this.#toValue(a.value,a.negateValue):this.#negate(a.value,a.negateValue),a.valueBool=this.#toBool(a.value),a.condition.type){case"block":a.condition=!!this.#prepareBlock(a.condition.value);break;case"generic":a.condition=!!this.#prepareVariable(a.condition.value);}return a}#prepareArguments(a){let b=[],c=[];for(let d=0;d<a.length;d++){const e=a[d];"separator"===e.type?(b.push(c),c=[]):c.push(e)}return b.push(c),b=b.map(a=>1==a.length?this.#getTokenValue(a[0]):this.#getTokenValue({type:"block",value:a})),b}#prepareVariable(a){const b=this.#getVariableValue(a);return null===b?null:this.#toBool(b)}#toBool(a){switch(typeof a){case"string":return 0!==a.length;case"object":return null!==a&&0!==Object.keys(a).length;case"number":return 0!==a;case"boolean":return a;default:return!1;}}#prepareBlock(tokens){const expression=this.#mkExpression(tokens);return eval(expression)}#mkExpression(a){let b="";for(;;){const c=this.#nextToken(a,["generic","block","text","operator",null]);if(!c)break;if(1<a.length&&"operator"===a[0].type&&this.#operatorsCompare.includes(a[0].value)){const d=this.#nextToken(a,["operator"]),e=this.#nextToken(a,["generic","text"]);b+=this.#compare(c,d,e)?1:0;continue}switch(c.type){case"operator":if(this.#operatorsBoolean.includes(c.value))b+=c.value;else throw new Error(`Invalid operator: ${c.value}. Supported operators: ${this.#operatorsBoolean.join(", ")}`);break;case"block":b+=this.#mkExpression(c.value);break;case"generic":b+=this.#prepareVariable(c.value)?1:0;break;case"text":b+=this.#toBool(c.value)?1:0;}}return"("+(b||"0")+")"}#toValue(a,b=0){let c=a;if("undefined"!=typeof a?.type&&"undefined"!=typeof a?.value)switch(a.type){case"generic":c=this.#getVariableValue(a.value);break;case"text":c=a.value;break;case"block":c=this.#prepareBlock(a.value);break;default:throw new Error(`Invalid token type: ${a.type} (value: ${JSON.stringify(a)}).`);}if(null===c)return c;c instanceof Array?c=c.length:c instanceof Number?c=c.valueOf():"object"==typeof c&&(c=Object.keys(c).length);const d=+c;let e=isNaN(d)?c:d.valueOf();return e=this.#negate(e,b),e}#compare(a,b,c){const d=this.#toValue(a),e=this.#toValue(c);switch(b.value){case"==":return d===e?1:0;case"!=":return d===e?0:1;case"<":return d<e?1:0;case"<=":return d<=e?1:0;case">":return d>e?1:0;case">=":return d>=e?1:0;default:throw new Error(`Invalid operator: ${b.value} . Supported operators: ${this.#operatorsCompare.join(", ")}`);}}#nextToken(a,b=[],c=[]){const d=a.shift();if(c.includes(d?.type))return a.unshift(d),null;if(!b.includes(d?d.type:null))throw new Error(`Invalid z-var value: (${d&&d.type}) ${JSON.stringify(d)}. Expected: ${JSON.stringify(b)}`);return d}#negate(a,b=0){for(let d=b;d;d--)a=!a;return a}}class Template{#rootElement=null;#vars=null;#callbacks={};#scopeLevel=0;#templateSelector="*[starts-with(@template, '{') or starts-with(@template, '[')]";#childrenScopeSelector="*[@z-removed or (@template-scope and @template-scope != 'inherit') or (@z-scope-children and @z-scope-children != 'inherit')]";#scopeSelector=`*[self::${this.#templateSelector} or @z-scope or parent::${this.#childrenScopeSelector} or @template-clone or ancestor::*/@z-removed]`;#zElementSelector="*[@z-var]";#scopeLevelSelector=`count(ancestor-or-self::${this.#scopeSelector})`;constructor(a){if(!(a instanceof Element))throw new Error("ZTemplate accepts an instance of HTMLElement as constructor argument");this.#rootElement=a,this.#scopeLevel=this.#query(this.#scopeLevelSelector,a.hasAttribute("template-scope")||a.hasAttribute("z-scope-children")?a.firstElementChild:a)}render(a,b){this.#vars=a,this.#callbacks=b;const c=this.#query(`self::${this.#zElementSelector}|descendant-or-self::${this.#zElementSelector}[${this.#scopeLevelSelector} = ${this.#scopeLevel}]`);for(let d=0;d<c.length;d++)this.#processZElement(c[d]);const d=this.#query(`self::${this.#templateSelector}|${this.#templateSelector}|descendant-or-self::${this.#templateSelector}[${this.#scopeLevelSelector} = ${this.#scopeLevel} + 1]`);for(let c=0;c<d.length;c++)this.#processTemplate(d[c])}#processTemplate(a){const b=a.getAttribute("template"),c=b.substring(1,b.length-1);if(!c)throw new Error(`Template "${c}" not found (attr template="${b}")`);const d=this.#getVariableValue(c);if(null===d)return void console.warn(`Template "${c}" not found in data`,this.#vars);let e;"{"==b.substr(0,1)?e=[d]:d&&d[Symbol.iterator]?e=Array.from(d):"object"==typeof d?e=Object.entries(d).map(([a,b])=>({key:a,value:b})):(console.warn("Template value '%s' is not iterable: %o. Template: %o",c,d,a),e=[]);const f=this.#getTemplateClones(a,e);let g=e.length,h=[a];for(let b=f.length-1;0<=b;b--){const a=f[b];switch(a.action){case"reuse":case"add":const b="object"==typeof e[--g]?e[g]:{value:e[g],key:g};b._parent_=this.#vars;for(let c=a.elements.length-1;0<=c;c--){const d=a.elements[c];if("add"==a.action){for(;h.length&&!h[0].parentNode;)h.shift();h[0].before(d)}const e=new Template(d);e.render(b,this.#callbacks),h.unshift(d)}break;case"remove":a.elements.forEach(a=>this.#animateRemove(a));}}}#getTemplateClones(a,b){const c=b.length,d=a.getAttribute("template"),e=[],f=[];let g=a.previousElementSibling,h=-1,i=-1;for(;g&&g.getAttribute("template-clone")==d;){const a=g.getAttribute("template-clone-id");i=_Mathmax(i,a),g.hasAttribute("z-removed")||(h==a&&null!==a?f[0].unshift(g):f.unshift([g])),g=g.previousElementSibling,h=a}const j=b.map(a=>this.#getHash(a)),k=f.map(a=>a[0].getAttribute("template-clone-hash"));for(;j.length||k.length;){const b=j.shift(),c=k.shift();if(b==c)e.push({elements:f.shift(),action:"reuse"});else{const g=k.indexOf(b),h=j.indexOf(c);if(b&&(!c||-1!=h)&&(-1==g||h<g))e.push({elements:this.#cloneTemplateElement(a,{"template-clone":d,"template-clone-id":++i,"template-clone-hash":b}),action:"add"}),c&&k.unshift(c);else{const a=f.shift();e.push({elements:a,action:"remove"}),b&&j.unshift(b)}}}return e}#cloneTemplateElement(a,b){const c=[];return a.content instanceof DocumentFragment?c.push(...Array.from(a.content.children).map(a=>a.cloneNode(!0))):c.push(a.cloneNode(!0)),c.forEach(a=>{a.classList.add("template-clone"),a.removeAttribute("template");for(const[c,d]of Object.entries(b))a.setAttribute(c,d)}),c}#processZElement(a){const b=a.getAttribute("z-var"),c=tokenize(b),d=Array.from(c).map(a=>prepare(a,this.#vars)),e=[],f=this.#cloneProto(a);d.forEach(a=>{if(a.condition){if(null===a.value)return void console.warn(`The command %o's value is null. Skipping the condition. Variables: %o`,a,this.#vars);switch(a.action){case"attr":this.#cmdAttr(f,a);break;case"text":this.#cmdText(f,a);break;case"html":this.#cmdHtml(f,a);break;case"value":this.#cmdValue(f,a);break;case"class":this.#cmdClass(f,a);break;case"toggle":this.#cmdToggle(f,a);break;case"debugger":if(a.valueBool)debugger;break;case"remove":case"event":case"call":e.push(a);}}}),this.#mergeProto(a,f),e.forEach(b=>{switch(a.parentNode||console.warn("The element %o was removed from the DOM. Something may break when executing the command %o",a,b),b.action){case"remove":this.#cmdRemove(a,b);break;case"event":this.#cmdEvent(a,b);break;case"call":this.#cmdCall(a,b);}})}#cmdCall(a,b){const c=this.#callbacks.get(b.param);if(!c||"function"!=typeof c)return void console.error(`Callback "${b.param}" not found or is not a function in command "${JSON.stringify(b)}"`);const d={value:b.value,data:this.#vars,arguments:b.arguments};return"function"==typeof c?void c(a,d):void console.error(`Callback ${b.param} is not defined`)}#cmdEvent(a,b){const c={value:b.value,data:this.#vars,arguments:b.arguments},d=new CustomEvent(b.param,{detail:c,bubbles:!0,cancelable:!0,composed:!1});a.dispatchEvent(d)}#cmdRemove(a,b){b.valueBool||this.#animateRemove(a)}#cmdToggle(a,b){a.classList.contains("z-template-hidden")||(b.valueBool?(a.classList.add("z-template-visible"),a.classList.remove("z-template-hidden")):(a.classList.add("z-template-hidden"),a.classList.remove("z-template-visible")))}#cmdClass(a,b){const c=b.param.trim().split(/[ ,.]+/);c.forEach(c=>{let d=b.valueBool;"!"===c.substr(0,1)&&(d=!d,c=c.substr(1)),d?a.classList.add(c):a.classList.remove(c)})}#cmdValue(a,b){if(a.matches("input[type=\"checkbox\"], input[type=\"radio\"]")){const c="boolean"==typeof b.value?b.value:a.value===b.value;c?a.setAttribute("checked","checked"):a.removeAttribute("checked")}else a.matches("input")?a.setAttribute("value",b.value):a.matches("select")?Array.from(a).filter(a=>a.value===b.value).forEach(a=>a.setAttribute("selected","selected")):a.matches("textarea")?a.textContent=b.value:a.setAttribute("value",b.value)}#cmdHtml(a,b){a.innerHTML=b.value}#cmdText(a,b){a.textContent=this.#getReplaceText(a,a.textContent,b.variable,void 0===b.value||null===b.value||!1===b.value?"":b.value,"")}#cmdAttr(a,b){if(!0===b.value)a.setAttribute(b.param,b.param);else if(!1===b.value)a.removeAttribute(b.param);else{const c=this.#getReplaceText(a,a.getAttribute(b.param),b.variable,b.value,b.param);a.setAttribute(b.param,c)}}#getReplaceText(a,b,c,d,e=""){const f="${"+c+"}",g="z-var-content"+(e?"-"+e:"");let h;return h=c&&b&&-1!==b.indexOf(f)?(b||"").replace(f,d):c&&b&&["src","href"].includes(e)&&-1!==b.indexOf(encodeURIComponent(f))?(b||"").replace(encodeURIComponent(f),encodeURIComponent(d)):d,b!==h&&b&&!a.hasAttribute(g)&&(-1!==b.indexOf("${")||-1!==b.indexOf(encodeURIComponent("${")))&&a.setAttribute(g,b),h}#mergeProto(a,b){let c=!1;a.innerHTML!==b.innerHTML&&(a.innerHTML=b.innerHTML,c=!0);for(let c=0;c<b.attributes.length;c++){const d=b.attributes[c];a.getAttribute(d.name)!==d.value&&a.setAttribute(d.name,d.value)}for(let c=0;c<a.attributes.length;c++){const d=a.attributes[c];b.hasAttribute(d.name)||a.removeAttribute(d.name)}if(c){const b=parseInt(a.getAttribute("z-content-rev")||0)+1;a.removeAttribute("z-content-rev"),setTimeout(()=>a.setAttribute("z-content-rev",b),0)}}#cloneProto(a){const b=a.cloneNode(!0),c=[],d=Array.from(a.attributes).filter(a=>{a.name.match(/^z-var-content-?/)&&c.push(a),b.setAttribute(a.name,a.value)});return c.forEach(a=>{const c=a.name.replace(/^z-var-content-?/,"");c?b.setAttribute(c,a.value):b.textContent=a.value}),b.classList.remove(...["dna-template-visible","z-template-visible","dna-template-hidden","z-template-hidden"]),b}#query(a,b=null){const c=this.#rootElement.ownerDocument.evaluate(a,b||this.#rootElement,null,XPathResult.ANY_TYPE,null);switch(c.resultType){case XPathResult.NUMBER_TYPE:return c.numberValue;case XPathResult.STRING_TYPE:return c.stringValue;case XPathResult.BOOLEAN_TYPE:return c.booleanValue;case XPathResult.UNORDERED_NODE_ITERATOR_TYPE:for(var d,e=[];d=c.iterateNext();e.push(d));return e;default:return null;}}#getVariableValue(a){const b=a.split(".");let c=this.#vars;for(let d=0;d<b.length;d++){if("undefined"==typeof c[b[d]])return console.warn("Can't find variable "+a+" in data source %o",this.#vars),null;c=c[b[d]]}return c}#getHash(a){let b;if("object"==typeof a&&"undefined"!=typeof a.id){if("string"==typeof a.id||"number"==typeof a.id)return a.id;b=JSON.stringify(a.id)}else b=JSON.stringify(a,(a,b)=>"_parent_"==a?void 0:b);for(var d,e=[],f=0;256>f;f++){d=f;for(var g=0;8>g;g++)d=1&d?3988292384^d>>>1:d>>>1;e[f]=d}for(var h=-1,f=0;f<b.length;f++)h=h>>>8^e[255&(h^b.charCodeAt(f))];return""+((-1^h)>>>0)}#animateRemove(a){if(a.hasAttribute("z-removed")||!a.parentNode)return;const b=window.getComputedStyle(a).animationName;a.setAttribute("z-removed","true");const c=window.getComputedStyle(a),d=c.animationName,e=1e3*((parseFloat(c.animationDuration)||0)+(parseFloat(c.animationDelay)||0)),f=_Mathmax(200,e),g=new Promise(c=>{b===d?c():(a.addEventListener("animationend",c),a.removeEventListener("animationcancel",c),a.removeEventListener("animationiteration",c))}),h=new Promise(a=>{setTimeout(a,f)}),i=new Promise(b=>{var d=Math.ceil;const e=a.getBoundingClientRect();a.style.height=e.height+"px",a.style.width=e.width+"px",a.style.transition="none",a.style.margin=`${c.marginTop} ${c.marginRight} ${c.marginBottom} ${c.marginLeft}`,a.addEventListener("transitionend",b),a.style.transition=`margin ${f}ms ease-in-out`,a.style.marginRight=`-${d(e.height+parseInt(c.marginLeft))}px`,a.style.marginBottom=`-${d(e.height+parseInt(c.marginTop))}px`});Promise.any([Promise.all([g,i]),h]).then(()=>a.remove())}}return zTemplate.callbacks=new Map,"undefined"==typeof jQuery||jQuery.fn.template||(jQuery.fn.template=function(a){return this.each((b,c)=>zTemplate(c,a)),this}),zTemplate}();

zTemplate.callbacks
    .set('roller', function(element, detail) {
        const document = element.ownerDocument;
        const speed = detail.arguments[0] || 1000;
        const delay = detail.arguments[1] || 100;
        // Convert value into string
        const sourceText = element.textContent + '';  
        const targetText = detail.value + '';
        const len = Math.max(sourceText.length, targetText.length);
        const frag = document.createDocumentFragment();
        const height = element.getBoundingClientRect().height;

        if (sourceText === targetText || sourceText.length === 0) {
            element.textContent = targetText;
            return;
        }

        // Set css variable --z-roller-speed
        element.classList.add('z-roller-rolling');
        
        for (let i = 0; i < len; i++) {
            const sourceChar = sourceText[i] || '';
            const targetChar = targetText[i] || '';

            const div = frag.appendChild(document.createElement('div'));
            div.setAttribute('data-target', targetChar);
            const {direction, chars} = generateRollerChars(sourceChar, targetChar);
            if (direction === 'up') {
                div.classList.add('z-roller-up', 'z-roller');
            } else {
                div.classList.add('z-roller-down', 'z-roller');
            }

            const charSpan = div.appendChild(document.createElement('span'));
            charSpan.classList.add('z-roller-letter');
            charSpan.textContent = sourceChar;

            // We use :before to avoid multiplying the textContents
            // when multiple callbacks are applied in short succession
            div.style.setProperty('--z-roller-speed', speed + 'ms');
            div.style.setProperty('--z-roller-line-height', height + 'px');
            div.setAttribute('data-z-face', chars.join("\n"));

        }

        element.replaceChildren(frag);
        for (i = 0; i < element.childElementCount; i++) {
            // Get i-th child element
            const child = element.children[i];
            const charDelay = delay * i;
            child.style.setProperty('--z-roller-delay', charDelay + 'ms');
            child.classList.add('z-roller-animate');
            setTimeout(() => child.replaceWith(child.getAttribute('data-target')), charDelay + speed);
        }

        // Generate array of characters between two characters
        function generateRollerChars(fromChar, toChar) {
            let fromCode = (fromChar || ' ').charCodeAt(0);
            let toCode = (toChar || ' ').charCodeAt(0);
            const len = Math.abs(fromCode - toCode);
            const chars = [];

            const direction = fromCode < toCode ? 'up' : 'down';
            if (direction === 'down') {
                [fromCode, toCode] = [toCode, fromCode];
            }

            for (let i = 0; i <= len; i++) {
                chars.push(String.fromCharCode(fromCode + i));
            }
            return {direction, chars};
        }
    });

if (typeof(Storage) !== "undefined") {
	var current = localStorage.recent;
	if (current) {
		var tabcontent = document.getElementsByClassName("tabcontent");
		for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		}
		var tablink = document.getElementsByClassName("tablink");
		for (i = 0; i < tablink.length; i++) {
			tablink[i].classList.remove("active");
		}
		if (current == "link1")
			document.getElementById("signin").style.display = "block";
		else
			document.getElementById("signup").style.display = "block";
		document.getElementById(current).classList.add("active");
	}
}
/*=================================MAIN=================================*/
function openTab(evt, choice) {
	var tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}
	var tablink = document.getElementsByClassName("tablink");
	for (i = 0; i < tablink.length; i++) {
		tablink[i].classList.remove("active");
	}
	document.getElementById(choice).style.display = "block";
	evt.currentTarget.classList.add("active");
	if (typeof(Storage) !== "undefined") {
		localStorage.recent = evt.currentTarget.getAttribute('id');
	}
}

function validateLogin() {
	clearRequiredFields();
	var required = document.getElementsByClassName("required");
	var useremail = document.getElementById("loginuseremail").value;
	var userpass = document.getElementById("loginuserpass").value;
	var result = true;
	if (useremail == "") {
		required[0].innerHTML = "This field cannot be empty.";
		result = false;
	}
	if (userpass == "") {
		required[1].innerHTML = "This field cannot be empty.";
		result = false;
	}
	return result;
}

function validateRegister() {
	clearRequiredFields();
	var required = document.getElementsByClassName("required");
	var userfirstname = document.getElementById("userfirstname").value;
	var userlastname = document.getElementById("userlastname").value;
	var userpass = document.getElementById("userpass").value;
	var userpassconfirm = document.getElementById("userpassconfirm").value;
	var useremail = document.getElementById("useremail").value;
	var usergender = document.getElementsByClassName("usergender");
	var result = true;
	if (userfirstname == "") {
		required[2].innerHTML = "This field cannot be empty.";
		result = false;
	}
	if (userlastname == "") {
		required[3].innerHTML = "This field cannot be empty.";
		result = false;
	}
	if (userpass == "") {
		required[5].innerHTML = "This field cannot be empty.";
		result = false;
	}
	if (userpassconfirm == "") {
		required[6].innerHTML = "This field cannot be empty.";
		result = false;
	}
	if (userpass != "" && userpassconfirm != "" && userpass != userpassconfirm) {
		required[5].innerHTML = "Passwords doesn't match.";
		required[6].innerHTML = "Passwords doesn't match.";
		result = false;
	}
	if (useremail == "") {
		required[7].innerHTML = "This field cannot be empty.";
		result = false;
	} else if (!validateEmail(useremail)) {
		required[7].innerHTML = "Invalid Email Format.";
		result = false;
	}
	if (!usergender[0].checked && !usergender[1].checked) {
		required[8].innerHTML = "You must select your gender.";
		result = false;
	}
	return result;
}

function clearRequiredFields() {
	var required = document.getElementsByClassName("required");
	for (i = 0; i < required.length; i++) {
		required[i].innerHTML = "";
	}
}
$("textarea").each(function () {
  this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:hidden;");
}).on("input", function () {
  this.style.height = 0;
  this.style.height = (this.scrollHeight) + "px";
});
function _like(id){
	$.get("worker/likes.php?post_id=" + id, function(data){
		var splt = data.split(";");
		var post_like = document.getElementById("post-like-" + id);
		var class_l = "icon-heart fa-heart icon-click";
		if(splt[0] === "1"){
			post_like.className = class_l + " fa-solid p-heart";
			post_like.classList.toggle("active");
		}else{
			post_like.className = class_l + " fa-regular white-col";
		}
		zTemplate(document.getElementById("post-like-count-" + id), {"counter": parseInt(splt[1])});
	});
}
window.onpopstate = function(e){
    if(e.state){
        document.getElementById("content").innerHTML = e.state.html;
        document.title = e.state.pageTitle;
    }
};
function changeUrl(url){
	$.ajax({
		url: url,
		type: 'GET',
		success: function(res) {
			processAjaxData(res, url);
        }
    });
}
function fetch_post(){
	var page = document.getElementById("page").value;
	$.get("worker/fetch_post.php?page=" + page, function(data){
		document.getElementById("feed").innerHTML = data;
	});
}
function processAjaxData(response, urlPath){
	var title = $(response).filter('title').text();
	document.getElementsByTagName("html")[0].innerHTML = response;
	document.title = title;
	window.history.pushState({"html":response,"pageTitle":title},"", urlPath);
	if(urlPath === "/home.php" || urlPath === "home.php")
		fetch_post();
}