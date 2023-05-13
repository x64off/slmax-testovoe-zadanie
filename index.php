<?php
require_once 'Person.php';
require_once 'PeopleList.php';
// Создание нового человека в БД
$person = new Person('John', 'Doe', '1990-05-25', 1, 'Minsk');
$person->save();
$db = new PDO('mysql:host=localhost;dbname=test', 'root', 'root');
// Получение данных из БД и форматирование
$person_from_db = Person::fromDb($person->getId(),$db);
$formatted_person = $person_from_db->toStdClass(true, true);

// Вывод данных на экран
echo "ID: " . $formatted_person->id . "<br>";
echo "Имя: " . $formatted_person->name . "<br>";
echo "Фамилия: " . $formatted_person->surname . "<br>";
echo "Дата рождения: " . $formatted_person->birthdate . "<br>";
echo "Возраст: " . $formatted_person->age . "<br>";
echo "Пол: " . $formatted_person->gender . "<br>";
echo "Город рождения: " . $formatted_person->birthplace . "<br>";
// $person_from_db->delete();
// Поиск
$params = array(
    'name' => 'John',
    'birthdate' => array('1990-01-01', '>=')
);
$peopleList = new PeopleList($db, $params);
// Получение Людей
$people = $peopleList->getPeople();
var_dump($people);
// Удаление
$peopleList->deletePeople();