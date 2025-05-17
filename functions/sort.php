<?php
$sort_list = array(
    'students'  =>  array(
        'id_asc'            =>  '`stud_id`',
        'id_desc'           =>  '`stud_id` DESC',
        'lname_asc'         =>  '`lname`',
        'lname_desc'        =>  '`lname` DESC',
        'mname_asc'         =>  '`mname`',
        'mname_desc'        =>  '`mname` DESC',
        'fname_asc'         =>  '`fname`',
        'fname_desc'        =>  '`fname` DESC',
        'birthday_asc'      =>  '`birthday`',
        'birthday_desc'     =>  '`birthday` DESC',
        'gender_asc'        =>  '`gender`',
        'gender_desc'       =>  '`gender` DESC',
        'group_asc'         =>  '`group`',
        'group_desc'        =>  '`group` DESC',
        'course_asc'        =>  '`course`',
        'course_desc'       =>  '`course` DESC',
    ),
    'orders'    =>  array(
        'id_asc'            =>  '`id`',
        'id_desc'           =>  '`id` DESC',
        'order_asc'         =>  '`order`',
        'order_desc'        =>  '`order` DESC',
        'student_asc'       =>  '`student`',
        'student_desc'      =>  '`student` DESC',
        'date_asc'          =>  '`order_date`',
        'date_desc'         =>  '`order_date` DESC',
        'group_asc'         =>  '`group`',
        'group_desc'        =>  '`group` DESC',
        'course_asc'        =>  '`course`',
        'course_desc'       =>  '`course` DESC',
    ),
    'groups'    =>  array(
        'id_asc'            =>  '`id`',
        'id_desc'           =>  '`id` DESC',
        'name_asc'          =>  '`name`',
        'name_desc'         =>  '`name` DESC',
        'type_asc'          =>  '`type`',
        'type_desc'         =>  '`type` DESC',
        'base_asc'          =>  '`base`',
        'base_desc'         =>  '`base` DESC',
        'form_asc'          =>  '`form`',
        'form_desc'         =>  '`form` DESC',
        'program_asc'       =>  '`program`',
        'program_desc'      =>  '`program` DESC',
    ),
    'grades'    =>  array(
        'id_asc'            =>  '`id`',
        'id_desc'           =>  '`id` DESC',
        'student_asc'       =>  '`student`',
        'student_desc'      =>  '`student` DESC',
        'subject_asc'       =>  '`subject`',
        'subject_desc'      =>  '`subject` DESC',
        'exam_type_asc'     =>  '`exam_type`',
        'exam_type_desc'    =>  '`exam_type` DESC',
        'date_asc'          =>  '`date`',
        'date_desc'         =>  '`date` DESC',
        'grade_asc'         =>  '`grade`',
        'grade_desc'        =>  '`grade` DESC',
    ),
);

function sort_link_th($title, $a, $b, $search=""): string {
    $params = [
        'sort'  => $a,
    ];
    if ($search){
        $params['search'] = $search;
    }
    $sort = $_GET['sort'];
    if ($sort == $a) {
        $params['sort'] = $b;
		return '<a class="active" href="' . '?'.http_build_query($params) . '">' . $title . ' <i>▲</i></a>';
	} elseif ($sort == $b) {
		return '<a class="active" href="' . '?'.http_build_query($params) . '">' . $title . ' <i>▼</i></a>';  
	} else {
		return '<a href="' . '?'.http_build_query($params) . '">' . $title . '</a>';  
	}
}
?>