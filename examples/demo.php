<?php

require __DIR__ . '/../vendor/autoload.php';

use Youvision\ThinkPscws4\Pscws4Manager;

$manager = new Pscws4Manager([
    'dict' => __DIR__ . '/../etc/dict.xdb',
    'rule' => __DIR__ . '/../etc/rules.utf8.ini',
]);

$text = '中国航天官员应邀到美国与太空总署官员开会';

echo '词语：' . implode(' / ', $manager->words($text)) . PHP_EOL;
echo '分词：' . $manager->cut($text, '|') . PHP_EOL;

echo '关键词：' . PHP_EOL;
foreach ($manager->keywords($text, 5) as $item) {
    printf("%s\t%s\t%d\t%.2f\n", $item['word'], $item['attr'], $item['times'], $item['weight']);
}

$manager->close();