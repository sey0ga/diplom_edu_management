<?php
require_once __DIR__ . "/../functions/queries.php";
require_once __DIR__ . "/../functions/auth.php";
require_once __DIR__ . "/../functions/sort.php";
require_once __DIR__ . "/../functions/export.php";

requireAuth();

$username = $_SESSION['username'];

$search_query = "";
$students = getAllStudents();

if (isset($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);
    $search_query = $search;
    if (!isset($_GET['sort'])){
        $students = getAllStudents(search: $search);
    }
    
}

if (isset($_GET['sort'])) {
    if (empty($search)) {
    $search = "";
    }

    $sort = htmlspecialchars($_GET['sort']);
    if (!array_key_exists($sort, $sort_list['students'])) {
        header("Location: /dean_office/sections/students.php");
        exit();
    }
    $sort_sql = $sort_list['students'][$sort];
    $students = getAllStudents(orderBy: $sort_sql, search: $search);
}

if (isset($_POST['stud_last_name']) && isset($_POST['stud_first_name']) &&
    isset($_POST['stud_date']) && isset($_POST['stud_gender'])) {
    $stud_last_name = $_POST['stud_last_name'];
    $stud_first_name = $_POST['stud_first_name'];
    $stud_middle_name = "" . $_POST['stud_middle_name'];
    $stud_date = $_POST['stud_date'];
    $stud_gender = $_POST['stud_gender'];
    $stud_phone = "" . $_POST['stud_phone'];
    $stud_email = "" . $_POST['stud_email'];
    $stud_address = "" . $_POST['stud_address'];
    $insertResult = addStudent($stud_last_name,
        $stud_first_name,
        $stud_middle_name,
        $stud_date,
        $stud_gender,
        $stud_address,
        $stud_phone,
        $stud_email);
    if ($insertResult) {
        header("Location: /dean_office/sections/students.php");
        exit();
    }
}

if (isset($_POST['export_students'])) {
    exportToCsv($students, "students_" . date('Y-m-d H:i:s') . ".csv");
}

$sort_id = sort_link_th('id', 'id_asc', 'id_desc', $search);
$sort_lname = sort_link_th('Фамилия', 'lname_asc', 'lname_desc', $search);
$sort_fname = sort_link_th('Имя', 'fname_asc', 'fname_desc', $search);
$sort_mname = sort_link_th('Отчество', 'mname_asc', 'mname_desc', $search);
$sort_birthday = sort_link_th('Дата рождения', 'birthday_asc', 'birthday_desc', $search);
$sort_gender = sort_link_th('Пол', 'gender_asc', 'gender_desc', $search);
$sort_group = sort_link_th('Группа', 'group_asc', 'group_desc', $search);
$sort_course = sort_link_th('Курс', 'course_asc', 'course_desc', $search);

$students = $students->fetchAll();

echo <<< _START
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components/selectize.default.css">
    <title>Студенты</title>
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
        <div class="students">
            <div class="container d-flex flex-column">
                <h3 class="text-center">Студенты</h3>
                <div class="pre-table d-flex justify-content-between">
                    <div class="action_button d-flex justify-content-between">
                        <button class="btn-dialog" id="addButtonOpenStudents">Добавить запись (студент)</button>
                    </div>
                    <div class="export-search d-flex flex-column justify-content-between">
                        <form class="export-form d-flex" action="" method="POST">
                            <input type="hidden" name="export_students" value="export">
                            <button type="submit" class="btn-dialog">Выгрузка CSV</button>
                        </form>
                        <form class="search-form d-flex" action="" method="GET">
                            <input type="text" name="search" placeholder="Поиск" value="$search_query">
                            <button type="submit">🔎</button>
                        </form>
                    </div>
                </div>
                <div id="overlay"></div>
                <div id="addDialogStudents" class="d-flex flex-column">
                    <h3 class="text-center">Добавить запись (студент)</h3>
                    <form id="addStudent" action="" method="POST">
                        <div class="add-form d-flex flex-column">
                            <div class="item d-flex flex-column">
                                <label for="stud_last_name">Фамилия:</label>
                                <input type="text"
                                    id="stud_last_name"
                                    name="stud_last_name"
                                    placeholder="Введите фамилию"
                                    max-length="50"
                                    required>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="stud_first_name">Имя:</label>
                                <input type="text"
                                    id="stud_first_name"
                                    name="stud_first_name"
                                    placeholder="Введите имя"
                                    max-length="50"
                                    required>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="stud_middle_name">Отчество:</label>
                                <input type="text"
                                    id="stud_middle_name"
                                    name="stud_middle_name"
                                    placeholder="Введите Отчество"
                                    max-length="50">
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="stud_date">Дата рождения:</label>
                                <input type="date" class="selectize-input" name="stud_date" id="stud_date" required>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="stud_gender">Пол:</label>
                                <select class="js-selectize" name="stud_gender" id="stud_gender" placeholder="Укажите пол" required>
                                    <option value=""></option>
                                    <option value="М">Муж.</option>
                                    <option value="Ж">Жен.</option>
                                </select>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="stud_address">Адрес проживания:</label>
                                <input type="text"
                                    id="stud_address"
                                    name="stud_address"
                                    placeholder="Введите адрес"
                                    max-length="255">
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="stud_phone">Номер телефона:</label>
                                <input type="text"
                                    id="stud_phone"
                                    name="stud_phone"
                                    placeholder="Введите телефон"
                                    max-length="15">
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="stud_email">Электронная почта:</label>
                                <input type="text"
                                    id="stud_email"
                                    name="stud_email"
                                    placeholder="Введите почту"
                                    max-length="100">
                            </div>
                            <div class="btns d-flex justify-content-between">
                                <button type="submit" class="btn-dialog">Добавить</button>
                                <button type="button" class="btn-dialog" id="addButtonCloseStudents">Отмена</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="item res-table d-flex justify-content-center">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>$sort_id</th>
                                <th>$sort_lname</th>
                                <th>$sort_fname</th>
                                <th>$sort_mname</th>
                                <th>$sort_birthday</th>
                                <th>$sort_gender</th>
                                <th>$sort_group</th>
                                <th>$sort_course</th>
                            </tr>
                        </thead>
                        <tbody>
_START;
showTbodyStudents($students);
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
    <script src="../assets/js/addDialogStudents.js"></script>
    <script>
    $(document).ready(function(){
        $('.js-selectize').selectize();
    });
    </script>
</body>
</html>
_END;
?>