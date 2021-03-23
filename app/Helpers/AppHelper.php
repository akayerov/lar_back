<?php

    namespace App\Helpers;


    class AppHelper
    {

        public static function translit($value)
        {        
            $map = array(
                'А' => 'A','Б' => 'B','В' => 'V','Г'=>'G','Д'=>'D','Е'=>'E',
                'Ж'=>'Z','З'=>'S', 'И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M',
                'Н'=>'N','О'=>'O','П'=>'P','Р'=>'R',
                'C'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ы'=>'I','Ь'=>'','Э'=>'E',
                'Ъ'=>'','Ю'=>'U','Я'=>'Y',
                'а' => 'a','б' => 'b','в' => 'v','г'=>'g','д'=>'d','е'=>'e',
                'ж'=>'z','з'=>'s', 'и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m',
                'н'=>'n','о'=>'o','п'=>'p','р'=>'r',
                'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ы'=>'i','ь'=>'','э'=>'e',
                'ъ'=>'','ю'=>'u','я'=>'y',
            );
            return  strtr($value,$map);
        }

        public static function bcrypt($value, $options = [])
        {
            return app('hash')->make($value, $options);
        }

        public static function get_value($object, $arr) {
            $field = array_shift($arr);

            if ($field !== null):
                $result = $object->$field ? self::get_value($object->$field, $arr) : null;
            else:
                $result = $object;
            endif;

            return $result;
        }
    }
