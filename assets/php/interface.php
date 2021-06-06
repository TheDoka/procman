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
        case  'giveSheetUserRole': 
          echo giveSheetUserRole($_POST['sheet'], $_POST['user'], $_POST['role']);
        break;
        case  'getProc': 
          echo json_encode(getProc($_POST['sheetID'], $_POST['version']));
        break;
        case  'deleteVersion': 
          echo json_encode(deleteVersion($_POST['sheetID'], $_POST['version']));
        break;
        case  'getAllCategories': 
          echo json_encode(getAllCategories());
        break;
        case  'deleteSheet': 
          echo json_encode(deleteSheet($_POST['sheetID']));
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
        case  'getCategoriesSheet': 
          echo json_encode(getCategoriesSheet($_POST['sheetID']));
        break;
        case  'getSheetByCategory': 
          echo json_encode(getSheetByCategory($_POST['category']));
        break;
        case  'getSheetByCategoryForUser': 
          echo json_encode(getSheetByCategoryForUser($_POST['category'], $_POST['user']));
        break; 
        case  'addNewCategory': 
          echo json_encode(addNewCategory($_POST['sheetID'], $_POST['cid']));
        break;
        case  'getRightsOverSheet': 
          echo json_encode(getRightsOverSheet($_POST['token'], $_POST['sheetID']));
        break;
        case  'getListOfAccessSheet': 
          echo json_encode(getListOfAccessSheet($_POST['sheetID']));
        break;
        case  'getRoles': 
          echo json_encode(getRoles());
        break; 
        case  'revokeRight': 
          echo json_encode(revokeRight($_POST['sheetID'], $_POST['userID']));
        break; 
        case  'revokeCategory': 
          echo json_encode(revokeCategory($_POST['sheetID'], $_POST['cid']));
        break; 
        case  'updateRole': 
          echo json_encode(updateRole($_POST['role']));
        break;
        case  'newRole': 
          echo json_encode(newRole($_POST['role']));
        break;
        case  'deleteRole': 
          echo json_encode(deleteRole($_POST['rid']));
        break;
        case  'getUsersName': 
          echo json_encode(getUsersName());
        break;
        case  'upload': 
          //echo upload();
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

function updateRole($role)
{

  $PDO = createPDO();

  $req = "UPDATE `role` SET 
         `name` = :name, 
         `canRead` = :canRead, 
         `canWrite` = :canWrite, 
         `canDelete` = :canDelete,
         `canShare` = :canShare
         WHERE `id` = :id";

  $cursor = $PDO->prepare($req);
  $cursor ->execute(array(
                        ':id'        => $role['id'],
                        ':name'      => $role['name'],
                        ':canRead'   => $role['canRead'],
                        ':canWrite'  => $role['canWrite'],
                        ':canDelete' => $role['canDelete'],
                        ':canShare'  => $role['canShare']
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
  $cursor ->execute();
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

function deleteSheet($sheetID)
{
  $PDO = createPDO();

    $req  ="DELETE SC, SA, S FROM sheet  S

            LEFT JOIN sheet_category SC 
            ON SC.sheet = S.id 
            
            LEFT JOIN sheet_access SA 
            ON SA.sheet = S.id
            
            WHERE S.id = $sheetID";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();
  
}

function giveSheetUserRole($sheet, $user, $role)
{
  $PDO = createPDO();

  $req  = "INSERT INTO `sheet_access` (`sheet`, `user`, `role`) 
           VALUES ('$sheet', '$user', '$role');";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();
  return $cursor->errorInfo()[2];
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

  $cursor = null;

  return true;

}

function addNewCategory($sheetID, $cid)
{

  $PDO = createPDO();

  $req = "INSERT INTO `sheet_category` (`id`, `sheet`, `category`) VALUES (NULL, '$sheetID', '$cid')";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $cursor = null;

  return true;

}

function deleteRole($rid)
{

  $PDO = createPDO();

  $req = "DELETE FROM role
          WHERE id = $rid";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  $cursor = null;

  return true;

}

function newRole($role)
{

  $PDO = createPDO();

  $a = $role['name'];
  $b = $role['canRead'];
  $c = $role['canWrite'];
  $d = $role['canDelete'];
  $e = $role['canShare'];

  $req = "INSERT INTO `role` VALUES 
  (NULL, '$a', '$b','$c', '$d', '$e', '0')";

$cursor = $PDO->prepare($req);
$cursor ->execute();

  $cursor = null;
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


function getCategoriesSheet($sheetID)
{
  $PDO = createPDO();

  $req = "SELECT C.* FROM sheet_category SC

          INNER JOIN category C
          ON C.id = SC.category 

          WHERE SC.sheet = $sheetID
        ";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  return $cursor->fetchAll(PDO::FETCH_ASSOC);

}

function getAllCategories()
{
  $PDO = createPDO();

  $req = "SELECT * FROM category";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  return $cursor->fetchAll(PDO::FETCH_ASSOC);

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

function getSheetByCategoryForUser($category, $user)
{
  $PDO = createPDO();

  $req = "SELECT DISTINCT S.*
          FROM sheet S
          
          INNER JOIN sheet_category SC
          ON SC.category = $category

          INNER JOIN sheet_access SH
          ON SH.sheet = S.id
          
          INNER JOIN role R
          ON R.id = SH.role
        
          WHERE SH.user = $user AND R.canRead = true AND S.version = (SELECT max(version) FROM sheet t2 WHERE t2.id = S.id)
        
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

function getRightsOverSheet($userToken, $sheetID)
{
  $PDO = createPDO();

  $req = "SELECT R.* FROM sheet_access S

          INNER JOIN role R
          ON R.id = S.role

          INNER JOIN user U 
          ON U.id = S.user

          WHERE S.sheet = $sheetID AND U.token = '$userToken'
        ";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  return $cursor->fetchAll(PDO::FETCH_ASSOC);

}

function getListOfAccessSheet($sheetID)
{
  $PDO = createPDO();

  $req = "SELECT U.username, U.id as uid, R.*, R.id as rid, R.name as roleName
          FROM sheet_access SH
          
          INNER JOIN user U
          ON U.id = SH.user

          INNER JOIN role R
          ON R.id = SH.role

          WHERE SH.sheet = $sheetID";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  return $cursor->fetchAll(PDO::FETCH_ASSOC);
}


function revokeRight($sheetID, $userID)
{
  $PDO = createPDO();

  $req = "DELETE FROM sheet_access 
          WHERE sheet = $sheetID AND user = ${userID}";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

}

function revokeCategory($sheetID, $cid)
{
  $PDO = createPDO();

  $req = "DELETE FROM sheet_category 
          WHERE sheet = $sheetID AND category = ${cid}";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

}

function getRoles()
{
  $PDO = createPDO();

  $req = "SELECT * FROM role";

  $cursor = $PDO->prepare($req);
  $cursor ->execute();

  return $cursor->fetchAll(PDO::FETCH_ASSOC);
}

function getUsersName()
{
  $PDO = createPDO();

  $req = "SELECT id, username FROM user";

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
