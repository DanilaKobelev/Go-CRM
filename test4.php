<?php
$max = 100;
for ($count=0;$count<=$max;$count++) {
    print_r($arr[$count] = rand(-10,+10) . ', ');
}
$count=0;
$arr_double_info=[];
for($i=0;$i<$max;$i++) {
    $i_next = $i+1;
    if ($arr[$i] == $arr[$i_next]){
        $count++;
        $arr_double_info[] = "Номер: $i Номер :{$i_next}";
    }
}
echo '<br>' . PHP_EOL;
echo " Количество последовательных пар одинаковых элементов - " . $count . PHP_EOL;
echo '<pre>';
print_r($arr_double_info);
echo '</pre>';
?>
</body>
</html>