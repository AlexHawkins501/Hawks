<?php
session_start();
require_once("./Connector/DbConnectorPDO.php");
require("./helper/helperFunctions.php");
$connection = getConnection();
$userId = isset($_SESSION["userId"]) && !empty($_SESSION["userId"]) ? $_SESSION["userId"] : 0;
//if ($userId !== 0) {
//    $q = "SELECT * from profile WHERE id =:userId";
//    $s = $connection->prepare($q);
//    $s->bindParam(':userId', $userId);
//    $s->execute();
//    $row = $s->fetch(PDO::FETCH_ASSOC);
//
//    $_SESSION['user'] = $row;
//    $userObj = $row;
//}
$userObj = $userId !== 0 && !IsVariableIsSetOrEmpty($_SESSION["user"]) ? $_SESSION["user"] : "";

$isSearchCriteria = false;
$firstName = "";
$city = "";
$gender = "";
$ageToSearch = "18";
$isWinkSent = false;
$isUserAlreadyFavourited = false;
$query = "select * from profile ";
$profileList = array();
if ($userId !== 0) {
    $query .= "where id <> :userId";
}

if (isset($_POST["Search"]) && !IsVariableIsSetOrEmpty($_POST["Search"])) {

    $searchCount = 0;
    $firstName = $_POST["firstName"];
    $city = $_POST["city"];
    $gender = $_POST["gender"];
    $ageToSearch = $_POST["age"];
    $searchQuery = "";

    if (!IsVariableIsSetOrEmpty($firstName)) {
        $searchQuery .= " firstName like :firstName";
        $searchCount++;
    }
    if (!IsVariableIsSetOrEmpty($city)) {
        if ($searchCount > 0) {
            $searchQuery .= " and ";
        }
        $searchQuery .= " city like :city";
        $searchCount++;
    }
    if (!IsVariableIsSetOrEmpty($gender)) {
        if ($searchCount > 0) {
            $searchQuery .= " and ";
        }
        $searchQuery .= " gender=:gender";
        $searchCount++;
    }
    if (!IsVariableIsSetOrEmpty($ageToSearch)) {
        $ageToSearch = intval($ageToSearch);
        if ($searchCount > 0) {
            $searchQuery .= " and ";
        }
        if ($ageToSearch === 18) {
            $searchQuery .= " (YEAR(CURDATE()) - YEAR(birthDate)) >=:age";
        } else {
            $searchQuery .= " (YEAR(CURDATE()) - YEAR(birthDate)) BETWEEN  18 and :age";
        }
        $searchCount++;
    }

    if ($searchCount > 0) {
        $isSearchCriteria = true;
        if ($userId === 0) {
            $query .= " where " . $searchQuery;
        } else {
            $query .= " and " . $searchQuery . "";
        }
    }
}

$profileListStmt = $connection->prepare($query);
if ($userId !== 0) {
    $profileListStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
}

if ($isSearchCriteria === true) {
    if (!empty($firstName)) {
        $newFirstNameString = "%{$firstName}%";
        $profileListStmt->bindParam(':firstName', $newFirstNameString, PDO::PARAM_STR);
    }
    if (!empty($city)) {
        $newCityString = "%{$city}%";
        $profileListStmt->bindParam(':city', $newCityString, PDO::PARAM_STR);
    }
    if (!empty($gender)) {
        $profileListStmt->bindParam(':gender', $gender, PDO::PARAM_STR);
    }
    if (!empty($ageToSearch)) {
        $profileListStmt->bindParam(':age', $ageToSearch, PDO::PARAM_INT);
    }
}
$profileListStmt->execute();
//$profileList = $stmt->setFetchMode(PDO::FETCH_ASSOC);
$profileList = $profileListStmt->fetchAll();

if (isset($_GET["sendWinkTo"])) {
    $sendWinkToId = isset($_GET["sendWinkTo"]) && !IsVariableIsSetOrEmpty($_GET["sendWinkTo"]) ? intval($_GET["sendWinkTo"]) : 0;
    if ($sendWinkToId !== 0) {
        $queryGetLastMessage = "SELECT * 
                            FROM messages 
                            WHERE msg_from_user_id=:sendWinkToId and msg_to_user_id=:userId and is_msg_read=0
                            order by msg_date desc limit 1";
        $getLastMessageStmt = $connection->prepare($queryGetLastMessage);
        $getLastMessageStmt->bindParam(':sendWinkToId', $sendWinkToId);
        $getLastMessageStmt->bindParam(':userId', $userId);
        $getLastMessageStmt->execute();
        $getLastMessageList = $getLastMessageStmt->fetchAll();
        if (isset($getLastMessageList) && !IsVariableIsSetOrEmpty($getLastMessageList) && count($getLastMessageList) > 0) {
            $getFirstRow = $getLastMessageList[0];
            if (!IsVariableIsSetOrEmpty($getFirstRow)) {
                $updateAllMsgReadQuery = "UPDATE messages set is_msg_read=1,msg_read_date=NOW() where msg_from_user_id=:sentToUserID and msg_to_user_id=:userId and is_msg_read=0";
                $updateAllMsgRead = $connection->prepare($updateAllMsgReadQuery);
                $updateAllMsgRead->bindParam(':sentToUserID', $sendWinkToId);
                $updateAllMsgRead->bindParam(':userId', $userId);
                $updateAllMsgRead->execute();
            }
        }

        $insertMessageQuery = "INSERT INTO messages(msg_from_user_id,msg,msg_to_user_id,msg_date,is_msg_read) 
                               values(:userId,':^)',:sendWinkToId,NOW(),0)";
        $insertStmt = $connection->prepare($insertMessageQuery);
        $insertStmt->bindParam(':userId', $userId);
        $insertStmt->bindParam(':sendWinkToId', $sendWinkToId);
        $insertStmt->execute();
        $isWinkSent = true;
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
    <?php include("./includes/header.php") ?>
    <link href="./css/style.css" rel="stylesheet" type="text/css">
    <title>Профили пользователей</title>
</head>
<body>
<div class="container-fluid wrapper">
    <?php
    include("./includes/nav-bar.php")
    ?>

    <?php
    if ($isWinkSent && isset($_GET["sendWinkTo"])) {
        ?>
        <div class="row mt-10 mb-10">
            <div class="col-md-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Подмигивание ( :^) ) отправлено пользователю успешно! Нажмите<strong><a
                                href="./chat-users.php?id=<?= $_GET["sendWinkTo"] ?>"> здесь</a></strong> чтобы начать общаться в чате
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="mb15">
        <div class="row mt-10 mb-10">
            <div class="col-md-12 text-center">
                <h2>Поиск профилей</h2>
            </div>
        </div>
        <div class="row mb-10">
            <div class="col-md-12">
                <form method="post" action="view-profiles.php">
                    <div class="form-row mb-10">
                        <div class="col">
                            <div class="form-group">
                                <label for="firsName">Поиск по имени</label>
                                <input name="firstName" id="firstName" type="text" class="form-control"
                                       placeholder="Имя" value="<?= $firstName ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="lastName">Поиск по городу</label>
                                <input name="city" id="city" type="text" class="form-control"
                                       placeholder="Город" value="<?= $city ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="gender">Поиск по полу</label>
                                <select id="gender" class="form-control" name="gender">
                                    <option value="" <?php if (empty($gender)) {
                                        echo "selected";
                                    } ?>>-- Выбрать пол
                                        --
                                    </option>
                                    <option value="male" <?php if ($gender === "male") {
                                        echo "selected";
                                    } ?>>Мужчина
                                    </option>
                                    <option value="female" <?php if ($gender === "female") {
                                        echo "selected";
                                    } ?>>Женщина
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="formControlRange">Выберите нужный диапазон возраста для поиска</label>

                                <input type="range" min="18" value="<?= $ageToSearch ?>" max="90"
                                       class="form-control-range" name="age"
                                       id="ageInputId">
                                <output name="ageOutputName" id="ageOutputId">Поиск анкет с возрастом строго старше 18 лет</output>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-10 col-sm-12">
                            <input type="submit" name="Search" value="Поиск" class="btn btn-dark w-100"/>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <input type="submit" name="Reset" value="Сбросить настройки поиска" class="btn btn-info w-100"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php
        if (count($profileList) > 0) {
            $counter = 0;
            foreach ($profileList as $profile) {
                $counter++;
                if ($counter === 1) {
                    echo '<div class="row mb-10">';
                }
                ?>
                <div class="col-md-3">
                    <div class="card card-container">
                        <img class="card-img-top"
                             src="<?= $profile["imgUrl"] ?>"
                             alt="profile image">
                        <div class="card-body">
                            <h5 class="card-title">Имя: <?= $profile["firstName"] . ' ' . $profile['lastName'] ?></h5>
                            <p class="card-text bio-desc-container"><?= $profile["bio"] ?></p>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Возраст: <?php
                                try {
                                    $birthDay = new DateTime($profile["birthDate"]);
                                    $today = new Datetime(date('y-d-m'));
                                    $diff = $today->diff($birthDay);
                                    echo "$diff->y years";
                                } catch (Exception $e) {
                                } // Your date of birth
                                ?></li>
                            <li class="list-group-item">Город: <?= $profile["city"] ?></li>
                            <li class="list-group-item">
                                Пол:
                                <span style="text-transform: capitalize">
									<? if ($profile["gender"]===male)
									   echo "Мужской";
									   else 
									   echo "Женский"?>
                                </span>
                            </li>
                        </ul>
                        <div class="card-body">
                            <?php
                            if ($userId === 0) {
                                ?>
                                <div class="row mb-10">
                                    <div class="col-md-12 col-sm-12">
                                        <button class="btn btn-success w-100" data-toggle="modal"
                                                data-target="#loginModal">
                                            Написать
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <button class="btn btn-info w-100" data-toggle="modal"
                                                data-target="#loginModal">
                                            Смайлик
                                        </button>
                                    </div>
<!--                                     <div class="col-md-6 col-sm-12">
                                        <button class="btn btn-danger w-100" data-toggle="modal"
                                                data-target="#loginModal">
                                            Favourite
                                        </button>
                                    </div> -->
                                </div>

                                <?php
                            } else {
                                ?>
                                <div class="row mb-10">
                                    <div class="col-md-12 col-sm-12">
                                        <a href="./chat-users.php?id=<?= $profile["id"] ?>"
                                           class="btn btn-success w-100">
                                            Написать
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <a href="./view-profiles.php?sendWinkTo=<?= $profile["id"] ?>"
                                           name="SendWink" class="btn btn-info w-100">Смайлик</a>

                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <?php
                                        if ($userObj["user_role"] === "admin") {
                                            echo "<a href='delete-user.php?id=".$profile["id"]."'>";
                                            ?>
                                                <button class="btn btn-danger w-100" data-toggle="modal"
                                                    data-target="#addToFavouriteModal">
                                                    Удалить
                                                </button>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                if ($counter === 4) {
                    echo '</div>';
                    $counter = 0;
                }
            }
        } else {
            ?>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-info text-center" role="alert">
                        <?php
                        if ($userId !== 0) {
                            ?>
                            Не найдено такого профиля!
                            <?php
                        } else {
                            ?>
                            Не найдено такого профиля! Нажмите <a href="./register.php">здесь</a> на регистрацию!
                        <?php } ?>
                    </div>
                </div>

            </div>
            <?php
        }
        ?>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Упс!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Вы не вошли в систему. Чтобы использовать эту функцию, пожалуйста, войдите в систему или создайте профиль.</p>
                </div>
                <div class="modal-footer">
                    <a href="./login.php" class="btn btn-success">Войти</a>
                    <a href="./register.php" class="btn btn-primary">Регистрация</a>
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