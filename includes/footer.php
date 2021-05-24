<?php
include_once("./helper/helperFunctions.php");
$userId = 0;
$user = array();
if (isset($_SESSION["userId"])) {
    $userId = !IsVariableIsSetOrEmpty($_SESSION['userId']) ? $_SESSION['userId'] : 0;
    $user = $userId !== 0 && !IsVariableIsSetOrEmpty($_SESSION['user']) ? $_SESSION['user'] : array();
}
?>
<footer class="mt-10 bg-dark">
    <div class="container">
        <div class="row ">
            <div class="col-md-4 text-center text-md-left ">

                <div class="py-0">
                    <h3 class="my-4 text-white">Знакомства<span class="mx-2 font-italic text-warning ">Макара</span>
                    </h3>

                    <p class="footer-links font-weight-bold">
                        <a class="text-white" href="./index.php">Главная</a>
                        |
                        <a class="text-white" href="./view-profiles.php">Профили</a>
                        <?php
                        if ($userId === 0) {
                            ?>
                            |
                            <a class="text-white" href="./login.php">Войти</a>
                            |
                            <a class="text-white" href="./register.php">Регистрация</a>
                            <?php
                        } else {
                            ?>
                            |
                            <a class="text-white" href="./edit-profile.php">Редактировать профиль</a>
                            <?php
                        }
                        ?>

                    </p>
                    <p class="text-light py-4 mb-4">&copy;<?php echo date("Y"); ?> Сайт знакомств Макара</p>
                </div>
            </div>

            <div class="col-md-4 text-white text-center text-md-left ">
                <div class="py-2 my-4">
                    <div>
                        <p class="text-white"><i class="fa fa-map-marker mx-2 "></i>
                            Двинская ул., 5/7
                            Город Санкт-Петербург</p>
                    </div>

                    <div>
                        <p><i class="fa fa-phone  mx-2 "></i>8 (800) 555-35-35</p>
                    </div>
                    <div>
                        <p><i class="fa fa-envelope  mx-2"></i><a href="mailto:makara@gmail.com">makara@gmail.com</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 text-white my-4 text-center text-md-left ">
                <span class=" font-weight-bold ">О нас</span>
                <p class="text-warning my-2">Мы предлагаем услуги онлайн-знакомств. Платформа для поиска идеального
спутника жизни.</p>
            </div>
        </div>
    </div>
</footer>