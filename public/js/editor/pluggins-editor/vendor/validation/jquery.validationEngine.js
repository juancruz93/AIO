/*
 * Inline Form Validation Engine 2.6.2, jQuery plugin
 *
 * Copyright(c) 2010, Cedric Dugas
 * http://www.position-absolute.com
 *
 * 2.0 Rewrite by Olivier Refalo
 * http://www.crionics.com
 *
 * Form validation engine allowing custom regex rules to be added.
 * Licensed under the MIT License
 */
(function(e){"use strict";var t={init:function(n){var r=this;if(!r.data("jqv")||r.data("jqv")==null)n=t._saveOptions(r,n),e(document).on("click",".formError",function(){e(this).fadeOut(150,function(){e(this).parent(".formErrorOuter").remove(),e(this).remove()})});return this},attach:function(n){var r=this,i;return n?i=t._saveOptions(r,n):i=r.data("jqv"),i.validateAttribute=r.find("[data-validation-engine*=validate]").length?"data-validation-engine":"class",i.binded&&(r.on(i.validationEventTrigger,"["+i.validateAttribute+"*=validate]:not([type=checkbox]):not([type=radio]):not(.datepicker)",t._onFieldEvent),r.on("click","["+i.validateAttribute+"*=validate][type=checkbox],["+i.validateAttribute+"*=validate][type=radio]",t._onFieldEvent),r.on(i.validationEventTrigger,"["+i.validateAttribute+"*=validate][class*=datepicker]",{delay:300},t._onFieldEvent)),i.autoPositionUpdate&&e(window).bind("resize",{noAnimation:!0,formElem:r},t.updatePromptsPosition),r.on("click","a[data-validation-engine-skip], a[class*='validate-skip'], button[data-validation-engine-skip], button[class*='validate-skip'], input[data-validation-engine-skip], input[class*='validate-skip']",t._submitButtonClick),r.removeData("jqv_submitButton"),r.on("submit",t._onSubmitEvent),this},detach:function(){var n=this,r=n.data("jqv");return n.find("["+r.validateAttribute+"*=validate]").not("[type=checkbox]").off(r.validationEventTrigger,t._onFieldEvent),n.find("["+r.validateAttribute+"*=validate][type=checkbox],[class*=validate][type=radio]").off("click",t._onFieldEvent),n.off("submit",t.onAjaxFormComplete),n.die("submit",t.onAjaxFormComplete),n.removeData("jqv"),n.off("click","a[data-validation-engine-skip], a[class*='validate-skip'], button[data-validation-engine-skip], button[class*='validate-skip'], input[data-validation-engine-skip], input[class*='validate-skip']",t._submitButtonClick),n.removeData("jqv_submitButton"),r.autoPositionUpdate&&e(window).unbind("resize",t.updatePromptsPosition),this},validate:function(){var n=e(this),r=null;if((n.is("form")||n.hasClass("validationEngineContainer"))&&!n.hasClass("validating")){n.addClass("validating");var i=n.data("jqv"),r=t._validateFields(this);setTimeout(function(){n.removeClass("validating")},100),r&&i.onSuccess?i.onSuccess():!r&&i.onFailure&&i.onFailure()}else if(n.is("form")||n.hasClass("validationEngineContainer"))n.removeClass("validating");else{var s=n.closest("form, .validationEngineContainer"),i=s.data("jqv")?s.data("jqv"):e.validationEngine.defaults,r=t._validateField(n,i);r&&i.onFieldSuccess?i.onFieldSuccess():i.onFieldFailure&&i.InvalidFields.length>0&&i.onFieldFailure()}return i.onValidationComplete?!!i.onValidationComplete(s,r):r},updatePromptsPosition:function(n){if(n&&this==window)var r=n.data.formElem,i=n.data.noAnimation;else var r=e(this.closest("form, .validationEngineContainer"));var s=r.data("jqv");return r.find("["+s.validateAttribute+"*=validate]").not(":disabled").each(function(){var n=e(this);s.prettySelect&&n.is(":hidden")&&(n=r.find("#"+s.usePrefix+n.attr("id")+s.useSuffix));var o=t._getPrompt(n),u=e(o).find(".formErrorContent").html();o&&t._updatePrompt(n,e(o),u,undefined,!1,s,i)}),this},showPrompt:function(e,n,r,i){var s=this.closest("form, .validationEngineContainer"),o=s.data("jqv");return o||(o=t._saveOptions(this,o)),r&&(o.promptPosition=r),o.showArrow=i==1,t._showPrompt(this,e,n,!1,o),this},hide:function(){var n=e(this).closest("form, .validationEngineContainer"),r=n.data("jqv"),i=r&&r.fadeDuration?r.fadeDuration:.3,s;return e(this).is("form")||e(this).hasClass("validationEngineContainer")?s="parentForm"+t._getClassName(e(this).attr("id")):s=t._getClassName(e(this).attr("id"))+"formError",e("."+s).fadeTo(i,.3,function(){e(this).parent(".formErrorOuter").remove(),e(this).remove()}),this},hideAll:function(){var t=this,n=t.data("jqv"),r=n?n.fadeDuration:300;return e(".formError").fadeTo(r,300,function(){e(this).parent(".formErrorOuter").remove(),e(this).remove()}),this},_onFieldEvent:function(n){var r=e(this),i=r.closest("form, .validationEngineContainer"),s=i.data("jqv");s.eventTrigger="field",window.setTimeout(function(){t._validateField(r,s),s.InvalidFields.length==0&&s.onFieldSuccess?s.onFieldSuccess():s.InvalidFields.length>0&&s.onFieldFailure&&s.onFieldFailure()},n.data?n.data.delay:0)},_onSubmitEvent:function(){var n=e(this),r=n.data("jqv");if(n.data("jqv_submitButton")){var i=e("#"+n.data("jqv_submitButton"));if(i&&i.length>0)if(i.hasClass("validate-skip")||i.attr("data-validation-engine-skip")=="true")return!0}r.eventTrigger="submit";var s=t._validateFields(n);return s&&r.ajaxFormValidation?(t._validateFormWithAjax(n,r),!1):r.onValidationComplete?!!r.onValidationComplete(n,s):s},_checkAjaxStatus:function(t){var n=!0;return e.each(t.ajaxValidCache,function(e,t){if(!t)return n=!1,!1}),n},_checkAjaxFieldStatus:function(e,t){return t.ajaxValidCache[e]==1},_validateFields:function(n){var r=n.data("jqv"),i=!1;n.trigger("jqv.form.validating");var s=null;n.find("["+r.validateAttribute+"*=validate]").not(":disabled").each(function(){var o=e(this),u=[];if(e.inArray(o.attr("name"),u)<0){i|=t._validateField(o,r),i&&s==null&&(o.is(":hidden")&&r.prettySelect?s=o=n.find("#"+r.usePrefix+t._jqSelector(o.attr("id"))+r.useSuffix):s=o);if(r.doNotShowAllErrosOnSubmit)return!1;u.push(o.attr("name"));if(r.showOneMessage==1&&i)return!1}}),n.trigger("jqv.form.result",[i]);if(i){if(r.scroll){var o=s.offset().top,u=s.offset().left,a=r.promptPosition;typeof a=="string"&&a.indexOf(":")!=-1&&(a=a.substring(0,a.indexOf(":")));if(a!="bottomRight"&&a!="bottomLeft"){var f=t._getPrompt(s);f&&(o=f.offset().top)}r.scrollOffset&&(o-=r.scrollOffset);if(r.isOverflown){var l=e(r.overflownDIV);if(!l.length)return!1;var c=l.scrollTop(),h=-parseInt(l.offset().top);o+=c+h-5;var p=e(r.overflownDIV+":not(:animated)");p.animate({scrollTop:o},1100,function(){r.focusFirstField&&s.focus()})}else e("html, body").animate({scrollTop:o},1100,function(){r.focusFirstField&&s.focus()}),e("html, body").animate({scrollLeft:u},1100)}else r.focusFirstField&&s.focus();return!1}return!0},_validateFormWithAjax:function(n,r){var i=n.serialize(),s=r.ajaxFormValidationMethod?r.ajaxFormValidationMethod:"GET",o=r.ajaxFormValidationURL?r.ajaxFormValidationURL:n.attr("action"),u=r.dataType?r.dataType:"json";e.ajax({type:s,url:o,cache:!1,dataType:u,data:i,form:n,methods:t,options:r,beforeSend:function(){return r.onBeforeAjaxFormValidation(n,r)},error:function(e,n){t._ajaxError(e,n)},success:function(i){if(u=="json"&&i!==!0){var s=!1;for(var o=0;o<i.length;o++){var a=i[o],f=a[0],l=e(e("#"+f)[0]);if(l.length==1){var c=a[2];if(a[1]==1)if(c==""||!c)t._closePrompt(l);else{if(r.allrules[c]){var h=r.allrules[c].alertTextOk;h&&(c=h)}r.showPrompts&&t._showPrompt(l,c,"pass",!1,r,!0)}else{s|=!0;if(r.allrules[c]){var h=r.allrules[c].alertText;h&&(c=h)}r.showPrompts&&t._showPrompt(l,c,"",!1,r,!0)}}}r.onAjaxFormComplete(!s,n,i,r)}else r.onAjaxFormComplete(!0,n,i,r)}})},_validateField:function(n,r,i){n.attr("id")||(n.attr("id","form-validation-field-"+e.validationEngine.fieldIdCounter),++e.validationEngine.fieldIdCounter);if(!r.validateNonVisibleFields&&(n.is(":hidden")&&!r.prettySelect||n.parent().is(":hidden")))return!1;var s=n.attr(r.validateAttribute),o=/validate\[(.*)\]/.exec(s);if(!o)return!1;var u=o[1],a=u.split(/\[|,|\]/),f=!1,l=n.attr("name"),c="",h="",p=!1,d=!1;r.isError=!1,r.showArrow=!1,r.maxErrorsPerField>0&&(d=!0);var v=e(n.closest("form, .validationEngineContainer"));for(var m=0;m<a.length;m++)a[m]=a[m].replace(" ",""),a[m]===""&&delete a[m];for(var m=0,g=0;m<a.length;m++){if(d&&g>=r.maxErrorsPerField){if(!p){var y=e.inArray("required",a);p=y!=-1&&y>=m}break}var b=undefined;switch(a[m]){case"required":p=!0,b=t._getErrorMessage(v,n,a[m],a,m,r,t._required);break;case"custom":b=t._getErrorMessage(v,n,a[m],a,m,r,t._custom);break;case"groupRequired":var w="["+r.validateAttribute+"*="+a[m+1]+"]",E=v.find(w).eq(0);E[0]!=n[0]&&(t._validateField(E,r,i),r.showArrow=!0),b=t._getErrorMessage(v,n,a[m],a,m,r,t._groupRequired),b&&(p=!0),r.showArrow=!1;break;case"ajax":b=t._ajax(n,a,m,r),b&&(h="load");break;case"minSize":b=t._getErrorMessage(v,n,a[m],a,m,r,t._minSize);break;case"maxSize":b=t._getErrorMessage(v,n,a[m],a,m,r,t._maxSize);break;case"min":b=t._getErrorMessage(v,n,a[m],a,m,r,t._min);break;case"max":b=t._getErrorMessage(v,n,a[m],a,m,r,t._max);break;case"past":b=t._getErrorMessage(v,n,a[m],a,m,r,t._past);break;case"future":b=t._getErrorMessage(v,n,a[m],a,m,r,t._future);break;case"dateRange":var w="["+r.validateAttribute+"*="+a[m+1]+"]";r.firstOfGroup=v.find(w).eq(0),r.secondOfGroup=v.find(w).eq(1);if(r.firstOfGroup[0].value||r.secondOfGroup[0].value)b=t._getErrorMessage(v,n,a[m],a,m,r,t._dateRange);b&&(p=!0),r.showArrow=!1;break;case"dateTimeRange":var w="["+r.validateAttribute+"*="+a[m+1]+"]";r.firstOfGroup=v.find(w).eq(0),r.secondOfGroup=v.find(w).eq(1);if(r.firstOfGroup[0].value||r.secondOfGroup[0].value)b=t._getErrorMessage(v,n,a[m],a,m,r,t._dateTimeRange);b&&(p=!0),r.showArrow=!1;break;case"maxCheckbox":n=e(v.find("input[name='"+l+"']")),b=t._getErrorMessage(v,n,a[m],a,m,r,t._maxCheckbox);break;case"minCheckbox":n=e(v.find("input[name='"+l+"']")),b=t._getErrorMessage(v,n,a[m],a,m,r,t._minCheckbox);break;case"equals":b=t._getErrorMessage(v,n,a[m],a,m,r,t._equals);break;case"funcCall":b=t._getErrorMessage(v,n,a[m],a,m,r,t._funcCall);break;case"creditCard":b=t._getErrorMessage(v,n,a[m],a,m,r,t._creditCard);break;case"condRequired":b=t._getErrorMessage(v,n,a[m],a,m,r,t._condRequired),b!==undefined&&(p=!0);break;default:}var S=!1;if(typeof b=="object")switch(b.status){case"_break":S=!0;break;case"_error":b=b.message;break;case"_error_no_prompt":return!0;default:}if(S)break;typeof b=="string"&&(c+=b+"<br/>",r.isError=!0,g++)}!p&&!n.val()&&n.val().length<1&&(r.isError=!1);var x=n.prop("type"),T=n.data("promptPosition")||r.promptPosition;(x=="radio"||x=="checkbox")&&v.find("input[name='"+l+"']").size()>1&&(T==="inline"?n=e(v.find("input[name='"+l+"'][type!=hidden]:last")):n=e(v.find("input[name='"+l+"'][type!=hidden]:first")),r.showArrow=!1),n.is(":hidden")&&r.prettySelect&&(n=v.find("#"+r.usePrefix+t._jqSelector(n.attr("id"))+r.useSuffix)),r.isError&&r.showPrompts?t._showPrompt(n,c,h,!1,r):f||t._closePrompt(n),f||n.trigger("jqv.field.result",[n,r.isError,c]);var N=e.inArray(n[0],r.InvalidFields);return N==-1?r.isError&&r.InvalidFields.push(n[0]):r.isError||r.InvalidFields.splice(N,1),t._handleStatusCssClasses(n,r),r.isError&&r.onFieldFailure&&r.onFieldFailure(n),!r.isError&&r.onFieldSuccess&&r.onFieldSuccess(n),r.isError},_handleStatusCssClasses:function(e,t){t.addSuccessCssClassToField&&e.removeClass(t.addSuccessCssClassToField),t.addFailureCssClassToField&&e.removeClass(t.addFailureCssClassToField),t.addSuccessCssClassToField&&!t.isError&&e.addClass(t.addSuccessCssClassToField),t.addFailureCssClassToField&&t.isError&&e.addClass(t.addFailureCssClassToField)},_getErrorMessage:function(n,r,i,s,o,u,a){var f=jQuery.inArray(i,s);if(i==="custom"||i==="funcCall"){var l=s[f+1];i=i+"["+l+"]",delete s[f]}var c=i,h=r.attr("data-validation-engine")?r.attr("data-validation-engine"):r.attr("class"),p=h.split(" "),d;i=="future"||i=="past"||i=="maxCheckbox"||i=="minCheckbox"?d=a(n,r,s,o,u):d=a(r,s,o,u);if(d!=undefined){var v=t._getCustomErrorMessage(e(r),p,c,u);v&&(d=v)}return d},_getCustomErrorMessage:function(e,n,r,i){var s=!1,o=t._validityProp[r];if(o!=undefined){s=e.attr("data-errormessage-"+o);if(s!=undefined)return s}s=e.attr("data-errormessage");if(s!=undefined)return s;var u="#"+e.attr("id");if(typeof i.custom_error_messages[u]!="undefined"&&typeof i.custom_error_messages[u][r]!="undefined")s=i.custom_error_messages[u][r].message;else if(n.length>0)for(var a=0;a<n.length&&n.length>0;a++){var f="."+n[a];if(typeof i.custom_error_messages[f]!="undefined"&&typeof i.custom_error_messages[f][r]!="undefined"){s=i.custom_error_messages[f][r].message;break}}return!s&&typeof i.custom_error_messages[r]!="undefined"&&typeof i.custom_error_messages[r]["message"]!="undefined"&&(s=i.custom_error_messages[r].message),s},_validityProp:{required:"value-missing",custom:"custom-error",groupRequired:"value-missing",ajax:"custom-error",minSize:"range-underflow",maxSize:"range-overflow",min:"range-underflow",max:"range-overflow",past:"type-mismatch",future:"type-mismatch",dateRange:"type-mismatch",dateTimeRange:"type-mismatch",maxCheckbox:"range-overflow",minCheckbox:"range-underflow",equals:"pattern-mismatch",funcCall:"custom-error",creditCard:"pattern-mismatch",condRequired:"value-missing"},_required:function(t,n,r,i,s){switch(t.prop("type")){case"text":case"password":case"textarea":case"file":case"select-one":case"select-multiple":default:var o=e.trim(t.val()),u=e.trim(t.attr("data-validation-placeholder")),a=e.trim(t.attr("placeholder"));if(!o||u&&o==u||a&&o==a)return i.allrules[n[r]].alertText;break;case"radio":case"checkbox":if(s){if(!t.attr("checked"))return i.allrules[n[r]].alertTextCheckboxMultiple;break}var f=t.closest("form, .validationEngineContainer"),l=t.attr("name");if(f.find("input[name='"+l+"']:checked").size()==0)return f.find("input[name='"+l+"']:visible").size()==1?i.allrules[n[r]].alertTextCheckboxe:i.allrules[n[r]].alertTextCheckboxMultiple}},_groupRequired:function(n,r,i,s){var o="["+s.validateAttribute+"*="+r[i+1]+"]",u=!1;n.closest("form, .validationEngineContainer").find(o).each(function(){if(!t._required(e(this),r,i,s))return u=!0,!1});if(!u)return s.allrules[r[i]].alertText},_custom:function(e,t,n,r){var i=t[n+1],s=r.allrules[i],o;if(!s){alert("jqv:custom rule not found - "+i);return}if(s.regex){var u=s.regex;if(!u){alert("jqv:custom regex not found - "+i);return}var a=new RegExp(u);if(!a.test(e.val()))return r.allrules[i].alertText}else{if(!s.func){alert("jqv:custom type not allowed "+i);return}o=s.func;if(typeof o!="function"){alert("jqv:custom parameter 'function' is no function - "+i);return}if(!o(e,t,n,r))return r.allrules[i].alertText}},_funcCall:function(e,t,n,r){var i=t[n+1],s;if(i.indexOf(".")>-1){var o=i.split("."),u=window;while(o.length)u=u[o.shift()];s=u}else s=window[i]||r.customFunctions[i];if(typeof s=="function")return s(e,t,n,r)},_equals:function(t,n,r,i){var s=n[r+1];if(t.val()!=e("#"+s).val())return i.allrules.equals.alertText},_maxSize:function(e,t,n,r){var i=t[n+1],s=e.val().length;if(s>i){var o=r.allrules.maxSize;return o.alertText+i+o.alertText2}},_minSize:function(e,t,n,r){var i=t[n+1],s=e.val().length;if(s<i){var o=r.allrules.minSize;return o.alertText+i+o.alertText2}},_min:function(e,t,n,r){var i=parseFloat(t[n+1]),s=parseFloat(e.val());if(s<i){var o=r.allrules.min;return o.alertText2?o.alertText+i+o.alertText2:o.alertText+i}},_max:function(e,t,n,r){var i=parseFloat(t[n+1]),s=parseFloat(e.val());if(s>i){var o=r.allrules.max;return o.alertText2?o.alertText+i+o.alertText2:o.alertText+i}},_past:function(n,r,i,s,o){var u=i[s+1],a=e(n.find("input[name='"+u.replace(/^#+/,"")+"']")),f;if(u.toLowerCase()=="now")f=new Date;else if(undefined!=a.val()){if(a.is(":disabled"))return;f=t._parseDate(a.val())}else f=t._parseDate(u);var l=t._parseDate(r.val());if(l>f){var c=o.allrules.past;return c.alertText2?c.alertText+t._dateToString(f)+c.alertText2:c.alertText+t._dateToString(f)}},_future:function(n,r,i,s,o){var u=i[s+1],a=e(n.find("input[name='"+u.replace(/^#+/,"")+"']")),f;if(u.toLowerCase()=="now")f=new Date;else if(undefined!=a.val()){if(a.is(":disabled"))return;f=t._parseDate(a.val())}else f=t._parseDate(u);var l=t._parseDate(r.val());if(l<f){var c=o.allrules.future;return c.alertText2?c.alertText+t._dateToString(f)+c.alertText2:c.alertText+t._dateToString(f)}},_isDate:function(e){var t=new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/);return t.test(e)},_isDateTime:function(e){var t=new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/);return t.test(e)},_dateCompare:function(e,t){return new Date(e.toString())<new Date(t.toString())},_dateRange:function(e,n,r,i){if(!i.firstOfGroup[0].value&&i.secondOfGroup[0].value||i.firstOfGroup[0].value&&!i.secondOfGroup[0].value)return i.allrules[n[r]].alertText+i.allrules[n[r]].alertText2;if(!t._isDate(i.firstOfGroup[0].value)||!t._isDate(i.secondOfGroup[0].value))return i.allrules[n[r]].alertText+i.allrules[n[r]].alertText2;if(!t._dateCompare(i.firstOfGroup[0].value,i.secondOfGroup[0].value))return i.allrules[n[r]].alertText+i.allrules[n[r]].alertText2},_dateTimeRange:function(e,n,r,i){if(!i.firstOfGroup[0].value&&i.secondOfGroup[0].value||i.firstOfGroup[0].value&&!i.secondOfGroup[0].value)return i.allrules[n[r]].alertText+i.allrules[n[r]].alertText2;if(!t._isDateTime(i.firstOfGroup[0].value)||!t._isDateTime(i.secondOfGroup[0].value))return i.allrules[n[r]].alertText+i.allrules[n[r]].alertText2;if(!t._dateCompare(i.firstOfGroup[0].value,i.secondOfGroup[0].value))return i.allrules[n[r]].alertText+i.allrules[n[r]].alertText2},_maxCheckbox:function(e,t,n,r,i){var s=n[r+1],o=t.attr("name"),u=e.find("input[name='"+o+"']:checked").size();if(u>s)return i.showArrow=!1,i.allrules.maxCheckbox.alertText2?i.allrules.maxCheckbox.alertText+" "+s+" "+i.allrules.maxCheckbox.alertText2:i.allrules.maxCheckbox.alertText},_minCheckbox:function(e,t,n,r,i){var s=n[r+1],o=t.attr("name"),u=e.find("input[name='"+o+"']:checked").size();if(u<s)return i.showArrow=!1,i.allrules.minCheckbox.alertText+" "+s+" "+i.allrules.minCheckbox.alertText2},_creditCard:function(e,t,n,r){var i=!1,s=e.val().replace(/ +/g,"").replace(/-+/g,""),o=s.length;if(o>=14&&o<=16&&parseInt(s)>0){var u=0,n=o-1,a=1,f,l=new String;do f=parseInt(s.charAt(n)),l+=a++%2==0?f*2:f;while(--n>=0);for(n=0;n<l.length;n++)u+=parseInt(l.charAt(n));i=u%10==0}if(!i)return r.allrules.creditCard.alertText},_ajax:function(n,r,i,s){var o=r[i+1],u=s.allrules[o],a=u.extraData,f=u.extraDataDynamic,l={fieldId:n.attr("id"),fieldValue:n.val()};if(typeof a=="object")e.extend(l,a);else if(typeof a=="string"){var c=a.split("&");for(var i=0;i<c.length;i++){var h=c[i].split("=");h[0]&&h[0]&&(l[h[0]]=h[1])}}if(f){var p=[],d=String(f).split(",");for(var i=0;i<d.length;i++){var v=d[i];if(e(v).length){var m=n.closest("form, .validationEngineContainer").find(v).val(),g=v.replace("#","")+"="+escape(m);l[v.replace("#","")]=m}}}s.eventTrigger=="field"&&delete s.ajaxValidCache[n.attr("id")];if(!s.isError&&!t._checkAjaxFieldStatus(n.attr("id"),s))return e.ajax({type:s.ajaxFormValidationMethod,url:u.url,cache:!1,dataType:"json",data:l,field:n,rule:u,methods:t,options:s,beforeSend:function(){},error:function(e,n){t._ajaxError(e,n)},success:function(r){var i=r[0],o=e("#"+i).eq(0);if(o.length==1){var a=r[1],f=r[2];if(!a){s.ajaxValidCache[i]=!1,s.isError=!0;if(f){if(s.allrules[f]){var l=s.allrules[f].alertText;l&&(f=l)}}else f=u.alertText;s.showPrompts&&t._showPrompt(o,f,"",!0,s)}else{s.ajaxValidCache[i]=!0;if(f){if(s.allrules[f]){var l=s.allrules[f].alertTextOk;l&&(f=l)}}else f=u.alertTextOk;s.showPrompts&&(f?t._showPrompt(o,f,"pass",!0,s):t._closePrompt(o)),s.eventTrigger=="submit"&&n.closest("form").submit()}}o.trigger("jqv.field.result",[o,s.isError,f])}}),u.alertTextLoad},_ajaxError:function(e,t){e.status==0&&t==null?alert("The page is not served from a server! ajax call failed"):typeof console!="undefined"&&console.log("Ajax error: "+e.status+" "+t)},_dateToString:function(e){return e.getFullYear()+"-"+(e.getMonth()+1)+"-"+e.getDate()},_parseDate:function(e){var t=e.split("-");return t==e&&(t=e.split("/")),t==e?(t=e.split("."),new Date(t[2],t[1]-1,t[0])):new Date(t[0],t[1]-1,t[2])},_showPrompt:function(n,r,i,s,o,u){var a=t._getPrompt(n);u&&(a=!1),e.trim(r)&&(a?t._updatePrompt(n,a,r,i,s,o):t._buildPrompt(n,r,i,s,o))},_buildPrompt:function(n,r,i,s,o){var u=e("<div>");u.addClass(t._getClassName(n.attr("id"))+"formError"),u.addClass("parentForm"+t._getClassName(n.closest("form, .validationEngineContainer").attr("id"))),u.addClass("formError");switch(i){case"pass":u.addClass("greenPopup");break;case"load":u.addClass("blackPopup");break;default:}s&&u.addClass("ajaxed");var a=e("<div>").addClass("formErrorContent").html(r).appendTo(u),f=n.data("promptPosition")||o.promptPosition;if(o.showArrow){var l=e("<div>").addClass("formErrorArrow");if(typeof f=="string"){var c=f.indexOf(":");c!=-1&&(f=f.substring(0,c))}switch(f){case"bottomLeft":case"bottomRight":u.find(".formErrorContent").before(l),l.addClass("formErrorArrowBottom").html('<div class="line1"><!-- --></div><div class="line2"><!-- --></div><div class="line3"><!-- --></div><div class="line4"><!-- --></div><div class="line5"><!-- --></div><div class="line6"><!-- --></div><div class="line7"><!-- --></div><div class="line8"><!-- --></div><div class="line9"><!-- --></div><div class="line10"><!-- --></div>');break;case"topLeft":case"topRight":l.html('<div class="line10"><!-- --></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div><div class="line2"><!-- --></div><div class="line1"><!-- --></div>'),u.append(l)}}o.addPromptClass&&u.addClass(o.addPromptClass);var h=n.attr("data-required-class");if(h!==undefined)u.addClass(h);else if(o.prettySelect&&e("#"+n.attr("id")).next().is("select")){var p=e("#"+n.attr("id").substr(o.usePrefix.length).substring(o.useSuffix.length)).attr("data-required-class");p!==undefined&&u.addClass(p)}u.css({opacity:0}),f==="inline"?(u.addClass("inline"),typeof n.attr("data-prompt-target")!="undefined"&&e("#"+n.attr("data-prompt-target")).length>0?u.appendTo(e("#"+n.attr("data-prompt-target"))):n.after(u)):n.before(u);var c=t._calculatePosition(n,u,o);return u.css({position:f==="inline"?"relative":"absolute",top:c.callerTopPosition,left:c.callerleftPosition,marginTop:c.marginTopSize,opacity:0}).data("callerField",n),o.autoHidePrompt&&setTimeout(function(){u.animate({opacity:0},function(){u.closest(".formErrorOuter").remove(),u.remove()})},o.autoHideDelay),u.animate({opacity:.87})},_updatePrompt:function(e,n,r,i,s,o,u){if(n){typeof i!="undefined"&&(i=="pass"?n.addClass("greenPopup"):n.removeClass("greenPopup"),i=="load"?n.addClass("blackPopup"):n.removeClass("blackPopup")),s?n.addClass("ajaxed"):n.removeClass("ajaxed"),n.find(".formErrorContent").html(r);var a=t._calculatePosition(e,n,o),f={top:a.callerTopPosition,left:a.callerleftPosition,marginTop:a.marginTopSize};u?n.css(f):n.animate(f)}},_closePrompt:function(e){var n=t._getPrompt(e);n&&n.fadeTo("fast",0,function(){n.parent(".formErrorOuter").remove(),n.remove()})},closePrompt:function(e){return t._closePrompt(e)},_getPrompt:function(n){var r=e(n).closest("form, .validationEngineContainer").attr("id"),i=t._getClassName(n.attr("id"))+"formError",s=e("."+t._escapeExpression(i)+".parentForm"+r)[0];if(s)return e(s)},_escapeExpression:function(e){return e.replace(/([#;&,\.\+\*\~':"\!\^$\[\]\(\)=>\|])/g,"\\$1")},isRTL:function(t){var n=e(document),r=e("body"),i=t&&t.hasClass("rtl")||t&&(t.attr("dir")||"").toLowerCase()==="rtl"||n.hasClass("rtl")||(n.attr("dir")||"").toLowerCase()==="rtl"||r.hasClass("rtl")||(r.attr("dir")||"").toLowerCase()==="rtl";return Boolean(i)},_calculatePosition:function(e,t,n){var r,i,s,o=e.width(),u=e.position().left,a=e.position().top,f=e.height(),l=t.height();r=i=0,s=-l;var c=e.data("promptPosition")||n.promptPosition,h="",p="",d=0,v=0;typeof c=="string"&&c.indexOf(":")!=-1&&(h=c.substring(c.indexOf(":")+1),c=c.substring(0,c.indexOf(":")),h.indexOf(",")!=-1&&(p=h.substring(h.indexOf(",")+1),h=h.substring(0,h.indexOf(",")),v=parseInt(p),isNaN(v)&&(v=0)),d=parseInt(h),isNaN(h)&&(h=0));switch(c){default:case"topRight":i+=u+o-30,r+=a;break;case"topLeft":r+=a,i+=u;break;case"centerRight":r=a+4,s=0,i=u+e.outerWidth(!0)+5;break;case"centerLeft":i=u-(t.width()+2),r=a+4,s=0;break;case"bottomLeft":r=a+e.height()+5,s=0,i=u;break;case"bottomRight":i=u+o-30,r=a+e.height()+5,s=0;break;case"inline":i=0,r=0,s=0}return i+=d,r+=v,{callerTopPosition:r+"px",callerleftPosition:i+"px",marginTopSize:s+"px"}},_saveOptions:function(t,n){if(e.validationEngineLanguage)var r=e.validationEngineLanguage.allRules;else e.error("jQuery.validationEngine rules are not loaded, plz add localization files to the page");e.validationEngine.defaults.allrules=r;var i=e.extend(!0,{},e.validationEngine.defaults,n);return t.data("jqv",i),i},_getClassName:function(e){if(e)return e.replace(/:/g,"_").replace(/\./g,"_")},_jqSelector:function(e){return e.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g,"\\$1")},_condRequired:function(e,n,r,i){var s,o;for(s=r+1;s<n.length;s++){o=jQuery("#"+n[s]).first();if(o.length&&t._required(o,["required"],0,i,true)==undefined)return t._required(e,["required"],0,i)}},_submitButtonClick:function(t){var n=e(this),r=n.closest("form, .validationEngineContainer");r.data("jqv_submitButton",n.attr("id"))}};e.fn.validationEngine=function(n){var r=e(this);if(!r[0])return r;if(typeof n=="string"&&n.charAt(0)!="_"&&t[n])return n!="showPrompt"&&n!="hide"&&n!="hideAll"&&t.init.apply(r),t[n].apply(r,Array.prototype.slice.call(arguments,1));if(typeof n=="object"||!n)return t.init.apply(r,arguments),t.attach.apply(r);e.error("Method "+n+" does not exist in jQuery.validationEngine")},e.validationEngine={fieldIdCounter:0,defaults:{validationEventTrigger:"blur",scroll:!0,focusFirstField:!0,showPrompts:!0,validateNonVisibleFields:!1,promptPosition:"topRight",bindMethod:"bind",inlineAjax:!1,ajaxFormValidation:!1,ajaxFormValidationURL:!1,ajaxFormValidationMethod:"get",onAjaxFormComplete:e.noop,onBeforeAjaxFormValidation:e.noop,onValidationComplete:!1,doNotShowAllErrosOnSubmit:!1,custom_error_messages:{},binded:!0,showArrow:!1,isError:!1,maxErrorsPerField:!1,ajaxValidCache:{},autoPositionUpdate:!1,InvalidFields:[],onFieldSuccess:!1,onFieldFailure:!1,onSuccess:!1,onFailure:!1,validateAttribute:"class",addSuccessCssClassToField:"",addFailureCssClassToField:"",autoHidePrompt:!1,autoHideDelay:1e4,fadeDuration:.3,prettySelect:!1,addPromptClass:"",usePrefix:"",useSuffix:"",showOneMessage:!1}},e(function(){e.validationEngine.defaults.promptPosition=t.isRTL()?"topLeft":"topRight"})})(jQuery);
