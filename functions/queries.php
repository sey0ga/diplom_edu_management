<?php
require_once __DIR__ . "/db.php";

// Универсальный запрос

function getAllData(string $table): array {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT * FROM $table");
    $stmt->execute();
    return $stmt->fetchAll();
}


// Студенты
function getAllStudents(string $orderBy='stud_id', string $search=""):  PDOStatement {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT 
    s.id AS `stud_id`,
    s.first_name AS `fname`,
    s.last_name AS `lname`,
    s.middle_name AS `mname`,
    DATE_FORMAT(s.birthday, '%d.%m.%Y') AS `birthday`,
    s.gender AS `gender`,
    s.address AS `address`,
    s.phone AS `phone`,
    g.name AS `group`,
    ma.new_course AS `course`,
    ep.code AS `code`,
    ep.name AS `edu_prog_name`,
    ep.edu_type AS `edu_type`,
    ep.edu_form AS `edu_form`,
    ep.edu_base AS `edu_base`
    FROM 
        students s
    LEFT JOIN (
        SELECT 
            sm.student_id,
            sm.id AS movement_id,
            sm.order_date
        FROM
            students_movements sm
        INNER JOIN (
            SELECT 
                student_id, 
                MAX(order_date) AS max_date
            FROM 
                students_movements
            GROUP BY 
                student_id
        ) latest ON sm.student_id = latest.student_id AND sm.order_date = latest.max_date
    ) latest_movement ON latest_movement.student_id = s.id
    LEFT JOIN movements_adds ma ON ma.movements_id = latest_movement.movement_id
    LEFT JOIN groups g ON g.id = ma.new_group_id
    LEFT JOIN educational_programs ep ON ep.id = g.edu_prog_id
    WHERE 
    (s.first_name LIKE '%$search%') OR 
    (s.last_name LIKE '%$search%') OR
    (s.middle_name LIKE '%$search%') OR 
    (DATE_FORMAT(s.birthday, '%d.%m.%Y') LIKE '%$search%') OR
    (s.gender LIKE '%$search%') OR 
    (s.address LIKE '%$search%') OR
    (g.name LIKE '%$search%') OR
    (ma.new_course LIKE '%$search%')
    ORDER BY 
        {$orderBy}");

    $stmt->execute();
    return $stmt;
}

function showTbodyStudents(array $students): void {
        foreach($students as $row) {
        $tbody = "<tr><td>" . $row['stud_id'] . "</td><td>" . $row['lname'] . "</td><td>" . 
        $row['fname'] . "</td><td>" . $row['mname'] . "</td><td>" . $row['birthday'] . "</td><td>" . 
        $row['gender'] . "</td><td>" . $row['group'] . "</td><td>" . $row['course'] . "</td></tr>";
        echo $tbody;
    }
}

function getListStudents(): string {
    $students = getAllStudents()->fetchAll();
    $res = "";
    foreach($students as $row) {
        $intag = $row['lname'] . " " . $row['fname'] . " " . $row['mname'] . " " .
            $row['group'] . " " . $row['course'] . " курс";
        $res .= sprintf("<option value='%d'>%s</option>", $row['stud_id'], $intag);
    }
    return $res;
}

function addStudent(string $last_name,
    string $first_name,
    string $middle_name,
    string $birthday,
    string $gender,
    string $address,
    string $phone,
    string $email): array {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO students(last_name,
        first_name,
        middle_name,
        birthday,
        gender,
        address,
        phone,
        email) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$last_name, $first_name, $middle_name, $birthday, $gender, $address, $phone, $email]);
    if ($pdo->lastInsertId()) {
        return array(true, $pdo->lastInsertId());
    } else {
        return array(false, $pdo->lastInsertId());
    }
}

// Движения
function getAllMovements(string $orderBy='id', string $search=""): PDOStatement {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT students_movements.id AS id,
        orders_types.name AS `order`,
        CONCAT_WS(' ', students.last_name,
            CONCAT(LEFT(students.first_name, 1), '.'),
            CONCAT(LEFT(students.middle_name, 1), '.')) AS `student`,
        DATE_FORMAT(students_movements.order_date, '%d.%m.%Y') AS `date`,
        groups.name AS `group`,
        movements_adds.new_course AS `course`
    FROM students_movements
    JOIN orders ON orders.id = students_movements.order_id
    JOIN orders_types ON orders_types.id = orders.type
    JOIN students ON students.id = students_movements.student_id
    JOIN movements_adds ON movements_adds.movements_id = students_movements.id
    JOIN groups ON groups.id = movements_adds.new_group_id
    WHERE CONCAT(students.first_name,
        students.last_name,
        students.middle_name,
        orders_types.name,
        DATE_FORMAT(students_movements.order_date, '%d.%m.%Y'),
        groups.name) LIKE '%$search%'
    ORDER BY {$orderBy}");

    $stmt->execute();
    return $stmt;
}

function showTbodyMovements(array $orders): void {
    foreach($orders as $row) {
        $res = "<tr><td>" . $row['id'] . "</td><td>" . $row['order'] . "</td><td>" . 
        $row['student'] . "</td><td>" . $row['date'] . "</td><td>" . $row['group'] . "</td><td>" . 
        $row['course'] . "</td></tr>";
        echo $res;
    }
}


function getListOrders(): string {
    $orders = getAllData('orders');
    $res = "";
    foreach($orders as $row) {
        $intag = $row['id'] . " " . $row['name'];
        $res .= sprintf("<option value='%d'>%s</option>", $row['id'], $intag);
    }
    return $res;
}

function getListOrdersTypes(): string {
    $orders_types = getAllData('orders_types');
    $res = "";
    foreach($orders_types as $row) {
        $intag = $row['name'];
        $res .= sprintf("<option value='%d'>%s</option>", $row['id'], $intag);
    }
    return $res;
}

function addOrder(string $order_name, int $type): array {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO orders(name, type) VALUES(?, ?)");
    $stmt->execute([$order_name, $type]);
    if ($pdo->lastInsertId()) {
        return array(true, $pdo->lastInsertId());
    } else {
        return array(false, $pdo->lastInsertId());
    }
}

function addMove(int $order_id, int $student_id, string $move_date, int $group_id, int $course): bool {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO students_movements(order_id, student_id, order_date) VALUES (?, ?, ?)");
    $stmt->execute([$order_id, $student_id, $move_date]);
    if ($pdo->lastInsertId()){
        $stmt = $pdo->prepare("INSERT INTO movements_adds(movements_id, new_group_id, new_course) VALUES(?, ?, ?)");
        $stmt->execute([$pdo->lastInsertId(), $group_id, $course]);
        return true;
    } else {
        return false;
    }
}

// Группы
function getAllGroups(string $orderBy='id', string $search=""): PDOStatement {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT groups.id AS `id`,
        groups.name as `name`,
        educational_programs.edu_type as `type`,
        educational_programs.edu_base as `base`,
        educational_programs.edu_form as `form`,
        CONCAT_WS(' ', educational_programs.code, educational_programs.name) as `program`
    FROM groups
    JOIN educational_programs ON educational_programs.id = groups.edu_prog_id
    WHERE CONCAT(groups.name,
        educational_programs.edu_type,
        educational_programs.edu_base,
        educational_programs.edu_form,
        educational_programs.code,
        educational_programs.name) LIKE '%$search%'
    ORDER BY {$orderBy}");

    $stmt->execute();
    return $stmt;
}

function showTbodyGroups(array $groups): void {
    foreach($groups as $row) {
        $res = "<tr><td>" . $row['id'] . "</td><td>" . $row['name'] . "</td><td>" . 
        $row['type'] . "</td><td>" . $row['base'] . "</td><td>" . $row['form'] . "</td><td>" . 
        $row['program'] . "</td></tr>";
        echo $res;
    }
}

function getListGroups(): string {
    $groups = getAllGroups()->fetchAll();
    $res = "";
    foreach($groups as $row) {
        $intag = $row['id'] . " " . $row['name'] . " " . $row['type']. " " . $row['base'] .
            " " . $row['form'] . " " . $row['program'];
        $res .= sprintf("<option value='%d'>%s</option>", $row['id'], $intag);
    }
    return $res;
}


function addGroup(string $group_name, int $edu_prog_id): bool {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO groups(name, edu_prog_id) VALUES (?, ?)");
    $stmt->execute([$group_name, $edu_prog_id]);
    if ($pdo->lastInsertId()) {
        return true;
    } else {
        return false;
    }
}

function getListEduProg(): string {
    $groups = getAllData('educational_programs');
    $res = "";
    foreach($groups as $row) {
        $intag = $row['id'] . " " . $row['code'] . " " . $row['name']. " " . $row['edu_type'] .
            " " . $row['edu_form'] . " На базе: " . $row['edu_base'] . "кл.";
        $res .= sprintf("<option value='%d'>%s</option>", $row['id'], $intag);
    }
    return $res;
}

// Успеваемость
function getAllGrades(string $orderBy='id', string $search=""): PDOStatement {
    $pdo = dbConnect();
    $stmt= $pdo->prepare("SELECT academic_perfomance.id AS `id`,
        CONCAT_WS(' ', students.last_name,
                CONCAT(LEFT(students.first_name, 1), '.'),
                CONCAT(LEFT(students.middle_name, 1), '.')) AS `student`,
        subjects.name AS `subject`,
        exams_types.type as `exam_type`,
        DATE_FORMAT(academic_perfomance.date, '%d.%m.%Y') as `date`,
        academic_perfomance.grade as `grade`
    FROM academic_perfomance
    JOIN students ON students.id = academic_perfomance.student_id
    JOIN exams_types ON exams_types.id = academic_perfomance.exam_type_id
    JOIN subjects ON subjects.id = academic_perfomance.subject_id
    WHERE CONCAT(students.first_name,
        students.last_name,
        students.middle_name,
        subjects.name,
        exams_types.type,
        DATE_FORMAT(academic_perfomance.date, '%d.%m.%Y'),
        academic_perfomance.grade) LIKE '%$search%'
    ORDER BY {$orderBy}");
    $stmt->execute();
    return $stmt;
}

function getListSubjects(): string {
    $subjects = getAllData("subjects");
    $res = "";
    foreach($subjects as $row) {
        $intag = $row['id'] . " " . $row['name'];
        $res .= sprintf("<option value='%d'>%s</option>", $row['id'], $intag);
    }
    return $res;
}


function getListExamTypes(): string {
    $exam_types = getAllData("exams_types");
    $res = "";
    foreach($exam_types as $row) {
        $intag = $row['type'];
        $res .= sprintf("<option value='%d'>%s</option>", $row['id'], $intag);
    }
    return $res;
}

function addGrade(int $student_id,
        int $subject_id,
        int $exam_type_id,
        string $exam_date,
        string $grade): bool {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO academic_perfomance(student_id, subject_id, exam_type_id, date, grade)
        VALUES(?, ?, ?, ?, ?)");
    $stmt->execute([$student_id, $subject_id, $exam_type_id, $exam_date, $grade]);
    if ($pdo->lastInsertId()) {
        return true;
    } else {
        return false;
    }
}

function addSubject(string $subject_name): bool {
    $pdo = dbConnect();
    $stmt = $pdo->prepare("INSERT INTO subjects(name) VALUES (?)");
    $stmt->execute([$subject_name]);
    if ($pdo->lastInsertId()) {
        return true;
    } else {
        return false;
    }
}

function showTbodyGrades(array $grades): void {
    foreach($grades as $row) {
        $res = "<tr><td>" . $row['id'] . "</td><td>" . $row['student'] . "</td><td>" . 
        $row['subject'] . "</td><td>" . $row['exam_type'] . "</td><td>" . $row['date'] . "</td><td>" . 
        $row['grade'] . "</td></tr>";
        echo $res;
    }
}
?>