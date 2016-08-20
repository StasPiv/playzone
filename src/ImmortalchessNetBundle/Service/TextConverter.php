<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.08.16
 * Time: 0:21
 */

namespace ImmortalchessNetBundle\Service;

use League\CLImate\CLImate;
use League\CLImate\TerminalObject\Dynamic\Progress;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TextConvertor
 * @package ImmortalchessNetBundle\Service
 */
class TextConverter implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var CLImate
     */
    private $climate;

    /**
     * @var array
     */
    private $russianLetters = [
        'А',
        'Б',
        'В',
        'Г',
        'Д',
        'Е',
        'Ё',
        'Ж',
        'З',
        'И',
        'Й',
        'К',
        'Л',
        'М',
        'Н',
        'О',
        'П',
        'Р',
        'С',
        'Т',
        'У',
        'Ф',
        'Х',
        'Ц',
        'Ч',
        'Ш',
        'Щ',
        'Ъ',
        'Ы',
        'Ь',
        'Э',
        'Ю',
        'Я',
        'а',
        'б',
        'в',
        'г',
        'д',
        'е',
        'ё',
        'ж',
        'з',
        'и',
        'й',
        'к',
        'л',
        'м',
        'н',
        'о',
        'п',
        'р',
        'с',
        'т',
        'у',
        'ф',
        'х',
        'ц',
        'ч',
        'ш',
        'щ',
        'ъ',
        'ы',
        'ь',
        'э',
        'ю',
        'я',
    ];

    /**
     * @var array
     */
    private $abrakadabraLetters = [
        'Ð',
        'Ð‘',
        'Ð’',
        'Ð“',
        'Ð”',
        'Ð•',
        'Ð',
        'Ð–',
        'Ð—',
        'Ð˜',
        'Ð™',
        'Ðš',
        'Ð›',
        'Ðœ',
        'Ð',
        'Ðž',
        'ÐŸ',
        'Ð ',
        'Ð¡',
        'Ð¢',
        'Ð£',
        'Ð¤',
        'Ð¥',
        'Ð¦',
        'Ð§',
        'Ð¨',
        'Ð©',
        'Ðª',
        'Ð«',
        'Ð¬',
        'Ð­',
        'Ð®',
        'Ð¯',
        'Ð°',
        'Ð±',
        'Ð²',
        'Ð³',
        'Ð´',
        'Ðµ',
        'Ñ‘',
        'Ð¶',
        'Ð·',
        'Ð¸',
        'Ð¹',
        'Ðº',
        'Ð»',
        'Ð¼',
        'Ð½',
        'Ð¾',
        'Ð¿',
        'Ñ€',
        'Ñ',
        'Ñ‚',
        'Ñƒ',
        'Ñ„',
        'Ñ…',
        'Ñ†',
        'Ñ‡',
        'Ñˆ',
        'Ñ‰',
        'ÑŠ',
        'Ñ‹',
        'ÑŒ',
        'Ñ',
        'ÑŽ',
        'Ñ',
    ];

    /**
     * TextConverter constructor.
     */
    public function __construct()
    {
        $this->climate = new CLImate();
    }

    /**
     * @param string $fileInName
     * @param string $fileOutName
     * @param bool $output
     */
    public function convertTextFile(string $fileInName, string $fileOutName = null, bool $output = false)
    {
        if (!$fileOutName) {
            $fileOutName = $fileInName;
        }

        $fileHandler = fopen($fileInName, 'r');

        /** @var Progress $progress */
        $progress = $this->climate->progress();

        if ($output) {
            $progress->total($this->getCountLines($fileInName) + 1);
        }

        $counter = 0;
        while (false !== ($line = fgets($fileHandler))) {
            if ($output) {
                $progress->current(++$counter);
            }
            file_put_contents($fileOutName, $this->convertTextToNormal($line), FILE_APPEND);
        }
    }

    /**
     * @param string $filename
     * @return int
     */
    private function getCountLines(string $filename): int
    {
        $linecount = 0;
        /** @var Progress $progress */
        $progress = $this->climate->progress()->total(10000000);
        $handle = fopen($filename, "r");
        while (!feof($handle)) {
            $progress->advance();
            fgets($handle);
            $linecount++;
        }

        fclose($handle);

        return $linecount;
    }

    /**
     * @param string $normalText
     * @return string
     */
    public function convertTextFromNormal(string $normalText): string
    {
        return str_replace(
            $this->russianLetters,
            $this->abrakadabraLetters,
            $normalText
        );
    }

    /**
     * @param string $abrakadabraText
     * @return string
     */
    public function convertTextToNormal(string $abrakadabraText): string
    {
        return str_replace(
            $this->abrakadabraLetters,
            $this->russianLetters,
            $abrakadabraText
        );
    }
}