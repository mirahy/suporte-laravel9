const BROWSER = (function BrowserDetection() {
    //Check if browser is IE
    if (navigator.userAgent.search("MSIE") >= 0) {
        return "MSIE";
    }
    //Check if browser is Chrome
    else if (navigator.userAgent.search("Chrome") >= 0) {
        return "Chrome";
    }
    //Check if browser is Firefox
    else if (navigator.userAgent.search("Firefox") >= 0) {
        return "Firefox";
    }
    //Check if browser is Safari
    else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
        return "Safari";
    }
    //Check if browser is Opera
    else if (navigator.userAgent.search("Opera")  >= 0) {
        return "Opera";
    }
})();