# think-pscws4

基于 [PSCWS4](http://www.ftphp.com/scws/) 的纯 PHP 中文分词，适配 ThinkPHP 8。

## 安装

```bash
composer require youvision/think-pscws4
```

服务通过 `extra.think.services` 自动注册，无需手动添加。

## 配置

包内默认配置位于 `config/pscws4.php`，可在应用的 `config/pscws4.php` 中覆盖：

```php
return [
    'dict'    => '',                                  // 词典文件，留空使用包内 etc/dict.xdb
    'rule'    => '',                                  // 规则集，留空使用包内 etc/rules.utf8.ini
    'ignore'  => true,                                // 忽略标点等特殊符号
    'multi'   => 0,                                   // 复合分词等级 0~15
    'duality' => false,                               // 散字二元聚合
    'debug'   => false,                               // 调试信息
];
```

> 词典文件 `dict.xdb` 为必需文件，请放置到 `etc/dict.xdb`，或在配置中填写绝对路径。
> `dict` / `rule` 配置为相对路径时，相对应用根目录解析。

## 使用

门面：

```php
use Youvision\ThinkPscws4\Facade;

$words = Facade::words('中国航天官员应邀到美国开会');
$text  = Facade::cut('中国航天官员应邀到美国开会', ' / ');
$tops  = Facade::keywords('中国航天官员应邀到美国开会', 10, 'n,v');
```

容器注入：

```php
use Youvision\ThinkPscws4\Pscws4Manager;

class Article
{
    public function __construct(protected Pscws4Manager $pscws4)
    {
    }

    public function keywords(string $content): array
    {
        return $this->pscws4->keywords($content, 5);
    }
}
```

## 方法

| 方法 | 说明 |
| --- | --- |
| `words(string $text): array` | 返回词语列表 |
| `cut(string $text, string $separator = ' '): string` | 返回以分隔符连接的分词结果 |
| `segment(string $text): array` | 返回完整分词结果（含 word/off/idf/len/attr） |
| `keywords(string $text, int $limit = 10, string $attr = ''): array` | 提取关键词 |
| `engine(): Pscws4` | 获取底层 PSCWS4 实例 |
| `version(): string` | 版本号 |
| `close(): void` | 释放词典资源 |

## 许可证

核心分词实现版权归原作者 hightman 所有，详见 [docs/PSCWS4.txt](docs/PSCWS4.txt)。