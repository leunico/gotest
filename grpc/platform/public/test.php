<?php
// $start_mem = memory_get_usage();
// $arr = range(1, 10000);
// foreach ($arr as $item) {
//     // echo $item.',';
// }
// $end_mem = memory_get_usage();
// echo " use mem : ". ($end_mem - $start_mem) .'bytes'.PHP_EOL;

// $start_mem = memory_get_usage();
// function yield_range($start, $end)
// {
//     while ($start <= $end) {
//         $start++;
//         yield $start;
//     }
// }
// foreach (yield_range(0, 9999) as $item) {
//     echo $item.',';
// }
// $end_mem = memory_get_usage();
// echo " use mem : ". ($end_mem - $start_mem) .'bytes'.PHP_EOL;

// function yield_range($start, $end)
// {
//     while ($start <= $end) {
//         yield $start;
//         $start++;
//     }
// }
// $generator = yield_range(1, 10);
// // valid() current() next() 都是Iterator接口中的方法
// while ($generator->valid()) {
//     echo $generator->current().PHP_EOL;
//     $generator->next();
// }

function yield_range($start, $end)
{
    while ($start <= $end) {
        $ret = yield $start;
        $start++;
        echo "yield receive : ".$ret.PHP_EOL;
    }
}
$generator = yield_range(1, 10);
// $generator->send($generator->current() * 10);
foreach ($generator as $item) {
    $generator->send($generator->current() * 10);
}
