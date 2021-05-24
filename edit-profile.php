<?php
session_start();
require_once("./Connector/DbConnectorPDO.php");
include("./helper/helperFunctions.php");
$userId = isset($_SESSION["userId"]) && !IsVariableIsSetOrEmpty($_SESSION["userId"]) ? $_SESSION["userId"] : 0;
$connection = getConnection();
$userObj = $userId !== 0 && !IsVariableIsSetOrEmpty($_SESSION["user"]) ? $_SESSION["user"] : "";
$errors = array();
$id = '';
$firstName = '';
$lastName = '';
$email = '';
$city = '';
$bio = '';
$password = '';
$birthDate = '';
$bio = '';
$gender = '';
$image = '';
$notification = '';
$birthday = '';
$image_uploaded = false;
$imageURL = "./images/user_images/";
function getUserFromUserId($userId, $connection)
{
    $selectQuery = "SELECT * from profile WHERE id = '$userId'";
    $selectQuerystmt = $connection->prepare($selectQuery);
    $selectQuerystmt->execute();
    return $selectQuerystmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $users = $_SESSION['user'];
    $row = getUserFromUserId($userId, $connection);

    if (IsVariableIsSetOrEmpty($row)) {
        array_push($errors, 'Error fetching Data');
    } else {
        $id = $row['id'];
        $firstName = $row['firstName'];
        $password = $row['password'];
        $lastName = $row['lastName'];
        $email = $row['email'];
        $bio = $row['bio'];
        $city = $row['city'];
        $birthDate = $row['birthDate'];
        $gender = $row['gender'];
        $image = $row['imgUrl'];
        $notification = $row['receive_notification'];
        $birthday = new DateTime($row["birthDate"]);
        DateTime::createFromFormat('d/m/Y', $birthDate_old);
        $today = new DateTime('today');
        $diff = $today->diff($birthday);

    }

    if (isset($_POST["resetPassword"])) {
        $oldpassword = $_POST['oldPassword'];
        $NewPassword = $_POST['newPassword'];
        $ConfirmPassword = $_POST['confirmPassword'];
        if ($password === $oldpassword) {
            if ($NewPassword === $ConfirmPassword) {
                $updateQueryForPassword = "UPDATE profile SET password = '$NewPassword' WHERE id = '$id'";
                $updateQueryForPasswordstmt = $connection->prepare($updateQueryForPassword);
                $updateQueryForPasswordstmt->execute();
                $updateQueryForPasswordcount = $updateQueryForPasswordstmt->rowCount();
            } else {
                array_push($errors, 'Couldnt match the New Password and Confirm Password');
            }
        } else {
            array_push($errors, 'Old Password is incorrect');
        }
    }

    if (isset($_POST["uploadImage"]) && isset($_FILES["newImageFileUploadControl"])) {
        $file_name = $email . "_" . $_FILES['newImageFileUploadControl']['name'];
        $file_size = $_FILES['newImageFileUploadControl']['size'];
        $file_tmp = $_FILES['newImageFileUploadControl']['tmp_name'];
        $file_type = $_FILES['newImageFileUploadControl']['type'];
        $array = explode('.', $_FILES['newImageFileUploadControl']['name']);
        $file_ext = strtolower(end($array));

        $extensions = array("jpeg", "jpg", "png", "gif");

        if ($file_size > 5120000) {
            array_push($errors, 'File size must be less than 5 MB');
        }

        $imageURL = $imageURL . $file_name;
        move_uploaded_file($file_tmp, $imageURL);
        $image_uploaded = true;
        try {
            $updateQueryForImg = "UPDATE profile SET imgUrl = '$imageURL' WHERE id = '$id'";
            $updateQueryForImgstmt = $connection->prepare($updateQueryForImg);
            $updateQueryForImgstmt->execute();
            $updateQueryForImgcount = $updateQueryForImgstmt->rowCount();
        } catch (PDOException $exception) {
            throw $exception;
        }
        header("Location: ./edit-profile.php");
    }


    if (isset($_POST['Submit'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $bio = $_POST['bio'];
        $city = $_POST['city'];
        $birthDate = $_POST['birthDate'];
        $gender = $_POST['gender'];
        if(isset($_POST['notification'])){
            $notification = 1;
        }else{
            $notification = 0;
        }
        $updateQueryForTable = "UPDATE profile SET email = '$email', firstName = '$firstName', lastName = '$lastName', bio = '$bio', city = '$city', birthDate = '$birthDate', gender = '$gender', receive_notification = '$notification'  WHERE id = '$id'";
        $updateQueryForTablestmt1 = $connection->prepare($updateQueryForTable);
        $updateQueryForTablestmt1->execute();

        $rowGetNewUserData = getUserFromUserId($userId, $connection);
        if (!IsVariableIsSetOrEmpty($rowGetNewUserData)) {
            $_SESSION["user"] = $rowGetNewUserData;
        }

        // header("Location: ./edit-profile.php");
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Редактировать</title>
    <?php include("./includes/header.php") ?>
    <link href="./css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container-fluid wrapper">
    <?php
    include("./includes/nav-bar.php")
    ?>


    <div class="container">
        <br>
        <br>
        <div class="row mt-5">
            <?php
            // if any errors are there display them
            if (count($errors) > 0) {
                ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-danger" role="alert">
                        <?php
                        foreach ($errors as $error) { ?>
                            <li><?= $error ?></li>
                        <?php } ?>
                    </div>
                </div>

            <?php } ?>
        </div>
        <div class="row" id="main">
            <div class="col-md-4 well" id="leftPanel">
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <img src="<?php echo $image; ?>" height="220px" width="90%" alt="avatar" class="rounded-circle">

                    </div>
                </div>
                <div class="row mb-10">
                    <div class="col-md-12">
                        <form action="edit-profile.php" method="post" enctype="multipart/form-data">
                            <br>
                            <input accept="image/*" name="newImageFileUploadControl" type="file" class=" mb-10" required>
                            <input type="submit" name="uploadImage" class="form-control btn btn-light"
                                   value="Загрузить изображение"/>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h2><?php echo $firstName ?> <?php echo $lastName ?> </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3><?php echo "$diff->y лет"; ?></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <blockquote>
                            <p><?php echo $bio; ?></p>
                        </blockquote>
                    </div>
                </div>
            </div>
            <div class="col-md-8 well" id="rightPanel">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-signin" action="edit-profile.php" method="post" enctype="multipart/form-data">
                            <h2>Редактировать профиль</h2>
                            <hr class="colorgraph">
                            <div class="form-label-group">
                                <input type="text" name="firstName" id="firstName" class="form-control"
                                       placeholder="Имя" value="<?php echo $firstName; ?>" required>
                                <label for="firstName">Имя</label>
                            </div>
                            <div class="form-label-group">
                                <input type="text" id="lastName" name="lastName" class="form-control"
                                       placeholder="Фамилия" value="<?php echo $lastName; ?>" required>
                                <label for="lastName">Фамилия</label>
                            </div>
                            <div class="form-label-group">
                                <input type="email" name="email" id="inputEmail" class="form-control"
                                       placeholder="Почта" pattern="^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$"
                                       value="<?php echo $email; ?>" required>
                                <label for="inputEmail">Почта</label>
                            </div>
                            <div class="form-label-group">
                                <textarea class="form-control" name="bio" id="exampleFormControlTextarea1" rows="3"
                                          placeholder="Биография"><?php echo $bio; ?></textarea>
                            </div>
                            <div class="form-label-group">
                                <input type="password" name="password" id="inputPassword" class="form-control"
                                       placeholder="Пароль" value="<?php echo $password; ?>" disabled>
                                <label for="inputPassword">Пароль</label>
                            </div>
                            <div class="form-label-group">
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                        data-target="#changePasswordModal">
                                    Сменить пароль
                                </button>
                            </div>
                            <div class="form-label-group">
                                <input type="type" id="city" name="city" class="form-control"
                                       value="<?php echo $city; ?>" required>
                                <label for="city">Город</label>
                            </div>
                            <div class="form-label-group">
                                <input type="date" id="birthDate" name="birthDate" class="form-control"
                                       value="<?php echo $birthDate; ?>" required>
                                <label for="birthDate">Дата рождения</label>
                            </div>

                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender"
                                           value="male" <?php if ($gender === "male") {
                                        echo "checked";
                                    } ?> required>
                                    <label class="form-check-label" for="male">Мужчина</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender"
                                           value="female" <?php if ($gender === "female") {
                                        echo "checked";
                                    } ?>>
                                    <label class="form-check-label" for="female">Женщина</label>
                                </div>
                            </div>
                            <div class="form-label-group">
                                <input type="checkbox" id="notification" name="notification" value="notification" <?php if ($notification === "1") {
                                    echo "checked";
                                }?> >
                                <label for="notification">Получать уведомление или нет</label>
                            </div>
                            <hr class="colorgraph">
                            <div class="row">
                                <div class="col-xs-12 col-md-6"></div>
                                <div class="col-xs-12 col-md-6">
                                    <button class="btn btn-lg btn-primary btn-block text-uppercase" name="Submit"
                                            type="submit">Обновить
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog"
                 aria-labelledby="changePasswordModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalTitle">Сменить пароль</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="edit-profile.php" method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="password" class="form-control" id="oldPassword"
                                                   name="oldPassword" required placeholder="Пароль">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="password" class="form-control" id="newPassword"
                                                   name="newPassword" required placeholder="Новый пароль">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="password" class="form-control" id="confirmPassword"
                                                   name="confirmPassword" required placeholder="Повторите новый пароль">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                <button class="btn btn-primary" name="resetPassword" type="Submit">Сменить
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer -->
    <?php include("./includes/footer.php") ?>
    <!-- end of footer -->
</div>

</body>
</html>