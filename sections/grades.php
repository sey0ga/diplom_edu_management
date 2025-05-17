<?php
require_once __DIR__ . "/../functions/queries.php";
require_once __DIR__ . "/../functions/auth.php";
require_once __DIR__ . "/../functions/sort.php";
require_once __DIR__ . "/../functions/export.php";

requireAuth();

$username = $_SESSION['username'];

$search_query = "";
$grades = getAllGrades();

if (isset($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);
    $search_query = $search;
    if (!isset($_GET['sort'])){
        $grades = getAllGrades(search: $search);
    }
    
}

if (isset($_GET['sort'])) {
    if (empty($search)) {
    $search = "";
    }

    $sort = htmlspecialchars($_GET['sort']);
    if (!array_key_exists($sort, $sort_list['grades'])) {
        header("Location: /dean_office/sections/grades.php");
        exit();
    }
    $sort_sql = $sort_list['grades'][$sort];
    $grades = getAllGrades(orderBy: $sort_sql, search: $search);
}

if (isset($_POST['student']) && isset($_POST['subject']) && isset($_POST['exam_type']) &&
    isset($_POST['exam_date']) && isset($_POST['grade'])) {
    $insertResult = addGrade($_POST['student'],
        $_POST['subject'],
        $_POST['exam_type'],
        $_POST['exam_date'],
        $_POST['grade']);

    if ($insertResult) {
        header("Location: /dean_office/sections/grades.php");
        exit();
    }
} else if (isset($_POST['subject_name'])) {
    $insertResult = addSubject($_POST['subject_name']);
    if ($insertResult) {
        header("Location: /dean_office/sections/grades.php");
        exit();
    }
}

if (isset($_POST['export_grades'])) {
    exportToCsv($grades, "grades_" . date('Y-m-d H:i:s') . ".csv");
}

$sort_id = sort_link_th('id', 'id_asc', 'id_desc', $search);
$sort_student = sort_link_th('Студент', 'student_asc', 'student_desc', $search);
$sort_subject = sort_link_th('Предмет', 'subject_asc', 'subject_desc', $search);
$sort_type = sort_link_th('Тип зачета', 'exam_type_asc', 'exam_type_desc', $search);
$sort_date = sort_link_th('Дата', 'date_asc', 'date_desc', $search);
$sort_grade = sort_link_th('Оценка', 'grade_asc', 'grade_desc', $search);

$students_list = getListStudents();
$subjects_list = getListSubjects();
$exam_types = getListExamTypes();

$grades = $grades->fetchAll();

echo <<< _START
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components/selectize.default.css">
    <title>Успеваемость</title>
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
        <div class="grades">
            <div class="container d-flex flex-column">
                <h3 class="text-center">Успеваемость</h3>
                <div class="pre-table d-flex justify-content-between">
                    <div class="action_button d-flex justify-content-between">
                        <button class="btn-dialog" id="addButtonOpenGrades">Добавить запись (оценка)</button>
                        <button class="btn-dialog" id="addButtonOpenSubject">Добавить запись (предмет)</button>
                    </div>
                    <div class="export-search d-flex flex-column justify-content-between">
                        <form class="export-form d-flex" action="" method="POST">
                            <input type="hidden" name="export_grades" value="export">
                            <button type="submit" class="btn-dialog">Выгрузка CSV</button>
                        </form>
                        <form class="search-form d-flex" action="" method="GET">
                            <input type="text" name="search" placeholder="Поиск" value="$search_query">
                            <button type="submit">🔎</button>
                        </form>
                    </div>
                </div>
                <div id="overlay"></div>
                <div id="addDialogGrades" class="d-flex flex-column">
                    <h3 class="text-center">Добавить запись (движения)</h3>
                    <form id="addGrade" action="" method="POST">
                        <div class="add-form d-flex flex-column">
                            <div class="item d-flex flex-column">
                                <label for="student">Студент:</label>
                                <select class="js-selectize" name="student" id="students" placeholder="Выберите обучающегося" required>
                                    <option value=""></option>
                                    $students_list
                                </select>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="subject">Предмет:</label>
                                <select class="js-selectize" name="subject" id="subject" placeholder="Выберите предмет" required>
                                    <option value=""></option>
                                    $subjects_list
                                </select>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="exam_type">Тип:</label>
                                <select class="js-selectize" name="exam_type" id="exam_type" placeholder="Выберите тип" required>
                                    <option value=""></option>
                                    $exam_types
                                </select>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="exam_date">Дата:</label>
                                <input type="date" class="selectize-input" name="exam_date" id="exam_date" required>
                            </div>
                            <div class="item d-flex flex-column">
                                <label for="grade">Оценка:</label>
                                <input type="text"
                                        name="grade"
                                        id="grade"
                                        class="selectize-input"
                                        placeholder="Введите оценку"
                                        max-length="30"
                                        required>
                            </div>
                            <div class="btns d-flex justify-content-between">
                                <button type="submit" class="btn-dialog">Добавить</button>
                                <button type="button" class="btn-dialog" id="addButtonCloseGrades">Отмена</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="addDialogSubject" class="d-flex flex-column">
                    <h3 class="text-center">Добавить запись (предмет)</h3>
                    <form id="addSubject" action="" method="POST">
                        <div class="add-form d-flex flex-column">
                            <div class="item d-flex flex-column">
                                <label for="subject_name">Название:</label>
                                <input type="text" id="subject_name" name="subject_name" placeholder="Введите название дисциплины">
                            </div>
                            <div class="btns d-flex justify-content-between">
                                <button type="submit" class="btn-dialog">Добавить</button>
                                <button type="button" class="btn-dialog" id="addButtonCloseSubject">Отмена</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="item res-table d-flex justify-content-center">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>$sort_id</th>
                                <th>$sort_student</th>
                                <th>$sort_subject</th>
                                <th>$sort_type</th>
                                <th>$sort_date</th>
                                <th>$sort_grade</th>
                            </tr>
                        </thead>
                        <tbody>
_START;
showTbodyGrades($grades);
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
    <script src="../assets/js/addDialogGrades.js"></script>
    <script>
    $(document).ready(function(){
        $('.js-selectize').selectize();
    });
    </script>
</body>
</html>
_END;
?>