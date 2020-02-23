<?php
namespace BlogBundle\Helper;


class ConverHelper
{
    public static function filterwords($comment)
    {
        $badwords = [ "/(hello)/", "/(fuck)/","/(shit)/", "/(hell)/" ];

        $phrase = $comment;
       return preg_replace_callback(
            $badwords,
            function ($matches) {
                return str_repeat('*', strlen($matches[0]));
            },
            $phrase
        );

    }

}