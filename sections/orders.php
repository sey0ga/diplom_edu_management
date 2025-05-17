<?php
require_once __DIR__ . "/../functions/queries.php";
require_once __DIR__ . "/../functions/auth.php";
require_once __DIR__ . "/../functions/sort.php";
require_once __DIR__ . "/../functions/export.php";

requireAuth();
$username = $_SESSION['username'];

$search_query = "";
$orders = getAllMovements();

if (isset($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);
    $search_query = $search;
    if (!isset($_GET['sort'])){
        $orders = getAllMovements(search: $search);
    }
    
}

if (isset($_GET['sort'])) {
    if (empty($search)) {
    $search = "";
    }

    $sort = htmlspecialchars($_GET['sort']);
    if (!array_key_exists($sort, $sort_list['orders'])) {
        header("Location: /dean_office/sections/orders.php");
        exit();
    }
    $sort_sql = $sort_list['orders'][$sort];
    $orders = getAllMovements(orderBy: $sort_sql, search: $search);
}


if (isset($_POST['order_name']) && isset($_POST['order_type'])) {
    [$insertResult, $lastId] = addOrder($_POST['order_name'], $_POST['order_type']);
    if ($insertResult) {
        header("Location: /dean_office/sections/orders.php");
        exit();
    }
} else if (isset($_POST['student']) && isset($_POST['orders']) &&
            isset($_POST['movement_date']) && isset($_POST['group']) && isset($_POST['course'])) {
                $insertResult = addMove($_POST['orders'], $_POST['student'], $_POST['movement_date'], $_POST['group'], $_POST['course']);
                if ($insertResult) {
                    header("Location: /dean_office/sections/orders.php");
                    exit();
                }
            }
if (isset($_POST['export_orders'])) {
    exportToCsv($orders, "orders_" . date('Y-m-d H:i:s') . ".csv");
}

$sort_id = sort_link_th('id', 'id_asc', 'id_desc', $search);
$sort_order = sort_link_th('Тип приказа', 'order_asc', 'order_desc', $search);
$sort_student = sort_link_th('Студент', 'student_asc', 'student_desc', $search);
$sort_date = sort_link_th('Дата', 'date_asc', 'date_desc', $search);
$sort_group = sort_link_th('Новая группа', 'group_asc', 'group_desc', $search);
$sort_course = sort_link_th('Новый курс', 'course_asc', 'course_desc', $search);

$students_list = getListStudents();
$orders_list = getListOrders();
$orders_types_list = getListOrdersTypes();
$groups_list = getListGroups();

$orders = $orders->fetchAll();

echo <<< _START
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components/selectize.default.css">
    <title>Движения</title>
</head>
<body>
    <div class="page d-flex flex-column justify-content-between" id="page">
    <div class="upper">
        <div class="header" id="header">
            <div class="container d-flex justify-content-between">
                <div class="item">
                    <a href="/dean_office" class="logo">
                        <img src="../assets/img/logo.svg" alt="mpgu-logo">
                    </a>
                    <span id="header-title">Учебное управление</span>
                </div>
                <div class="item d-flex flex-column justify-content-around align-items-left" id="user-info">
                    <span id="username">$username</span>
                    <span id="logout"><a href="../functions/logout.php">выйти</a></span>
                </div>
            </div>
        </div>
        <div class="nav" id="nav">
            <div class="container d-flex justify-content-around">
                <a href="#page">
                    <img src="../assets/img/home.png" alt="В начало">
                </a>
                <a href="students.php">Студенты</a>
                <a href="groups.php">Группы</a>
                <a href="grades.php">Успеваемость</a>
                <a href="orders.php">Движения</a>
            </div>
        </div>
        <div class="slogan" id="slogan">
            <div class="container d-flex justify-content-center">
                <div class="wrap"><h3>Добро пожаловать</h3></div>
            </div>
        </div>
        <div class="orders">
            <div class="container d-flex flex-column">
                <h3 class="text-center">Движения</h3>
                <div class="pre-table d-flex justify-content-between">
                    <div class="action_button d-flex justify-content-between">
                        <button class="btn-dialog" id="addButtonOpenMove">Добавить запись (движение)</button>
                        <button class="btn-dialog" id="addButtonOpenOrders">Добавить запись (приказ)</button>
                    </div>
                    <div class="export-search d-flex flex-column justify-content-between">
                        <form class="export-form d-flex" action="" method="POST">
                            <input type="hidden" name="export_orders" value="export">
                            <button type="submit" class="btn-dialog">Выгрузка CSV</button>
                        </form>
                        <form class="search-form d-flex" action="" method="GET">
                            <input type="text" name="search" placeholder="Поиск" value="$search_query">
                            <button type="submit">🔎</button>
                        </form>
                    </div>
                </div>
                <div id="overlay"></div>
                <div id="addDialogMove" class="d-flex flex-column">
                    <h3 class="text-center">Добавить запись (движения)</h3>
                    <form id="addMove" action="" method="POST">
                        <div class="add-form d-flex flex-column">
                            <div class="item d-flex flex-column">
                                <label for="student">Студент:</label>
                                <select class="js-selectize" name="student" id="students" placeholder="Выберите обучающегося" required>
                                <option value=""></option>
                                $students_list
                                </select>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="orders">Приказ:</label>
                                <select class="js-selectize" name="orders" id="orders" placeholder="Выберите приказ" required>
                                <option value=""></option>
                                $orders_list
                                </select>
                            </div>
                            <div class="item d-flex flex-column">
                            <label for="movement_date">Дата:</label>
                            <input type="date" class="selectize-input" name="movement_date" id="movement_date" required>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="group">Группа:</label>
                                <select class="js-selectize" name="group" id="group" placeholder="Выберете групу" required>
                                <option value=""></option>
                                $groups_list
                                </select>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="course">Курс:</label>
                                <select class="js-selectize" name="course" id="course" placeholder="Выберете курс" required>
                                <option value=""></option>
                                <option value="1">1 курс</option>
                                <option value="2">2 курс</option>
                                <option value="3">3 курс</option>
                                <option value="4">4 курс</option>
                                <option value="5">5 курс</option>
                                </select>
                            </div>
                        </div>
                        <div class="btns d-flex justify-content-between">
                            <button type="submit" class="btn-dialog" id="move-send">Добавить</button>
                            <button type="button" class="btn-dialog" id="addButtonCloseMove">Отмена</button>
                        </div>
                    </form>
                </div>
                <div id="addDialogOrders" class="d-flex flex-column">
                    <h3 class="text-center">Добавить запись (движения)</h3>
                    <form id="addOrder" action="" method="POST">
                        <div class="add-form d-flex flex-column">
                            <div class="item d-flex flex-column">
                                <label for="order_name">Название приказа:</label>
                                <input type="text"
                                        name="order_name"
                                        id="order_name"
                                        class="selectize-input"
                                        placeholder="Введите название приказа"
                                        max-length="255"
                                        required>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="order_type">Тип:</label>
                                <select class="js-selectize" name="order_type" id="order_type" placeholder="Выберите тип приказа" required>
                                <option value=""></option>
                                $orders_types_list
                                </select>
                            </div>
                        </div>
                        <div class="btns d-flex justify-content-between">
                            <button type="submit" class="btn-dialog" id="orders-send">Добавить</button>
                            <button type="button" class="btn-dialog" id="addButtonCloseOrders">Отмена</button>
                        </div>
                    </form>
                </div>
                <div class="item res-table d-flex justify-content-center">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>$sort_id</th>
                                <th>$sort_order</th>
                                <th>$sort_student</th>
                                <th>$sort_date</th>
                                <th>$sort_group</th>
                                <th>$sort_course</th>
                            </tr>
                        </thead>
                        <tbody>
_START;
showTbodyMovements($orders);
echo <<< _END
                        </tbody>
                    </table>
                </div>
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
                    <img src="../assets/img/logo.svg" alt="mpgu-logo">
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
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <scripy src="../assets/js/microplugin.js"></script>
    <script src="../assets/js/selectize.min.js"></script>
    <script src="../assets/js/sifter.min.js"></script>
    <script src="../assets/js/year.js"></script>
    <script src="../assets/js/smoothScroll.js"></script>
    <script src="../assets/js/addDialogOrders.js"></script>
    <script>
    $(document).ready(function(){
        $('.js-selectize').selectize();
    });
    </script>
</body>
</html>
_END;
?>