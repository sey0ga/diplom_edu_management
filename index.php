<?php
require_once __DIR__ . "/functions/auth.php";

requireAuth();
$username = $_SESSION['username'];

echo <<< _END
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Добро пожаловать</title>
</head>
<body>
    <div class="page d-flex flex-column justify-content-between" id="page">
    <div class="upper">
        <div class="header" id="header">
            <div class="container d-flex justify-content-between">
                <div class="item">
                    <a href="/dean_office" class="logo">
                        <img src="assets/img/logo.svg" alt="mpgu-logo">
                    </a>
                    <span id="header-title">Учебное управление</span>
                </div>
                <div class="item d-flex flex-column justify-content-around align-items-left" id="user-info">
                    <span id="username">$username</span>
                    <span id="logout"><a href="functions/logout.php">выйти</a></span>
                </div>
            </div>
        </div>
        <div class="nav" id="nav">
            <div class="container d-flex justify-content-around">
                <a href="#page">
                    <img src="assets/img/home.png" alt="В начало">
                </a>
                <a href="sections/students.php">Студенты</a>
                <a href="sections/groups.php">Группы</a>
                <a href="sections/grades.php">Успеваемость</a>
                <a href="sections/orders.php">Движения</a>
            </div>
        </div>
        <div class="slogan" id="slogan">
            <div class="container d-flex justify-content-center">
                <div class="wrap"><h3>Добро пожаловать</h3></div>
            </div>
        </div>
        <div class="welcome" id="welcome">
            <h3 class="text-center">Перед началом работы</h3>
            <div class="container d-flex flex-column">
                <a href="sections/students.php">
                    <div class="item d-flex">
                        <img src="assets/img/students.png" alt="студенты">
                        <div class="text d-flex flex-column justify-content-around">
                            <h5 class="text-uppercase">студенты</h5>
                            <p>Раздел содержит информацию о студентах и отображает личные карты обучающихся, для перехода в данный раздел нажмите кнопку "Студенты" в навигации</p>
                        </div>
                    </div>
                </a>
                <a href="sections/groups.php">
                    <div class="item d-flex">
                        <img src="assets/img/groups.png" alt="группы">
                        <div class="text d-flex flex-column justify-content-around">
                            <h5 class="text-uppercase">группы</h5>
                            <p>Раздел содержит информацию о сформированных группах, для перехода в данный раздел нажмите кнопку "Группы" в навигации</p>
                        </div>
                    </div>
                </a>
                <a href="sections/grades.php">
                    <div class="item d-flex">
                        <img src="assets/img/grades.png" alt="успеваемость">
                        <div class="text d-flex flex-column justify-content-around">
                            <h5 class="text-uppercase">успеваемость</h5>
                            <p>Раздел содержит информацию о результатах экзаменов для обучающихся, для перехода в данный раздел нажмите кнопку "Успеваемость" в навигации</p>
                        </div>
                    </div>
                </a>
                <a href="sections/orders.php">
                    <div class="item d-flex">
                        <img src="assets/img/movements.png" alt="движения">
                        <div class="text d-flex flex-column justify-content-around">
                            <h5 class="text-uppercase">движения</h5>
                            <p>Раздел содержит информацию об истории перемещений студентов, для перехода в данный раздел нажмите кнопку "Движения" в навигации</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="bottom">
        <div class="footer" id="footer">
            <div class="container d-flex justify-content-between">
                <div class="item d-flex flex-column">
                    <h5>Контактная информация</h3>
                    <p>Начальник подразделения: Нарвыш Лариса Валентиновна</p>
                    <p>Адрес: г. Ставрополь, ул. Доваторцев 66 г, каб 213, 214, 215</p>
                    <p>Телефон: +7 (8652) 52-16-81</p>
                    <p>Почта: <a href="mailto:lv.narvysh@mpgu.su">lv.narvysh@mpgu.su</a>, <a href="mailto:sf.vpo@mpgu.su">sf.vpo@mpgu.su</a>, <a href="mailto:sf.vpozaoch@mpgu.su">sf.vpozaoch@mpgu.su</a></p>
                </div>
                <a href="#page" class="logo">
                    <img src="assets/img/logo.svg" alt="mpgu-logo">
                </a>
                <div class="item d-flex flex-column">
                    <h5>График работы</h3>
                    <p>С 8:30 до 17:15 (понедельник - четверег)</p>
                    <p>С 8:30 до 16:00 (пятница)</p>
                    <p>Перерыв: с 12:30 до 13:00</p>
                    <p>Суббота, воскресенье - выходные</p>
                </div>
            </div>
            <div class="container align-items-center">
                <span id="copy"><span id="copy-year"></span> © «Учебное управление» Ставропольский филиал МПГУ</span>
            </div>
        </div>
    </div>
    </div>
    <script src="assets/js/year.js"></script>
    <script src="assets/js/smoothScroll.js"></script>
</body>
</html>
_END;
?>