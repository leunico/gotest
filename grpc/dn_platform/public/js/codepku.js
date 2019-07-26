/**
 * Common functions and variables.
 * @author  Park Lam
 */

/**
 * Usage:
 * Code: "helloworld".startsWith("hell")
 * Result: true
 */
if(!String.prototype.startsWith) {
    String.prototype.startsWith = function(str) {
        return this.substring(0, str.length) === str;
    };
}

/**
 * Usage:
 * Code: "{0} is the {1} {2}.".format("Park Lam", "most", "handsome guy")
 * Result: "Park Lam is the most handsome guy."
 */
if(!String.prototype.format) {
    String.prototype.format = function() {
        if(arguments.length === 0) return this;
        var args = arguments;
        return this.replace(/\{(\d+)\}/g, function(match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };
}

/**
 * Usage:
 * Code: "    content here     ".trim();
 * Result: "content here"
 */
if(!String.prototype.trim) {
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/gm, '');
    };
}

/**
 * Usage:
 * a = ["a", "b"]
 * b = ["a", "b"]
 * a.equals(b) == true
 */
if(!Array.prototype.equals) {
    Array.prototype.equals = function(array) {
        if(!array) {
            return false;
        }

        if(this.length != array.length) {
            return false;
        }

        for(var i = 0, l = this.length; i < l; i++) {
            if(this[i] instanceof Array && array[i] instanceof Array) {
                if(!this[i].equals(array[i])) {
                    return false;
                }
            } else if(this[i] != array[i]) {
                return false;
            }
        }
        return true;
    };
}

/**
 * Usage:
 * ["key1", "value2", "key3", "value4"].filter(
 *      function(item, index, arr) {
 *          return item.startsWith("value");
 *      });
 * Result: ["value2", "value4"]
 */
if(!Array.prototype.filter) {
    Array.prototype.filter = function(func) {
        if(this === 0 || this === null || typeof func != "function") {
            throw new TypeError();
        }

        var t = this;
        var len = t.length;

        var res = [];
        var thisp = arguments[1];
        for(var i = 0; i < len; i++) {
            if(i in t) {
                var val = t[i];
                if(func.call(thisp, val, i, t)){
                    res.push(val);
                }
            }
        }

        return res;
    };
}
/**
 * Usage:
 * ["key1", "value2", "key3", "value4"].foreach(
 *      function(item, index, arr) {
 *          console.info(item);
 *      });
 * Result:  "key1"
 *          "value2"
 *          "key3"
 *          "value4"
 */
if(!Array.prototype.foreach) {
    Array.prototype.foreach = function(func) {
        if(this === 0 || this === null || typeof func != "function") {
            throw new TypeError();
        }

        var t = Object(this);
        var len = t.length;

        var thisp = arguments[1];
        for(var i = 0; i < len; i++) {
            if(i in t) {
                var val = t[i];
                func.call(thisp, val, i, t);
            }
        }
    };
}
/**
 * Usage:
 * ["key1", "value2", "key3", "value4"].choice(2);
 * Result:  ["value4", "key1"]
 */
if(!Array.prototype.choice) {
    Array.prototype.choice = function(count) {
        if(count > this.length) {
            count = this.length;
        }

        var copy = this.concat([]);
        var result = [];
        while(result.length < count) {
            result.concat(copy.splice(codepku.randint(0, copy.length - 1), 1));
        }
        return result;
    };
}

/**
 * Usage:
 * Array.isArray([]); // true
 * Array.isArray({}); // false
 */
if(!Array.isArray) {
    Array.isArray = function(obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    };
}

/**
 * Usage:
 * Date.now()
 * Result: Current timestamp in milliseconds.
 */
if(!Date.now) {
    Date.now = function() {
        return +(new Date());
    };
}
var codepku = {};
/**
 * Purify object. Keep only properties.
 * Usage:
 * codepku.purify(window.location)
 */
codepku.purify = function(o) {
    return JSON.parse(JSON.stringify(o));
};

/**
 * Override 'typeof' function. It solved the typeof([]) returns 'object' problem.
 * Usage:
 * codepku.typeOf([]); // returns: 'array'
 */
codepku.typeOf = function(o) {
    return Object.prototype.toString.call(o).slice(8, -1).toLowerCase();
};

/**
 * Redirect to url.
 * Usage:
 * codepku.redirect("https://www.codepku.com/");
 */
codepku.redirect = function(url) {
    // for IE compatible
    setTimeout(function() { window.location.href = url; }, 0);
};

/**
 * Usage:
 * codepku.query("https://www.codepku.com/?debug=1&email=support@codepku.com");
 * result: "debug=1&email=support@codepku.com"
 */
codepku.query = function(url) {
    return url.replace(/^[^\?]+\??/, "");
};

/**
 * Usage:
 * codepku.parse("draft=1&debug=1&name=testcase");
 * result: { draft: "1", debug: "1", name: "testcase" }
 */
codepku.parse = function(q) {
    var params = {};
    q.split("&").foreach(
        function(item, index, arr) {
            item = item.split("=");
            key = item[0].trim();
            value = item[1].trim();
            if(params.hasOwnProperty(key)) {
                if(Array.isArray(params[key])) {
                    params[key].push(value);
                } else {
                    params[key] = [ params[key], value ];
                }
            } else {
                params[key] = value;
            }
        });
    return params;
};

/**
 * Get parameter by name from current views url query.
 *
 * Usage:
 * Page URL: https://www.example.com/?email=admin@example.com
 * var email = codepku.getUrlParameter("email");
 */
codepku.getUrlParameter = function(name) {
    return decodeURIComponent((new RegExp('[?|&]{0}=([^&;]+?)(&|#|;|$)'.format(name)).exec(window.location.search)||[null,''])[1].replace(/\+/g, '%20')) || null;
};

/**
 * Get parameter by name from current views url hash.
 *
 * Usage:
 * Page URL: https://www.example.com/#email=admin@example.com
 * var email = codepku.getUrlSegment("email");
 */
codepku.getUrlSegment = function(name) {
    return decodeURIComponent((new RegExp('[#|&]{0}=([^&;]+?)(&|#|;|$)'.format(name)).exec(window.location.hash)||[null,''])[1].replace(/\+/g, '%20')) || null;
};

/**
 * Redirect to HTTPS automatically if current protocol is HTTP.
 *
 * Usage:
 * codepku.forceHttps();
 */
codepku.forceHttps = function() {
    if(window.location.protocol != 'https:') {
        codepku.redirect('https:' + window.location.href.substring(window.location.protocol.length));
    }
};

/**
 * Get value by name from cookie.
 *
 * Usage:
 * var token = codepku.getCookie("token", "default_value_here");
 */
codepku.getCookie = function(name) {
    if(typeof name !== 'string') {
        throw new TypeError();
    }
    return (document.cookie.split(';').filter( function(item, index, arr) { return item.trim().split('=').shift() == name; }).shift() || "").split('=').pop();
};

/**
 * Set value to cookie.
 *
 * Usage:
 * codepku.setCookie("token", "token_value", 7);
 */
codepku.setCookie = function(name, value, days) {
    if(typeof name !== 'string') {
        throw new TypeError();
    }
    document.cookie = "{0}={1};expires={3};path=/".format(name.trim(), (value || "").trim(), new Date(Date.now() + days * 24 * 3600 * 1000).toUTCString());
};

/**
 * Generate random integer in range.
 *
 * Usage:
 * ingcrations.randint(10, 20);
 */
codepku.randint = function(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
};

codepku.hasRole = function(code){
	var permission = localStorage.getItem('permissions');
	if(permission){
		permission = permission.split(',');
		if(permission.indexOf(code)>-1){
			return true;
		}
		return false;
	}
	return false;
};

/**
 * get json remote
 */

codepku.getJson = function(url){
    return new Promise((resolve, reject)=>{
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'json';
        xhr.onload = function(){
            var status = xhr.status;
            if(status == 200){
                resolve(xhr.response);
            }else{
                reject(xhr.response);
            }
        };
        xhr.send();
    });
};
/*
* 金钱格式化
 */
codepku.formatMoney = (money,n) => {
    n = n > 0 && n <= 20 ? n : 2;
    money = parseFloat((money + "").replace(/[^\d\.-]/g, "")).toFixed(n) + "";
    let l = money.split(".")[0].split("").reverse(),
        r = money.split(".")[1];
    let t = "";
    for (let i = 0; i < l.length; i ++ ) {
        t += l[i] + ((i + 1) % 3 === 0 && (i + 1) !== l.length ? "," : "");
    }
    return '￥' + t.split("").reverse().join("") + "." + r;
};

/**
 * 动态获取本地文件
 */
codepku.openFile=function(){
    var fileInput = document.createElement("input");
    fileInput.setAttribute('type', 'file');
    fileInput.style.visibility = "hidden";
    document.body.appendChild(fileInput);
    fileInput.click();
    fileInput.onchange=function(event){
        console.log(event);
        document.body.removeChild(fileInput);
        return event;
    };
};

/**
 * 打开保存文件对话框
 */
codepku.saveAs = function(){
    var fileSave = new ActiveXObject("MSComDlg.CommonDialog");
    //fileSave.Filter = "";
    fileSave.FilterIndex = 2;
    fileSave.MaxFileSize = 512;
};


/**
 * Utilities for dictionary
 */
codepku.dict = {};
codepku.dict.clone = function(obj) {
    if(obj == null || typeof obj != "object") {
        return obj;
    }
    var copy = obj.constructor();
    for(var attr in obj) {
        if(obj.hasOwnProperty(attr)) {
            copy[attr] = obj[attr];
        }
    }
    return copy;
};

codepku.dict.merge = function() {
    var obj = {};
    for(var i = 0; i < arguments.length; i++) {
        if(arguments[i] == null) {
            continue;
        }
        for(var key in arguments[i]) {
            obj[key] = arguments[i][key];
        }
    }
    return obj;
};

codepku.dict.strip = function(obj, properties) {
    if(obj == null || !Array.isArray(properties)) {
        throw new TypeError();
    }
    for(var i = 0; i < properties.length; i++) {
        if(obj.hasOwnProperty(properties[i])) {
            delete obj[properties[i]];
        }
    }
    return obj;
};

codepku.dict.select = function(obj, properties) {
    if(obj == null || !Array.isArray(properties)) {
        throw new TypeError();
    }
    var result = {};
    for(var i = 0; i < properties.length; i++) {
        if(obj.hasOwnProperty(properties[i])) {
            result[properties[i]] = obj[properties[i]];
        }
    }
    return result;
};

// AMD: Asynchronous Module Definition (AMD) API
// refer to https://github.com/amdjs/amdjs-api/wiki/AMD
if(typeof define === 'function' && typeof define.amd === 'object' && define.amd) {
    define(function() {
        return codepku;
    });
} else if(typeof module !== 'undefined' && module.exports) {
    module.exports = codepku;
} else {
    window.codepku = codepku;
}

/*
* 判断是否为正整数
* */
codepku.dict.isInteger = function(number){
    return Math.round(number) == number;
};

/*
* 判断字符串是否有特殊字符
* */
codepku.dict.checkNotSpecialChar = function(params){
    var reg = new RegExp("^([\\u4e00-\\u9fa5]|([A-Za-z])|([0-9]))*$");
    if(reg.test(params)){
        return true;
    }
    return false;
};

/**
 * 用于校验用户名，孩子姓名，客户姓名可以有特殊字符\的情况
 * */
codepku.dict.checkCustomerName = function(params){
    var reg = new RegExp("^([\\u4e00-\\u9fa5]|([A-Za-z])|([0-9])|\\\\)*$");
    if(reg.test(params)){
        return true;
    }
    return false;
};

/*
* 判断字符是否为空
* */
codepku.dict.isNull = function(param){
    if(param === null || param === undefined || param === '' ){
        return true;
    }
    return false;
};

/*
* 校验电话号码
* */

codepku.dict.validateMobileNumber = function(phone){
    if(phone){
        return RegExp("^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\\d{8}$")
            .test(phone);
    }else{
        return false;
    }
};

/*
* 秒转化为小时分钟
* */
codepku.dict.formatSeconds = function(value){
    if(value > 0){
        var secondTime = parseInt(value);
        var minuteTime = 0;
        var hourTime = 0;
        if(secondTime > 60){
            minuteTime = parseInt(secondTime /60);
            secondTime = parseInt(secondTime % 60);
            if(minuteTime > 60){
                hourTime = parseInt(minuteTime /60);
                minuteTime = parseInt(minuteTime % 60);
            }
        }
        var result = parseInt(secondTime) + "秒";
        if(minuteTime >0){
            result = parseInt(minuteTime) + "分"+ result;
        }
        if(hourTime > 0){
            result = parseInt(hourTime) + "小时" + result;
        }
        return result;
    }else{
        return 0;
    }
};

/*
* 取最大值并向上取整
* */
codepku.dict.max = function(data){
    return Math.ceil(Math.max.apply(Math, data));
};

codepku.dict.min = function(data){
    return Math.floor(Math.min.apply(Math, data));
};

codepku.dict.addWebSource = function(targets){
    for(let i = 0; i < targets.length; i++){
        for(let item in targets[i]){
            if(typeof targets[i][item] == "string" && item == "md5ext" ){
                targets[i]["md5ext"] = process.env.CDN + '/scratch/media/'+targets[i]["md5ext"];
            }else if(typeof targets[i][item] == "object" && targets[i][item].length > 0){
                codepku.dict.addWebSource(targets[i][item]);
            }
        }
    }
    return targets;
};

codepku.dict.sortArr = (pro) => {
    return function (a,b) {
        let value1 = a[pro];
        let value2 = b[pro];
        return value1 - value2;
    };
};

/*
if(codepku.getUrlParams('debug') != '1') {
    codepku.forceHttps();
}
*/

function type(obj) {
    var toString = Object.prototype.toString;
    var map = {
        '[object Boolean]'  : 'boolean',
        '[object Number]'   : 'number',
        '[object String]'   : 'string',
        '[object Function]' : 'function',
        '[object Array]'    : 'array',
        '[object Date]'     : 'date',
        '[object RegExp]'   : 'regExp',
        '[object Undefined]': 'undefined',
        '[object Null]'     : 'null',
        '[object Object]'   : 'object'
    };
    return map[toString.call(obj)];
}
codepku.deepClone = function(data){
    var t = type(data), o, i, ni;

    if(t === 'array') {
        o = [];
    }else if( t === 'object') {
        o = {};
    }else {
        return data;
    }

    if(t === 'array') {
        for (i = 0, ni = data.length; i < ni; i++) {
            o.push(codepku.deepClone(data[i]));
        }
        return o;
    }else if( t === 'object') {
        for( i in data) {
            o[i] = codepku.deepClone(data[i]);
        }
        return o;
    }

};
// export default codepku;
