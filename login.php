<html>

    <head>
    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
        <script src="assets/js/utils.js"></script>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/login.css">


        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" crossorigin="anonymous"/>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    </head>

    <body>

        <div id="sidenav">
                <div id="headerTitleText">
                    <p class="shadowyText">We love learning</p>
                    <p class="lowerHeaderText">We'll try to make it easy</p>
                </div>
                <img id="hearthPlanet" src="assets/img/hearthPlanet.svg">

                
                
        </div>

        <div id="main">


            <div class="login-form">
                <img id="lockimg" src="assets/img/lock.svg">
                <form id="loginForm">
                    <div class="form-group">
                        <input id="username" type="text" class="form-control form-dark" placeholder="theysaidthisandthat@yahoo.com" required>
                    </div>

                    <div class="form-group">
                        <input id="password" type="password" class="form-control form-dark" placeholder="Password" required>
                    </div>
                    
                    <button tabindex="-1" type="button" id="login" class="btn btn-dark w-100"><i class="fas fa-angle-right"></i></button>
                </form>

            </div>
        </div>

    </body>

    <script type="text/javascript">
         $(document).ready(function(){

            if (getCurrentUser())
            {       
                document.location.href = 'index.php';
            }

            $("#login").click(function(e) {
                if ($("#loginForm")[0].checkValidity() )
                {
                    makeLogin($('#username').val(), $('#password').val());
                } else {
                    alert('Champs');
                }
               

            });

         });

        function makeLogin(username, password)
        {

            $.post('assets/php/interface.php',
                {
                    function: 'login',
                    username: username,
                    password: password
                }, function(data) {
                    data = JSON.parse(data);
                    if (data['id'])
                    {
                        createCookie('token', data['token'], 7);
                        createCookie('user',  data['id'], 7);
                        document.location.href = 'index.php';
                    }
            });

        }


    </script>

    


</html>