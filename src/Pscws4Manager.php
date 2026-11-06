<?php

namespace Youvision\ThinkPscws4;

use RuntimeException;

class Pscws4Manager
{
    protected array $config;

    protected string $basePath;

    protected ?Pscws4 $engine = null;

    public function __construct(array $config = [], string $basePath = '')
    {
        $defaults = require __DIR__ . '/../config/pscws4.php';

        $this->config = array_merge($defaults, $config);
        $this->basePath = $basePath;
    }

    public function engine(): Pscws4
    {
        if ($this->engine === null) {
            $engine = new Pscws4('utf8');

            $dict = $this->resolvePath((string) ($this->config['dict'] ?? ''));
            if ($dict === '') {
                $dict = __DIR__ . '/../etc/dict.xdb';
            }
            if (!is_file($dict)) {
                throw new RuntimeException('PSCWS4 词典文件不存在：' . $dict);
            }
            if (!$engine->set_dict($dict)) {
                throw new RuntimeException('PSCWS4 词典加载失败：' . $dict);
            }

            $rule = $this->resolvePath((string) ($this->config['rule'] ?? ''));
            if ($rule === '') {
                $rule = __DIR__ . '/../etc/rules.utf8.ini';
            }
            if (is_file($rule)) {
                $engine->set_rule($rule);
            }

            $engine->set_ignore((bool) ($this->config['ignore'] ?? true));
            $engine->set_multi((int) ($this->config['multi'] ?? 0));
            $engine->set_duality((bool) ($this->config['duality'] ?? false));
            $engine->set_debug((bool) ($this->config['debug'] ?? false));

            $this->engine = $engine;
        }

        return $this->engine;
    }

    /**
     * 分词并返回完整结果。
     *
     * @return array<int, array{word: string, off: int, idf: float, len: int, attr: string}>
     */
    public function segment(string $text): array
    {
        $engine = $this->engine();
        $engine->send_text($text);

        $result = [];
        while ($words = $engine->get_result()) {
            foreach ($words as $word) {
                $result[] = $word;
            }
        }

        return $result;
    }

    /**
     * 仅返回词语列表。
     *
     * @return string[]
     */
    public function words(string $text): array
    {
        return array_column($this->segment($text), 'word');
    }

    /**
     * 以指定分隔符返回分词字符串。
     */
    public function cut(string $text, string $separator = ' '): string
    {
        return implode($separator, $this->words($text));
    }

    /**
     * 提取关键词。
     *
     * @return array<int, array{word: string, times: int, weight: float, attr: string}>
     */
    public function keywords(string $text, int $limit = 10, string $attr = ''): array
    {
        $engine = $this->engine();
        $engine->send_text($text);

        return $engine->get_tops($limit, $attr) ?: [];
    }

    public function version(): string
    {
        return $this->engine()->version();
    }

    public function close(): void
    {
        if ($this->engine !== null) {
            $this->engine->close();
            $this->engine = null;
        }
    }

    protected function resolvePath(string $path): string
    {
        $path = trim($path);
        if ($path === '' || $this->isAbsolutePath($path)) {
            return $path;
        }

        return rtrim($this->basePath, '/\\') . DIRECTORY_SEPARATOR . $path;
    }

    protected function isAbsolutePath(string $path): bool
    {
        return (bool) preg_match('#^(?:[a-zA-Z]:[\\\\/]|[\\\\/])#', $path);
    }
}