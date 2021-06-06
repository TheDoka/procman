<?php


        
   
?>

<html>
    <head>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

        <script src="assets/js/index.js"></script>
        <script src="assets/js/utils.js"></script>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/index.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" crossorigin="anonymous"/>
        
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    </head>

    <body>


        <div role="document" style="width: 80%; margin: auto; height: 80%;">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: black; font-weight: bold;">Roles</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="color: black">
                    

                    <table class="table table-striped" id="rolesTable">
                        <thead>
                            <tr>
                            <th scope="col">Role</th>
                            <th scope="col">Read</th>
                            <th scope="col">Write</th>
                            <th scope="col">Delete</th>
                            <th scope="col">Share</th>
                            <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        </table>


                </div>
                <div class="modal-footer">
                </div>
                </div>
            </div>

    </body>

    <script>

    
        $(document).ready(function(){
    
            init();

            function init()
            {
                 populateRoles();
                 console.log(roles);
            }


            function populateRoles()
            {
                let dra = "";
                $('#rolesTable > tr').remove(); 
                getRoles().forEach(role => {

                            dra += `<tr>
                                    <td><input class="form-control rName" value="${role.name}"></td>
                                    <td><input class="form-check-input rcanRead" type="checkbox" value="" ${role.canRead == 1 ? "checked" : ""}></td>
                                    <td><input class="form-check-input rcanWrite" type="checkbox" value="" ${role.canWrite == 1 ? "checked" : ""}></td>
                                    <td><input class="form-check-input rcanDelete" type="checkbox" value="" ${role.canDelete == 1 ? "checked" : ""}></td>
                                    <td><input class="form-check-input rcanShare" type="checkbox" value="" ${role.canShare == 1 ? "checked" : ""}></td>
                                    <td style="display: flex;">
                                        <button style="width: 50%;" type="button" id="${role.id}" class="btn btn-dark applyEdit">Apply</button>
                                        ${
                                            role.reserved == 1 ? "" : `<button style="width: 50%;" type="button" id="${role.id}" class="btn btn-danger deleteRole">Delete</button>`
                                        }
                                    </td>
                            </tr>`
                });

                dra += `<tr>
                            <td colspan=6><button style="width: 100%;" type="button" class="btn btn-dark newRole">+</button></td>
                        </tr>`
                        
                $('#rolesTable > tbody').after(dra);
            }


            $(document).on('click','.newRole',function(e) {
                let dra = `<tr>
                            <td><input class="form-control rName" value=""></td>
                                    <td><input class="form-check-input rcanRead" type="checkbox" value=""}></td>
                                    <td><input class="form-check-input rcanWrite" type="checkbox" value=""}></td>
                                    <td><input class="form-check-input rcanDelete" type="checkbox" value=""}></td>
                                    <td><input class="form-check-input rcanShare" type="checkbox" value=""}></td>
                                    <td style="display: flex;">
                                        <button style="width: 50%;" type="button" id="" class="btn btn-dark confirmNewRole">Add</button>
                                        <button style="width: 50%;" type="button" id="" class="btn btn-danger deleteNewRole">Delete</button>
                                    </td>
                    </tr>`
                $('#rolesTable > tbody').after(dra);
            });
            
            function getCurrentEditRole(e)
            {
                return {
                    id: e.id,
                    name : $(e).closest('tr').find('.rName').val(),
                    canRead: $(e).closest('tr').find('.rcanRead').is(":checked") ? 1 : 0,
                    canWrite: $(e).closest('tr').find('.rcanWrite').is(":checked") ? 1 : 0,
                    canDelete: $(e).closest('tr').find('.rcanDelete').is(":checked") ? 1 : 0,
                    canShare: $(e).closest('tr').find('.rcanShare').is(":checked") ? 1 : 0
                };
            }

            $(document).on('click','.confirmNewRole',function(e) {
                let v = getCurrentEditRole(this);
                console.log(v);
                newRole(v);
                populateRoles();
                
            });



            $(document).on('click','.applyEdit',function(e) {
                
                updateRole(getCurrentEditRole(this));
                populateRoles();
                
            });

               
            $(document).on('click','.deleteRole',function(e) {

                deleteRole(this.id);
                populateRoles();
                
            });
            
            $(document).on('click','.deleteNewRole',function(e) {
                
                $(this).closest('tr').remove();
                
            });



        });

   

    </script>

</html>

