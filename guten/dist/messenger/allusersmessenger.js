!function(){"use strict";var e={n:function(t){var s=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(s,{a:s}),s},d:function(t,s){for(var a in s)e.o(s,a)&&!e.o(t,a)&&Object.defineProperty(t,a,{enumerable:!0,get:s[a]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.element,s=window.wp.apiFetch,a=e.n(s),n=window.wp.components,r=()=>{const e=parseInt(allusersmessenger_data.userid),s=1e3*parseInt(allusersmessenger_data.interval_sec),r=JSON.parse(allusersmessenger_data.messages),l=JSON.parse(allusersmessenger_data.messages_delete),[c,u]=(0,t.useState)(r),[o,m]=(0,t.useState)(l),[i,d]=(0,t.useState)(!1),[_,p]=(0,t.useState)(parseInt(allusersmessenger_data.latest_time)),[g,b]=(0,t.useState)(!1),E=(0,t.useRef)(!0),f=(0,t.useRef)(),h=(0,t.useRef)();(0,t.useEffect)((()=>{let t=setInterval((()=>{a()({path:"rf/all_users_messenger_view_api/token",method:"POST",data:{userid:e,delete:o,submit_delete:g}}).then((e=>{E.current&&(E.current.scrollIntoView(),E.current=!1),JSON.stringify(c)!==JSON.stringify(e.messages)&&(p(e.latest_time),u(e.messages),f.current.scrollIntoView()),g&&y(o),b(!1),window.addEventListener("resize",(()=>{f.current.scrollIntoView()}))}))}),s);return()=>{clearInterval(t)}}),[c,o,g]);const v=[];void 0!==c&&void 0!==_&&Object.entries(c).map((s=>{if(c.hasOwnProperty){let a=s[1].message.split(/\r\n|\n/),r=[];Object.keys(a).map((e=>{r.push((0,t.createElement)(t.Fragment,null,a[e],(0,t.createElement)("br",null)))})),s[1].userid==e?v.push((0,t.createElement)("div",{className:"balloon_r"},(0,t.createElement)("p",{className:"says says_r_color"},s[1].username,(0,t.createElement)("br",null),s[1].datetime,(0,t.createElement)("br",null),r),(0,t.createElement)(n.CheckboxControl,{checked:o[parseInt(s[0])],onChange:e=>N(parseInt(s[0]),e)}))):v.push((0,t.createElement)("div",{className:"balloon_l"},(0,t.createElement)("div",{className:"faceicon"},(0,t.createElement)("img",{src:s[1].avatar})),(0,t.createElement)("p",{className:"says says_l_color"},s[1].username,(0,t.createElement)("br",null),s[1].datetime,(0,t.createElement)("br",null),r)))}}));const w=[];void 0!==o&&void 0!==g&&void 0!==i&&i&&w.push((0,t.createElement)("div",{className:"delete_button"},(0,t.createElement)(n.Button,{className:"button button-primary",onClick:()=>{b(!0),d(!1)}},allusersmessenger_data.delete_label)));const y=e=>{Object.keys(e).map((t=>{if(e[t]){delete o[t];let e=Object.assign({},o);m(e)}})),O(o)},N=(e,t)=>{o[e]=t;let s=Object.assign({},o);m(s),O(o)},O=e=>{let t=Object.values(e).includes(!0);d(t)},I=[];if(h){const e=()=>{h.current.scrollIntoView()};I.push((0,t.createElement)(n.Button,{className:"button button-large top_button",onClick:e},allusersmessenger_data.top_button_label,"  ↑"))}const S=[];if(f){const e=()=>{f.current.scrollIntoView()};S.push((0,t.createElement)(n.Button,{className:"button button-large bottom_button",onClick:e},allusersmessenger_data.bottom_button_label,"  ↓"))}return(0,t.createElement)(t.Fragment,null,(0,t.createElement)("div",{ref:h}),S,v,w,I,(0,t.createElement)("div",{ref:E}),(0,t.createElement)("div",{ref:f}))},l=()=>{const e=parseInt(allusersmessenger_data.userid),[s,r]=(0,t.useState)(""),[l,c]=(0,t.useState)(!1),u=(0,t.useRef)(!0);(0,t.useEffect)((()=>{u.current?u.current=!1:a()({path:"rf/all_users_messenger_post_api/token",method:"POST",data:{userid:e,message:s,submit_message:l}}).then((e=>{l&&(c(!1),r(""))}))}),[l]);const o=[];var m;o.push((0,t.createElement)(n.TextareaControl,{name:"area_shift_enter",className:"message_text",help:allusersmessenger_data.input_help_label,rows:(m=s,m.split("\n").length),value:s,onChange:e=>{r(e),document.addEventListener("keydown",i,!1)}}));const i=e=>{e.target.type&&"textarea"==e.target.type&&e.target.name&&"area_shift_enter"==e.target.name&&e.shiftKey&&"Enter"===e.code&&c(!0)},d=[];return s&&d.push((0,t.createElement)(n.Button,{className:"css-button-arrow--green",onClick:()=>{c(!0)}},allusersmessenger_data.submit_label)),(0,t.createElement)("div",{className:"MessengerPost"},o,d)};(0,t.render)((0,t.createElement)((()=>(0,t.createElement)("div",{className:"wrap"},(0,t.createElement)(r,null),(0,t.createElement)(l,null))),null),document.getElementById("all-users-messenger-page"))}();