<?php

namespace Youvision\ThinkPscws4;

use think\Facade as ThinkFacade;

/**
 * @method static Pscws4 engine()
 * @method static array segment(string $text)
 * @method static string[] words(string $text)
 * @method static string cut(string $text, string $separator = ' ')
 * @method static array keywords(string $text, int $limit = 10, string $attr = '')
 * @method static string version()
 * @method static void close()
 *
 * @see \Youvision\ThinkPscws4\Pscws4Manager
 */
class Facade extends ThinkFacade
{
    protected static function getFacadeClass(): string
    {
        return Pscws4Manager::class;
    }
}