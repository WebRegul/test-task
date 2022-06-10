<?php

namespace App\Services\Images\ImageAdapters;

/**
 * Class AbstractImageDriver.
 *
 * @package Common\Services\Images\Drivers
 */
abstract class AbstractImageAdapter
{
    /**
     * Возвращает драйвер изображения
     * @return mixed
     */
    abstract public function getDriver();

    /**
     * Делает ресайз изображения до указанных значений
     * @param int $maxWidth
     * @param int $maxHeight
     * @return static
     */
    abstract public function resize(int $maxWidth, int $maxHeight);

    /**
     * Вырезает из изображения прямоугольный фрагмент, ограниченной области
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return static
     */
    abstract public function crop(int $x, int $y, int $width, int $height);

    /**
     * Поворачивает изображение на указанное число градусов
     * @param int $angle
     * @return static
     */
    abstract public function rotate(int $angle);

    /**
     * Проверяет, является ли изображение альбомной ориентации
     * @return bool
     */
    abstract public function isLandscape(): bool;

    /**
     * Возвращает размеры изображения
     * @return array
     */
    abstract public function getImageSize(): array;

    /**
     * Возвращает обработанное изображение с указанном формате
     * @param string|null $format
     * @return mixed
     */
    abstract public function get(?string $format = null);

    /**
     * Сохраняет изображение по указанному пути
     * @param string $path
     * @param null|string $format
     * @return mixed
     */
    abstract public function save(string $path, ?string $format = null);

    /**
     * Включает сжатие изображения
     * @return static
     */
    abstract public function enableCompression();

    /**
     * вписывает изображение в канвас
     * @param int $width
     * @param int $height
     * @param string $color
     * @param string|null $format
     * @return mixed
     */
    abstract public function compositeToCanvas(int $width, int $height, string $color = 'white', ?string $format = null);

    /**
     * Проверяет, является ли источник изображения - закодированной строкой base64
     * @param string $imageSource
     * @return bool
     */
    protected function isBase64DataUrl(string $imageSource): bool
    {
        # Возьмем первые 25 символов
        return preg_match('/data:.*;base64,/u', substr($imageSource, 0, 25));
    }

    /**
     * Парсит закодированную base64 строку
     * @param string $base64Image
     * @return array
     */
    protected function parseBase64DataUrl(string $base64Image): array
    {
        # Возьмем первые 25 символов
        preg_match('/data:.*;base64,/u', substr($base64Image, 0, 25), $response);
        $hasResponse = $response && \is_array($response) && \count($response);
        $type = $hasResponse ? str_replace(['data:image/', ';base64,'], '', $response[0]) : 'png';

        return [$type, str_replace($hasResponse ? $response[0] : 'data:;base64,', '', $base64Image)];
    }
}
