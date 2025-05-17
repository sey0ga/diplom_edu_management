<?php
require_once __DIR__ . "/../functions/queries.php";
require_once __DIR__ . "/../functions/auth.php";
require_once __DIR__ . "/../functions/sort.php";
require_once __DIR__ . "/../functions/export.php";

requireAuth();

$username = $_SESSION['username'];

$search_query = "";
$groups = getAllGroups();

if (isset($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);
    $search_query = $search;
    if (!isset($_GET['sort'])){
        $groups = getAllGroups(search: $search);
    }
    
}

if (isset($_GET['sort'])) {
    if (empty($search)) {
    $search = "";
    }

    $sort = htmlspecialchars($_GET['sort']);
    if (!array_key_exists($sort, $sort_list['groups'])) {
        header("Location: /dean_office/sections/groups.php");
        exit();
    }
    $sort_sql = $sort_list['groups'][$sort];
    $groups = getAllGroups(orderBy: $sort_sql, search: $search);
}

if (isset($_POST['group_name']) && isset($_POST['edu_prog'])) {
    $insertResult = addGroup($_POST['group_name'], $_POST['edu_prog']);
    if ($insertResult) {
        header("Location: /dean_office/sections/groups.php");
        exit();
    }
}

if (isset($_POST['export_groups'])) {
    exportToCsv($groups, "groups_" . date('Y-m-d H:i:s') . ".csv");
}

$sort_id = sort_link_th('id', 'id_asc', 'id_desc', $search);
$sort_group = sort_link_th('Группа', 'name_asc', 'name_desc', $search);
$sort_edu = sort_link_th('Образование', 'type_asc', 'type_desc', $search);
$sort_base = sort_link_th('База', 'base_asc', 'base_desc', $search);
$sort_form = sort_link_th('Форма', 'form_asc', 'form_desc', $search);
$sort_program = sort_link_th('Образовательная программа', 'program_asc', 'program_desc', $search);

$edu_prog_list = getListEduProg();

$groups = $groups->fetchAll();

echo <<< _START
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components/selectize.default.css">
    <title>Группы</title>
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
        <div class="groups">
            <div class="container d-flex flex-column">
                <h3 class="text-center">Группы</h3>
                <div class="pre-table d-flex justify-content-between">
                    <div class="action_button d-flex justify-content-between">
                        <button class="btn-dialog" id="addButtonOpenGroups">Добавить запись (группа)</button>
                    </div>
                    <div class="export-search d-flex flex-column justify-content-between">
                        <form class="export-form d-flex" action="" method="POST">
                            <input type="hidden" name="export_groups" value="export">
                            <button type="submit" class="btn-dialog">Выгрузка CSV</button>
                        </form>
                        <form class="search-form d-flex" action="" method="GET">
                            <input type="text" name="search" placeholder="Поиск" value="$search_query">
                            <button type="submit">🔎</button>
                        </form>
                    </div>
                </div>
                <div id="overlay"></div>
                <div id="addDialogGroups" class="d-flex flex-column">
                    <h3 class="text-center">Добавить запись (группа)</h3>
                    <form id="addGroup" action="" method="POST">
                        <div class="add-form d-flex flex-column">
                            <div class="item d-flex flex-column">
                                <label for="group_name">Название группы:</label>
                                <input type="text"
                                    id="group_name"
                                    name="group_name"
                                    placeholder="Введите название группы (пример КОФс-75ИСиП2101)"
                                    max-length="50"
                                    required>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="edu_prog">Специальность:</label>
                                <select class="js-selectize" name="edu_prog" id="edu_prog" placeholder="Выберите специальность" required>
                                <option value=""></option>
                                $edu_prog_list
                                </select>
                            </div>
                            <div class="btns d-flex justify-content-between">
                                <button type="submit" class="btn-dialog">Добавить</button>
                                <button type="button" class="btn-dialog" id="addButtonCloseGroups">Отмена</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="item res-table d-flex justify-content-center">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>$sort_id</th>
                                <th>$sort_group</th>
                                <th>$sort_edu</th>
                                <th>$sort_base</th>
                                <th>$sort_form</th>
                                <th>$sort_program</th>
                            </tr>
                        </thead>
                        <tbody>
_START;
showTbodyGroups($groups);
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
    <script src="../assets/js/addDialogGroups.js"></script>
    <script>
    $(document).ready(function(){
        $('.js-selectize').selectize();
    });
    </script>
</body>
</html>
_END;
?>