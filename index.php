<?php

class FileLineIterator implements Iterator
{
    private $file;
    private $key = 0;
    private $currentLine;

    public function __construct($filePath)
    {
        $this->file = fopen($filePath, 'r');
        if (!$this->file) {
            throw new Exception("Не удалось открыть файл: $filePath");
        }
    }

    public function __destruct()
    {
        if ($this->file) {
            fclose($this->file);
        }
    }

    public function rewind(): void
    {
        if ($this->file) {
            fseek($this->file, 0);
            $this->key = 0;
            $this->next();
        }
    }

    public function current(): string
    {
        return $this->currentLine;
    }

    public function key(): int
    {
        return $this->key;
    }

    public function next(): void
    {
        if ($this->file) {
            $this->currentLine = fgets($this->file);
            $this->key++;
        }
    }

    public function valid(): bool
    {
        return $this->currentLine !== false;
    }
}


$inputFile = "input.html";
$outputFile = "output.html";

$patterns = [
    '/<title[^>]*>.*?<\/title>/i',
    '/<meta[^>]+name="description"[^>]*>/i',
    '/<meta[^>]+name="keywords"[^>]*>/i'
];

try {
    $fileIterator = new FileLineIterator($inputFile);
    $output = fopen($outputFile, 'w');

    if (!$output) {
        throw new Exception("Не удалось открыть файл: $outputFile");
    }

    foreach ($fileIterator as $line) {
        $cleanedLine = preg_replace($patterns, '', $line);
        fwrite($output, $cleanedLine);
    }

    fclose($output);

    echo "Мета-теги успешно удалены и сохранены в $outputFile\n";
} catch (Exception $e) {
    echo 'Ошибка: ',  $e->getMessage(), "\n";
}
