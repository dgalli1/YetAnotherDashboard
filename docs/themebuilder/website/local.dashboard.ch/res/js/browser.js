/*! For license information please see browser.js.LICENSE.txt */
(()=>{var e=document.getElementById("userMenu"),n=document.getElementById("userButton"),t=document.getElementById("nav-content"),s=document.getElementById("nav-toggle");function i(e,n){for(;e.parentNode;){if(e==n)return!0;e=e.parentNode}return!1}document.onclick=function(d){var c=d&&d.target||event&&event.srcElement;i(c,e)||(i(c,n)&&e.classList.contains("invisible")?e.classList.remove("invisible"):e.classList.add("invisible"));i(c,t)||(i(c,s)&&t.classList.contains("hidden")?t.classList.remove("hidden"):t.classList.add("hidden"))}})();
//# sourceMappingURL=browser.js.map