<style>table {
        width: 100%;
    }

    td, table {
        border: solid 1px;
    }
</style>
<?php

require_once "const.php";
const SAVE_JSON = 'phonebook.json';
function drawTable(string $user, bool $admin = false): string
{
    if (!file_exists(SAVE_JSON)) file_put_contents(SAVE_JSON, json_encode(['keys' => ['adder', 'name', 'phone']]));

    $file = (array)json_decode(file_get_contents(SAVE_JSON));
    $th = $file['keys'];    //разделы
    $td = $file['values'];  //данные
    $container = [];
    $ret = '<table><tr>';
    $id = 0;
    foreach ($th as $o) {
        $container[$id++] = $o;
        $ret .= '<th>' . $o . '</th>';
    }
    $cs = sizeof($container);
    $ret .= '</tr>';
    foreach ($td as $o) {
        $o = (array)$o;
        $ret .= '<tr>';
        for ($i = 0; $i < $cs; $i++) $ret .= '<td>' . $o[$container[$i]] . '</td>';
        $ret .= '</tr>';
    }
    $ret .= '</table><div>'
        . ($user === 'unknown' ?
            'If you want to add phone to this, you need auth' :
            'You can add a new phone to this phonebook' .
            '<form action="/?action=' . A_ADDP . '" method="POST">
            <input name="' . A_PONM . '" type="text" placeholder="Phone owner"/>
            <input name="' . A_PHON . '" type="text" placeholder="Phone number"/>
            <button type="submit">Add</button></form>'
        ) . '</div>';
    return $ret;
}

function addPhone(string $adder = null, string $name = null, string $phone = null)
{
    if ($adder == null || $phone == null || $name == null) {
        print_r([$adder, $phone, $name]);
        die;
    }
    $file = (array)json_decode(file_get_contents(SAVE_JSON));
    $file['values'][] = ['adder' => $adder, 'name' => $name, 'phone' => $phone];
    file_put_contents(SAVE_JSON, json_encode($file));
    header("Location: /");
}