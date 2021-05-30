function createCookie(name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
    
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

function deleteCookie(c_name)
{
    document.cookie = c_name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function getCurrentUserToken()
{
    return getCookie('token');
}


function getCurrentUserID()
{
    return getCookie('user');
}

/*
* Return current user data or false if user session is correct
*/
function getCurrentUser()
{
    let token   = getCurrentUserToken();
    let userID  =  getCurrentUserID();
    let ret = false;

    if (token && userID)
    {

        ret = $.ajax({
            type: "POST", 
            url: "assets/php/interface.php", 
            async: false,
            data: {
                function: 'getUser',
                userID: userID,
                token: token
            }
        }).responseText;

        // Login is correct, also, weird but AJAX may return a "false" string
        if (ret != "false")
        {
            currentUser = JSON.parse(ret);
        }
    }

    return ret;
}
