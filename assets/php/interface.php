<?php



  if (isset($_POST['function']))
  {
    include('sql.php');
      switch($_POST['function'])
      {
        case  'login': 
          echo json_encode(login($_POST['username'], $_POST['password'])); 
        break;
        case  'logout': 
          echo logout($_POST['token']); 
        break;
        case  'getUser': 
          echo json_encode(getUser($_POST['userID'], $_POST['token']));
        break;
        case  'newProc': 
          echo json_encode(newProc($_POST['title'], $_POST['sheet']));
        break;
        case  'getProc': 
          echo json_encode(getProc($_POST['sheetID'], $_POST['version']));
        break;
        case  'deleteVersion': 
          echo json_encode(deleteVersion($_POST['sheetID'], $_POST['version']));
        break;
        case  'getSheetsTitleForUser': 
          echo json_encode(getSheetsTitleForUser($_POST['userID']));
        break;
        case  'getSheetVersions': 
          echo json_encode(getSheetVersions($_POST['sheetID']));
        break;
        case  'updateProc': 
          echo json_encode(updateProc($_POST['sheetID'], $_POST['title'], $_POST['sheet']));
        break;
        case  'uploadFile': 
          echo json_encode(uploadFile($_POST['sheetID'], $_POST['content']));
        break;
        case  'getCategories': 
          echo json_encode(getCategories());
        break;
        case  'getSheetByCategory': 
          echo json_encode(getSheetByCategory($_POST['category']));
        break;
        case  'upload': 
          echo upload();
        break;

      }

  }


function login($username, $password)
{
  $PDO = createPDO();

  $pass = md5($password);
  $req = "SELECT *
          FROM user
          WHERE username = '$username' && password = '$pass'";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $user = $cursor->fetch();
  $cursor = null;
  
  //if ($user['token'])
  //{
    // user is already logged in;
    //return 'User is already logged in.';
  //} 

  if ($user && !$user['token'])
  {
    $user['token'] = loginUUID();
    updateUser($user);
  }
  
  return $user;

}

function logout($token)
{
  $PDO = createPDO();

  $req = "SELECT *
          FROM user
          WHERE token = '$token'";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $user = $cursor->fetch();
  $cursor = null;
  
  if ($user)
  {
    $user['token'] = null;
    updateUser($user);
  }
  
}

function getUser($userID, $token)
{
  $PDO = createPDO();

  $req = "SELECT *
          FROM user
          WHERE token = '$token' && id = '$userID'";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();
  $user = $cursor->fetch();

  return $user;
}

function updateUser($user)
{

  $PDO = createPDO();

  $req = "UPDATE `user` SET 
         `username` = :username, 
         `password` = :password, 
         `mail` = :mail, 
         `token` = :token
         WHERE `id` = :id";

  $cursor = $PDO->prepare($req);
  $cursor ->execute(array(
                        ':id'       => $user['id'],
                        ':username' => $user['username'],
                        ':password' => $user['password'],
                        ':mail'     => $user['mail'],
                        ':token'    => $user['token']
                    ));
  return $cursor->errorInfo()[2];
}

function loginUUID()
{
  return uniqid('mor');
}

function updateProc($id, $title, $content)
{

  $PDO = createPDO();
  $versions = getSheetVersions($id);
  $nextVersion = $versions[count($versions)-1][0]+1;

  $content = addslashes($content);
  $req  = "INSERT INTO `sheet` 
          (`id`, `title`, `content`, `created`, `version`) VALUES 
          ($id, '$title', '$content', '2021-03-03 17:45:36', $nextVersion)";

  $cursor = $PDO->prepare($req);
  $cursor ->execute($proc);
  $cursor = null;

  return true;

}

function newProc($title, $content)
{
  $PDO = createPDO();

  $content = addslashes($content);
  $req  = "INSERT INTO `sheet` 
          (`id`, `title`, `content`, `created`, `version`) VALUES 
          (NULL, '$title', '$content', '2021-03-03 17:45:36', 1);
          ";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();
  
  $req = "SELECT max(id) FROM `sheet`;";
  $cursor = $PDO->prepare($req);
  $cursor ->execute();
  $sheetID = $cursor->fetch();

  $cursor = null;
  return $sheetID[0];
}

function getProc($sheetID, $version)
{
  $PDO = createPDO();

  $req = "SELECT *
          FROM sheet
          WHERE id = $sheetID && version = $version";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $sheet = $cursor->fetch();
  $cursor = null;

  return $sheet;

}

function getSheetsTitleForUser($userID)
{
  $PDO = createPDO();
  // Récupère l'intégralité des fiches qui sont accessibles par l'utilisateur en lecture.
  $req = "SELECT DISTINCT S.id, S.title, max(S.version)
          FROM sheet S
          
          INNER JOIN sheet_access SH
          ON SH.sheet = S.id
          
          INNER JOIN role R
          ON R.id = SH.role
          
          WHERE SH.user = $userID AND R.canRead = true
          
          GROUP BY S.id";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $sheets = $cursor->fetchAll(PDO::FETCH_NUM);
  $cursor = null;

  return $sheets;
}

function getSheetVersions($sheetID)
{

  $PDO = createPDO();

  $req = "SELECT version, created
          FROM sheet
          WHERE id = $sheetID";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $sheet = $cursor->fetchAll(PDO::FETCH_NUM);
  $cursor = null;

  return $sheet;

}

function deleteVersion($sheetID, $version)
{

  $PDO = createPDO();

  $req = "DELETE FROM `sheet` 
          WHERE `sheet`.`id` = $sheetID AND `sheet`.`version` = $version";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $sheet = $cursor->fetch();
  $cursor = null;

  return true;

}

function getCategories()
{
  $PDO = createPDO();

  $req = "SELECT *
          FROM category
          WHERE child is null";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();
  $categories = [];

  $primayCategories = $cursor->fetchAll(PDO::FETCH_ASSOC);
  foreach ($primayCategories as $key => &$primaryCategory) {
    $primaryCategory['childs'] = getTreeCategory($PDO, $primaryCategory);
  }


  $cursor = null;
  
  return $primayCategories;

}

function getSheetByCategory($category)
{
  $PDO = createPDO();

  $req = "SELECT S.*
          FROM sheet S

          INNER JOIN sheet_category SC
          ON SC.sheet = S.id
          
          WHERE SC.category = $category and S.version = (SELECT max(version) FROM sheet t2 WHERE t2.id = S.id)
          GROUP BY S.id
        ";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  return $cursor->fetchAll(PDO::FETCH_ASSOC);

}

function getTreeCategory($PDO, $category)
{

  $category['childs'] = getSubcategory($PDO, $category);

  foreach ($category['childs'] as $key => &$child) {
    $child['childs'] = getTreeCategory($PDO, $child);

  }

  return $category['childs'];
}

function getSubcategory($PDO, $category)
{
  
  $req = "SELECT *
          FROM category
          WHERE child =" . $category['id'];
  $cursor = $PDO->prepare($req);
  $cursor ->execute();
  
  return $cursor->fetchAll(PDO::FETCH_ASSOC);
}

function uploadFile($sheetID, $content)
{
//   {
//     "success" : 1,
//     "file": {
//         // any data you want 
//         // for example: url, name, size
//     }
// }
}

function upload($file)
{

//   if ( 0 < $file['error'] ) {
//     echo 'Error: ' . $file['error'] . '<br>';
//   }
//   else {
//       move_uploaded_file($file['tmp_name'], '../../uploads/' . $file['name']);
//   }

if ( 0 < $_FILES['file']['error'] ) {
  echo 'Error: ' . $_FILES['file']['error'] . '<br>';
}
else {
  move_uploaded_file($_FILES['file']['tmp_name'], '../../uploads/' . $_FILES['file']['name']);
}

echo $_FILES['file']['name'];
}

?>
