<?php
if (!class_exists('Person')) {
    trigger_error('Class Person not found', E_USER_ERROR);
    return;
}

class PeopleList
{
    private $ids;
    private $db;
    public function __construct($db, $params)
    {
        
        $this->db = $db;
        $sql = 'SELECT id FROM people WHERE ';

        $i = 0;
        foreach ($params as $field => $value) {
            $op = '=';
            if (is_array($value) && count($value) === 2) {
                list($value, $op) = $value;
                $params[$field] = $value;
            }
            if ($i > 0) {
                $sql .= ' AND ';
            }
            $sql .= "$field $op ?";
            $i++;
        }
        var_dump($params);
        $stmt = $db->prepare($sql);
        $stmt->execute(array_values($params));

        $this->ids = array_column($stmt->fetchAll(), 'id');
    }

    public function getPeople()
    {
        $people = array();

        foreach ($this->ids as $id) {
            $person = Person::fromDb($id,$this->db );
            $people[] = $person;
        }

        return $people;
    }

    public function deletePeople()
    {
        foreach ($this->ids as $id) {
            $person = Person::fromDb($id,$this->db );
            $person->delete();
        }
    }
}
