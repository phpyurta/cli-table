<?php

declare(strict_types=1);

namespace PHPYurta\CLI;

class CLICell
{
    public function __construct(
        protected string $text,
        protected int $width = 0,
    ) {
        $this->text = preg_replace([
            '/[\n\t]/', // remove tabs and new lines
            '/\s+/', // remove extra spaces
        ], [
            ' ',
            ' ',
        ], trim($this->text));
    }

    public function __toString(): string
    {
        return $this->getCellOutput();
    }

    public function getCellOutput(): string
    {
        $output = $this->strPad($this->text, $this->width);


        return $output;
    }

    public function setWidth(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Multibyte version of str_pad() function
     * @source https://www.php.net/str_pad
     * @source https://www.php.net/mb_str_pad
     */
    private function strPad($str, $padLength, string $padString = ' ', $padDirection = STR_PAD_RIGHT): string
    {
        if (function_exists('mb_str_pad')) // if php higher 8.3.0 https://www.php.net/mb_str_pad
        {
            return mb_str_pad($str, $padLength, $padString, $padDirection);
        }

        return $this->mb_str_pad($str, $padLength, $padString, $padDirection);
    }

     private function mb_str_pad(
        string $str,
        int $width,
        ?string $padStr = ' ',
        ?int $padDirection = STR_PAD_RIGHT,
        ?string $encoding = null
    ): string
    {
        $encoding ??= mb_internal_encoding();
        $cleanText = preg_replace([
            '/\033\[.+/' // remove terminal graphic symbols
        ], [
            ''
        ], $str);
        $padUnprintingSymbols = mb_strlen($str, $encoding) - mb_strlen($cleanText, $encoding);
        $padBefore = $padDirection === STR_PAD_BOTH || $padDirection === STR_PAD_LEFT;
        $padAfter = $padDirection === STR_PAD_BOTH || $padDirection === STR_PAD_RIGHT;
        $padLength = $width - mb_strlen($str, $encoding) + $padUnprintingSymbols;

        $targetLen = $padBefore && $padAfter ? $padLength / 2 : $padLength;
        $strToRepeatLen = mb_strlen($padStr, $encoding);
        $repeatTimes = (int) ceil($targetLen / $strToRepeatLen);
        $repeatedString = str_repeat($padStr, max(0, $repeatTimes)); // safe if used with valid utf-8 strings
        $before = $padBefore ? mb_substr($repeatedString, 0, (int) $targetLen, $encoding) : '';
        $after = $padAfter ? mb_substr($repeatedString, 0, (int) $targetLen, $encoding) : '';

        return $before . $str . $after;
    }
}
