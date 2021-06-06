<html>

    <head>
    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>



        <script src="assets/js/index.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
        <script src="assets/js/attaches.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/simple-image@latest"></script><!-- Image -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script><!-- Image -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script><!-- Delimiter -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script><!-- List -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest"></script><!-- Checklist -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script><!-- Quote -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/code@latest"></script><!-- Code -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script><!-- Embed -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script><!-- Table -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/link@latest"></script><!-- Link -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script><!-- Warning -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script><!-- Marker -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/inline-code@latest"></script><!-- Inline Code -->
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/raw"></script>

        <script src="assets/js/utils.js"></script>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
        <link rel="stylesheet" href="assets/css/index.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" crossorigin="anonymous"/>
        
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    </head>

    <body>

        <div class="grid-container">

            <div class="sidenav">
                <div id="userSquare">
                    <a id="logout" href="#"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
                    <a href="#" id="newProc"><i class="fas fa-plus"></i></a>
                    <img id="userLogo" src="assets/img/user-icon.svg">
                    <p id="username"></p>
                </div>

                <div id="explorer">

                    <input id="search" class="txtbox"></input>

                    <div id="catProcExplorer" class="list-group list-group-root"></div>


                </div>
            </div>

            <div class="main">
                <div id="infoProc">
                    <select id="versionProc" class="txtbox">
                        <option value="">/</option>
                    </select>
                </div>

                <div id="editor"></div>

                <div id="actionBox">
                    <a id="updateProc"    href="#"><i class="fas fa-save"></i></a>
                    <a id="deleteVersion" href="#"><i class="fas fa-trash-alt"></i></a>
                    <a id="shareProc"     href="#"><i class="fas fa-share-alt"></i></a>
                    <a id="categoryProc"  href="#"><i class="fas fa-file-spreadsheet"></i></a>
                </div>
            </div>
        
    
    
            <div class="modal fade" tabindex="-1" role="dialog" id="shareModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color: black">Share this sheet to yours friends</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="color: black">
                        

                        <table class="table table-striped" id="sheetSharedTable">
                            <thead>
                                <tr>
                                <th scope="col">User</th>
                                <th scope="col">Role</th>
                                <th scope="col">Options</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            </table>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>
    
            <div class="modal fade" tabindex="-1" role="dialog" id="categoryModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" style="color: black">Add category to your sheet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="color: black">
                        

                        <table class="table table-striped" id="sheetCategoriesTable">
                            <thead>
                                <tr>
                                <th scope="col">Category</th>
                                <th scope="col">Options</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            </table>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                </div>
            </div>

    </body>

    <script type="text/javascript">

         $(document).ready(function(){


            if (!getCurrentUser())
            {       
                document.location.href = 'login.php';
            }

            $('#shareProc').on('click', function()
            {
                $('#shareModal').modal('show');
            });

            $('#categoryProc').on('click', function()
            {
                $('#categoryModal').modal('show');
            });

            $("#logout").on('click', function(e) {
                logout(getCookie('token'));
                deleteCookie('token');
                deleteCookie('user');
                document.location = 'login.php';
            });

            $("#clear").click(function(e) {
                makeEditor({});
            });

            $('.list-group-item').on('click', function() {
                $('.fas', this)
                    .toggleClass('fas fa-chevron-right')
                    .toggleClass('fas fa-chevron-down');                
            });

            $('#versionProc').on('change', function() {
                makeEditor(
                    getProc(currentProc, $(this).val())
                );   
            });


            $("#newProc").click(function(e) {
                $("#versionProc").empty();
                newProc($('#search').val(), function(data) {
                    currentProc            = data;
                    currentVersion         = 1;

                    setupAutoCompleteOnSearch();
                    updateComboVersion();
                    giveRole(currentProc, getCurrentUserID(), "owner");
                });
            });


            $('#updateProc').on('click', function() {

                updateProc(currentProc, $('#search').val(),function(data) {
                    currentVersion++;                    
                    updateComboVersion();
                    setupAutoCompleteOnSearch();
                });      
                
                
            });

            $('#deleteVersion').on('click', function() {
                let nbVersions = $('#versionProc > option').length;
                if (nbVersions > 1)
                {
                    deleteVersion($('#versionProc').val(), function(data)
                    {
                        currentVersion--;
                        updateComboVersion();
                        setupAutoCompleteOnSearch();
                        makeEditor(
                            getProc(currentProc, currentVersion)
                        );  
                    });
                    
                } else {
                    deleteSheet(currentProc)
                }
            });

            $(document).on('click','.linkedSheet',function(e) {

                currentProc = $(this).attr('id');
                let currentVersionAr = sheetVersions(currentProc);

                currentVersion = currentVersionAr[currentVersionAr.length-1][0];
                let sheetContent = getProc(currentProc, currentVersion);

                makeEditor(sheetContent);

                setupAutoCompleteOnSearch();
                updateComboVersion();
                showOptionsByRights();
                populateSharesTable();
                populateCategoryTable();
            });

    
            $(document).on('click','.revokeRight',function(e) {
                revokeRight(currentProc, this.id)
                $("#rr"+this.id).remove();
            });
  
            $(document).on('click','#newRight',function(e) {
                let dra = `<tr>
                            <td>${selectUser()}</td>
                            <td>${selectRole()}</td>
                            <td><button type="button" class="btn btn-secondary addNewRight">Add</button></td>
                    </tr>`
                $('#sheetSharedTable > tbody').after(dra);
            });
            
            $(document).on('click','.addNewRight',function(e) {
                let uid = $(this).closest('tr').find('.userSelect').children(":selected").attr("value");
                let rid = $(this).closest('tr').find('.roleSelect').children(":selected").attr("value");
                giveSheetUserRole(currentProc, uid, rid)
                populateSharesTable();
                
            });

            $(document).on('click','.revokeCategory',function(e) {
                console.log(currentProc);
                console.log(this.id);
                revokeCategory(currentProc, this.id)
                $("#rr"+this.id).remove();
            });
  
            $(document).on('click','#newCategory',function(e) {
                let dra = `<tr>
                            <td>${selectCategory()}</td>
                            <td><button type="button" class="btn btn-secondary addNewCategory">Add</button></td>
                    </tr>`
                $('#sheetCategoriesTable > tbody').after(dra);
            });
            
            $(document).on('click','.addNewCategory',function(e) {
                let cid = $(this).closest('tr').find('.categorySelect').children(":selected").attr("value")
                addNewCategory(currentProc, cid);
                populateCategoryTable();
                
            });

            init();
            

         });
    </script>
</html>