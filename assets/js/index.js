    
    var editor;
    var currentUser;
    var currentProc;
    var currentVersion;

    function init()
    {
        makeEditor({});
        categoryList();
        setupAutoCompleteOnSearch(); 
 
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
            }
        });
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
        $.post('assets/php/interface.php',
        {
            function: 'getCategories',
        }, function(data) {
            data = JSON.parse(data);
            callback(data);
        });
    }

    function categoryList()
    {
        let lvl = 0;
        getCategories(function (data) {
            data.forEach(block => {
                $('#catProcExplorer').append(
                    buildHeaderBlock(lvl, 
                                    block['name'], 
                                    getRecursiveChildBlock(block, lvl)
                                    )
                );
                lvl++; 
            });
        });
        
    }

    function getRecursiveChildBlock(block, lvl)
    {

        var blocks = "";
        var sheetsBlock = "";
        var sheets =  getSheetByCategory(block['id'])
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
                    sheets =  getSheetByCategory(childBlock['id'])
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
                let sheets =  getSheetByCategory(childBlock['id'])
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