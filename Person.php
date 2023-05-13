<?php
class Person
{
    private int $id;
    private string $name;
    private string $surname;
    private string $birthdate;
    private bool $gender;
    private string $birthplace;
    private $db;
    public function __construct(
        string $name,
        string $surname,
        string $birthdate,
        int $gender,
        string $birthplace,
        int $id = 0
    ) {
        $this->db = new PDO('mysql:host=localhost;dbname=test', 'root', 'root');
        $this->name = $name;
        $this->surname = $surname;
        $this->birthdate = $birthdate;
        $this->gender = $gender;
        $this->birthplace = $birthplace;
        $this->id = $id;
        // Валидация данных при создании экземпляра класса
        if (!$this->isValid()) {
            throw new InvalidArgumentException('Invalid data provided');
        }
    }
    private function isValid()
    {
        if (empty($this->name) || !ctype_alpha($this->name)) {
            return false;
        }
        if (empty($this->surname) || !ctype_alpha($this->surname)) {
            return false;
        }
        if (empty($this->birthdate) || !strtotime($this->birthdate)) {
            
            return false;
        }
        if ($this->gender != 0 && $this->gender != 1) {
            return false;
        }
        if (empty($this->birthplace) || !ctype_alpha(preg_replace('/\s+/', '', $this->birthplace))) {
            return false;
        }
        return true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getname(): string
    {
        return $this->name;
    }

    public function setname(string $name): void
    {
        $this->name = $name;
    }

    public function getsurname(): string
    {
        return $this->surname;
    }

    public function setsurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getbirthdate(): string
    {
        return $this->birthdate;
    }

    public function setbirthdate(string $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function getGender(): string
    {
        return $this->gender?"муж":"жен";
    }

    public function setGender(bool $gender): void
    {
        $this->gender = $gender;
    }

    public function getBirthplace(): string
    {
        return $this->birthplace;
    }

    public function setBirthplace(string $birthplace): void
    {
        $this->birthplace = $birthplace;
    }

    public function save()
    {
        
        if ($this->id) {
            // Обновляем запись
            $stmt = $this->db->prepare('UPDATE people SET name=?, surname=?, birthdate=?, gender=?, birthplace=? WHERE id=?');
            $stmt->execute([$this->name, $this->surname, $this->birthdate, $this->gender, $this->birthplace, $this->id]);
        } else {
            // Создаем новую запись
            $stmt = $this->db->prepare('INSERT INTO people (name, surname, birthdate, gender, birthplace) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$this->name, $this->surname, $this->birthdate, $this->gender, $this->birthplace]);
            $this->id = $this->db->lastInsertId();
        }
    }
    
    // Метод удаления записи из БД
    public function delete()
    {
        $stmt = $this->db->prepare('DELETE FROM people WHERE id=?');
        $stmt->execute([$this->id]);
    }

    public static function calculateAge(string $birthdate): int
    {
        $birthdateTimestamp = strtotime($birthdate);
        $nowTimestamp = time();
        $age = date('Y', $nowTimestamp) - date('Y', $birthdateTimestamp);
        if (date('md', $nowTimestamp) < date('md', $birthdateTimestamp)) {
            $age--;
        }
        return $age;
    }

    public static function getGenderText(bool $gender): string
    {
        return $gender ? 'муж' : 'жен';
    }

    public static function fromDb($id,$db=null)
    {
        if ($db){
            $stmt = $db->prepare('SELECT * FROM people WHERE id = :id');
        }else{
            $stmt = $this->db->prepare('SELECT * FROM people WHERE id = :id');
        }
        
        $stmt->execute(array(':id' => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new self($row['name'], $row['surname'], $row['birthdate'], $row['gender'], $row['birthplace'],$row['id']);
    }

        public function toStdClass(bool $convertAge = true, bool $convertGender = true): stdClass
    {
        $personObj = new stdClass();
        $personObj->id = $this->id;
        $personObj->name = $this->name;
        $personObj->surname = $this->surname;
        $personObj->birthdate = $this->birthdate;
        $personObj->gender = $this->gender;
        $personObj->birthplace = $this->birthplace;
        
        if ($convertAge) {
            $personObj->age = self::calculateAge($this->birthdate);
        }
        
        if ($convertGender) {
            $personObj->gender = self::getGender($this->gender);
        }
        
        return $personObj;
    }

}