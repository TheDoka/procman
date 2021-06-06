    
    var editor;
    var currentUser;
    var currentProc;
    var currentVersion;

    var roles;
    var usersName;
    var categories;
    var allCategories;

    function init()
    {
        makeEditor({});
        this.categories = getCategories();
        this.allCategories = getAllCategories();
        categoryList();
        setupAutoCompleteOnSearch(); 

        this.roles = getRoles();
        this.usersName = getUsersName();

        $('#username').text(currentUser['username'].toUpperCase());
    }



    function logout(token)
    {
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'logout',
                token: token
            },
        });
    }


    function setupAutoCompleteOnSearch()
    {

        var t = getSheetsTitleForUser(getCurrentUserID());
        // t[0] id
        // t[1] title
        
        var source  = [ ];
        var mapping = { };
        for(var i = 0; i < t.length; ++i) {
            source.push(t[i][1]);
            mapping[t[i][1]] = t[i][0];
        }

        $( "#search" ).autocomplete({
            source: source,
            select: function( event, ui ) {
                currentProc    = mapping[ui.item.label];
                updateComboVersion();
                makeEditor(getProc(currentProc, currentVersion));  
                showOptionsByRights();
                populateSharesTable();
                populateCategoryTable();

            }
        });
    }

    function getRightsOverCurrentSheet()
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getRightsOverSheet',
                token: currentUser.token,
                sheetID: currentProc
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function getListOfAccessSheet()
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getListOfAccessSheet',
                sheetID: currentProc
            },success: function(data)
            {
                rdata = JSON.parse(data);
                console.log(rdata);
            },
        });
        return rdata;
    }
    function getUsersName()
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getUsersName'
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }
    
    function populateSharesTable()
    {
        let dra = "";
        console.log(currentUser.id);
        $('#sheetSharedTable > tr').remove(); 
        getListOfAccessSheet().forEach(user => {

                    dra += `<tr id="rr${user.uid}">
                            <td>${user.username}</td>
                            <td>${user.roleName}</td>
                            <td>
                                ${user.id != currentUser.id ? 
                                    `<button id="${user.uid}" type="button" class="btn btn-secondary revokeRight">Revoke</button>` 
                                    :
                                    ""
                                }
                            </td>
                    </tr>`
        });

        dra += `<tr>
                    <td colspan=3><button style="width: 100%;" id="newRight" type="button" class="btn btn-dark">+</button></td>
                </tr>`
                
        $('#sheetSharedTable > tbody').after(dra);
    }


    function getCategoriesSheet()
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getCategoriesSheet',
                sheetID: currentProc
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function revokeCategory(sheetID, cid)
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'revokeCategory',
                sheetID: currentProc,
                cid: cid
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function populateCategoryTable()
    {
        let dra = "";

        $('#sheetCategoriesTable > tr').remove(); 
        getCategoriesSheet().forEach(category => {

                    dra += `<tr id="rr${category.id}">
                            <td>${category.name}</td>
                            <td>
                            <button style="width: 100%;" type="button" id="${category.id}" class="btn btn-danger revokeCategory">-</button>
                            </td>
                            <td></td>
                    </tr>`
        });

        dra += `<tr>
                    <td colspan=3><button style="width: 100%;" id="newCategory" type="button" class="btn btn-dark">+</button></td>
                </tr>`

        $('#sheetCategoriesTable > tbody').after(dra);
    }

    function updateRole(role)
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'updateRole',
                role: role
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }
   
    function addNewCategory(sheetID, cid)
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'addNewCategory',
                sheetID,
                cid: cid
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }


    function deleteRole(rid)
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'deleteRole',
                rid: rid
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }
    
    function newRole(role)
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'newRole',
                role: role
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function selectRole()
    {
        let select = `<select class="form-select roleSelect">`
        roles.forEach(role => {
            select += `<option value="${role.id}">${role.name}</option>`
        });
        return select + "</select>";
    }
    

    function getAllCategories()
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getAllCategories'
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function selectCategory()
    {
        let select = `<select class="form-select categorySelect">`
        
        allCategories.forEach(category => {
    
            select += `<option value="${category.id}">${category.name}</option>`

        });

        return select + "</select>";
    }

    function selectUser()
    {
        let select = `<select class="form-select userSelect">`
        usersName.forEach(user => {
            select += `<option value="${user.id}">${user.username}</option>`
        });
        return select + "</select>";
    }

    function getRoles()
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getRoles',
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function revokeRight(sheetID, userID)
    {
        let rdata = [];
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'revokeRight',
                sheetID: sheetID,
                userID: userID,
            },success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function showOptionsByRights()
    {
        let role = getRightsOverCurrentSheet()[0];
        $('#infoProc').show();
        $('#deleteVersion').hide();
        $('#updateProc').hide();
        $('#shareProc').hide();
        $('#categoryProc').hide();
        
        if (role.canDelete == 1)
        {
            $('#deleteVersion').show();
        }
        if (role.canWrite == 1)
        {
            $('#updateProc').show();
        }
        if (role.canShare == 1)
        {
            $('#shareProc').show();
        }
        if (role.canCategory == 1)
        {
            $('#categoryProc').show();
        }
        console.log(role);
    }
   

    function updateComboVersion()
    {

        currentVersionArray  = sheetVersions(currentProc);
        currentVersion       = currentVersionArray[currentVersionArray.length-1][0];
        
        $("#versionProc").empty();
        $("#versionProc").append(new Option("Dernière version", currentVersion));

        for (let i = currentVersionArray.length-2; i >= 0; i--) {
            $("#versionProc").append(new Option(currentVersionArray[i][1], currentVersionArray[i][0]));
        }

    
    }


    function getProc(sheetID, version)
    {
        var rdata;
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getProc',
                sheetID: sheetID,
                version: version,
            },
            success: function(data)
            {
                rdata = JSON.parse(JSON.parse(data)['content']);
                // if (currentProc != sheetID)
                // {
                //     sheetVersions(sheetID);
                // }
            },
        });
        return rdata;
    }

    function sheetVersions(sheetID)
    {
        var rdata;
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getSheetVersions',
                sheetID: sheetID,
            },
            success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });

        return rdata;
    }

    function deleteSheet(sheetID)
    {
        var rdata;
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'deleteSheet',
                sheetID: sheetID,
            },
            success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });

        return rdata;
    }

    function newProc(title, callback)
    {

        editor.save().then((outputData) => {
            console.log('Article data: ', outputData)

            $.post('assets/php/interface.php',
                {
                    function: 'newProc',
                    title: title,
                    sheet: JSON.stringify(outputData),
                }, function(data) {
                    data = JSON.parse(data);
                    callback(data);
            });

        }).catch((error) => {
            console.log('Saving failed: ', error)
        });   

    }
    
    function giveSheetUserRole(sheet, user, role)
    {

        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'giveSheetUserRole',
                sheet: sheet,
                user: user,
                role: role,
            },
            success: function(data)
            {
                console.log(data);
            },
        });

    }

    function giveRole(sheet, user, role)
    {
        let idRole = 1;
        switch(role)
        {
            case "owner":   idRole = 1; break;
            case "viewer":  idRole = 2; break;
            case "editor":  idRole = 3; break;
        }
        giveSheetUserRole(sheet, user, idRole);
    }

    function makeEditor(sheetContent)
    {
        var currentHost = window.location.protocol + "//" + window.location.host + "/";
        if (editor)
        {
            editor.destroy();
        }
        editor = new EditorJS({ 
            holder: 'editor',
            placeholder: 'Charge une procédure voyonsssssssss!',
            data: sheetContent,
            tools: {
                /**
                 * Each Tool is a Plugin. Pass them via 'class' option with necessary settings {@link docs/tools.md}
                 */
                header: {
                    class: Header,
                    inlineToolbar: ['link'],
                    config: {
                        placeholder: 'Header'
                    },
                    shortcut: 'CMD+SHIFT+H'
                },
                attaches: {
                    class: AttachesTool,
                    config: {
                        endpoint: currentHost + "procman/assets/php/uploader.php"
                    }
                },
                /**
                 * Or pass class directly without any configuration
                 */
                image: {
                    class: SimpleImage,
                    inlineToolbar: ['link'],
                },
                imageUpload: {
                    class: ImageTool,
                    config: {
                        uploader: {

                            uploadByFile(file){

                              return upload(file).then((url) => {
                                  console.log(url);
                                return {
                                  success: 1,
                                  file: {
                                    url: url,
                                  }
                                };
                              });
                            }

                          }
                        }
                },
                list: {
                class: List,
                inlineToolbar: true,
                shortcut: 'CMD+SHIFT+L'
                },
                checklist: {
                class: Checklist,
                inlineToolbar: true,
                },
                quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Enter a quote',
                    captionPlaceholder: 'Quote\'s author',
                },
                shortcut: 'CMD+SHIFT+O'
                },
                warning: Warning,
                marker: {
                class:  Marker,
                shortcut: 'CMD+SHIFT+M'
                },
                code: {
                class:  CodeTool,
                shortcut: 'CMD+SHIFT+C'
                },
                delimiter: Delimiter,
                inlineCode: {
                class: InlineCode,
                shortcut: 'CMD+SHIFT+C'
                },
                linkTool: LinkTool,
                embed: Embed,
                table: {
                class: Table,
                inlineToolbar: true,
                shortcut: 'CMD+ALT+T'
                },
            },
        });

    }

    async function upload(file)
    {
        var url = "";
        let form_data = new FormData();                  
        form_data.append('file', file);
        //await fetch('assets/php/uploader.php', {method: "POST", body: form_data});  
        $.ajax({
            type: 'POST',
            url: 'assets/php/uploader.php',
            contentType: false,
            processData: false,
            async: false,
            data: form_data, 
            success: function(data)
            {
                data = JSON.parse(data);
                url = data['file']['url'];

            }
        });
        return new Promise((resolve) => {
            resolve(url);
        });
        
        
    }

    function updateProc(sheetID, title, callback)
    {
     
        editor.save().then((outputData) => {
            console.log('Article data: ', outputData)

            $.post('assets/php/interface.php',
            {
                function: 'updateProc',
                sheetID: sheetID,
                title: title,
                sheet: JSON.stringify(outputData),
            }, function(data) {
                data = JSON.parse(data);
                callback(data);
            });

        }).catch((error) => {
            console.log('Saving failed: ', error)
        });   

   
    }

    function getSheetsTitleForUser(userID)
    {

        var rdata;
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getSheetsTitleForUser',
                userID    : userID
            },
            success: function(data)
            {         
                rdata = JSON.parse(data);
            },
        });
        return rdata;
    }

    function deleteVersion(version, callback)
    {
        console.log(currentProc);
        console.log(version)

        $.post('assets/php/interface.php',
        {
            function: 'deleteVersion',
            sheetID: currentProc,
            version: version,
        }, function(data) {
            data = JSON.parse(data);
            callback(data);
        });
    }

    function getCategories(callback)
    {
        
        var rdata;
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getCategories'
            },
            success: function(data)
            {         
                rdata = JSON.parse(data);
            },
        });
        return rdata;
        
    }

    function categoryList()
    {
        let lvl = 0;

        categories.forEach(block => {
            $('#catProcExplorer').append(
                buildHeaderBlock(lvl, 
                                block['name'], 
                                getRecursiveChildBlock(block, lvl)
                                )
            );
            lvl++; 
        });
  
        
    }

    function getRecursiveChildBlock(block, lvl)
    {

        var blocks = "";
        var sheetsBlock = "";
        var sheets =  getSheetByCategoryForUser(block['id'], currentUser['id'])
        if (sheets.length > 0)
        {
            sheets.forEach(sheet => {
                blocks += buildLinkedSheetBlock(sheet);
            });
        }
         
        // If childs
        if (block['childs'].length > 0)
        {
            block['childs'].forEach(childBlock => {
                // If child has childs
                if(childBlock['childs'].length > 0)
                {
                    lvl++;
                    blocks += buildHeaderBlock(lvl, childBlock['name'], getChildsBlock(childBlock, lvl++));
                } else {
                    // Last child of the tree

                    sheetsBlock = "";
                    sheets =  getSheetByCategoryForUser(childBlock['id'], currentUser['id'])
                    if (sheets.length > 0)
                    {
                        sheets.forEach(sheet => {
                            sheetsBlock += buildLinkedSheetBlock(sheet);
                        });
                    }

                    lvl = lvl + 2;
                    // if theres is sheets associated with this last category on the tree
                    if (sheetsBlock)
                    {
                        blocks += buildHeaderBlock(lvl, childBlock['name'], sheetsBlock);
                    } else {
                        
                        
                        blocks += buildEmptyCatBlock(childBlock['name'], lvl);
                    }
                }
            });
        }

        
        return blocks;
    }

    function getChildsBlock(childBlock, lvl)
    {
        var blocks = "";

        childBlock['childs'].forEach(childBlock => {
            if (childBlock['childs'].length > 0)
            {

                lvl++;
                blocks += buildHeaderBlock(lvl, 
                                childBlock['name'], 
                                getRecursiveChildBlock(childBlock, lvl)
                                )             
            } else {

                var sheetsBlock = "";
                let sheets =  getSheetByCategoryForUser(childBlock['id'], currentUser['id'])
                if (sheets.length > 0)
                {
                    sheets.forEach(sheet => {
                        sheetsBlock += buildLinkedSheetBlock(sheet);
                    });
                }

                lvl++;
                // if theres is sheets associated with this last category on the tree
                if (sheetsBlock)
                {
                    blocks += buildHeaderBlock(lvl, childBlock['name'], sheetsBlock);
                } else {
                    blocks += buildEmptyCatBlock(childBlock['name']);
                }
            }
        });
    
        
        return blocks;
    }

    function buildHeaderBlock(number, title, content)
    {
        return `<a href="#item-`+number+`" class="list-group-item" data-toggle="collapse">
                    <i class="fas fa-chevron-right"></i>`+title+`
                </a>
                <div class="list-group collapse" id="item-`+number+`">
                    `+content+`
                </div>`;
    }

    function buildChildBlock(content)
    {
        return `<a href="#" class="list-group-item">`+content+`</a>`
    }

    function buildLinkedSheetBlock(sheet)
    {
        return `<a id="`+sheet['id']+`" href="#" class="list-group-item linkedSheet"><i class="fas fa-circle"></i>`+sheet['title']+`</a>`
    }

    function buildEmptyCatBlock(content)
    {
        return `<a href="#" class="list-group-item"><i class="fas fa-square"></i>`+content+`</a>`
    }

    function getSheetByCategory(category)
    {
        var rdata;
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getSheetByCategory',
                category: category
            },
            success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
 
    }
    
    function getSheetByCategoryForUser(category, userID)
    {
        var rdata;
        $.ajax({
            type: 'POST',
            url: 'assets/php/interface.php',
            async: false,
            data: {
                function: 'getSheetByCategoryForUser',
                category: category,
                user: userID
            },
            success: function(data)
            {
                rdata = JSON.parse(data);
            },
        });
        return rdata;
 
    }