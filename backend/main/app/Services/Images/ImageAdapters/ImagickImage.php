<?php

namespace App\Services\Images\ImageAdapters;

use Illuminate\Support\Facades\Log;
use Imagick;
use ImagickException;
use ImagickPixel;

/**
 * Class ImagickImage.
 *
 * @package Common\Services\Images\Drivers
 */
class ImagickImage extends AbstractImageAdapter
{
    /**
     * @var string
     */
    public const WEBP_FORMAT = 'webp';

    /**
     * @var Imagick
     */
    protected $imagick;

    /**
     * @var string
     */
    protected $imageFormat;

    /**
     * Источник изображения
     * ImagickImage constructor.
     * @param string $imageSource
     * @throws ImagickException
     */
    public function __construct(string $imageSource)
    {
        $this->imagick = new Imagick();
        if ($this->isBase64DataUrl($imageSource)) {
            [$this->imageFormat, $base64] = $this->parseBase64DataUrl($imageSource);
            $this->imagick->readImageBlob(base64_decode($base64));
        } else {
            $image = file_get_contents($imageSource);
            $this->imagick->readImageBlob($image);
            $this->imageFormat = strtolower($this->imagick->getImageFormat());
        }
    }

    /**
     * Делает ресайз изображения до указанных значений
     * @param int $maxWidth
     * @param int $maxHeight
     * @return static
     */
    public function resize(int $maxWidth, int $maxHeight)
    {
        $this->imagick->thumbnailImage($maxWidth, $maxHeight);
        return $this;
    }

    /**
     * Вырезает из изображения прямоугольный фрагмент, ограниченной области
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return static
     */
    public function crop(int $x, int $y, int $width, int $height)
    {
        $this->imagick->cropImage($width, $height, $x, $y);

        return $this;
    }

    /**
     * Поворачивает изображение на указанное число градусов
     * @param int $angle
     * @param string $background
     * @return static
     */
    public function rotate(int $angle, string $background = '#00000000')
    {
        $this->imagick->rotateImage($background, $angle);

        return $this;
    }

    /**
     * Проверяет, является ли изображение альбомной ориентации
     * @return bool
     */
    public function isLandscape(): bool
    {
        return $this->imagick->getImageWidth() >= $this->imagick->getImageHeight();
    }

    /**
     * Возвращает обработанное изображение с указанном формате
     * @param string|null $format
     * @return mixed
     */
    public function get(?string $format = null)
    {
        switch ($format) {
            case 'data_url':
                return "data:image/{$this->imageFormat};base64," . base64_encode($this->imagick->getImageBlob());

            case 'base64':
                return base64_encode($this->imagick->getImageBlob());

            case null:
                return $this->imagick;

            default:
                $this->imagick->setImageFormat($format);

                return $this->imagick->getImageBlob();
        }
    }

    /**
     * Возвращает драйвер изображения
     * @return mixed
     */
    public function getDriver()
    {
        return $this->imagick;
    }

    /**
     * Сохраняет изображение по указанному пути
     * @param string $path
     * @param null|string $format
     * @param bool|callable $compression
     * @return mixed
     */
    public function save(string $path, ?string $format = null, $compression = true)
    {
        $format = $format ?? $this->imageFormat;
        $this->imagick->setImageFormat($format);
        if ($format === static::WEBP_FORMAT) {
            $this->imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
            $this->imagick->setBackgroundColor(new ImagickPixel('transparent'));
            # Опция "Без потери качества". Увеличение размера будет, если:
            # - если изображение предварительно уже было сжато;
            # - если источник имеет формат с потерями (например, jpg)
            # @see(https://wpgutenberg.top/faq-po-webp-ot-google/)
            if (!$compression) {
                $this->imagick->setOption('webp:lossless', 'true');
            }
            # Если закомментировано, то сохранение происхожит
            $this->imagick->setOption('webp:method', '6');
            $this->imagick->setOption('webp:low-memory', 'true');
        }

        return $this->imagick->writeImage($path);
    }

    public function __clone()
    {
        $this->imagick = clone $this->imagick;
    }

    /**
     * Возвращает размеры изображения
     * @return array
     */
    public function getImageSize(): array
    {
        return [$this->imagick->getImageWidth(), $this->imagick->getImageHeight()];
    }

    /**
     * Включает сжатие изображения
     * @param null|callable $cb
     * @return static
     */
    public function enableCompression($cb = null)
    {
        if (\is_callable($cb)) {
            $cb($this->imagick);
        } elseif ($this->imageFormat !== static::WEBP_FORMAT) {
            $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            $this->imagick->setImageCompressionQuality(95);
            $this->imagick->stripImage();
            $this->imagick->setInterlaceScheme(Imagick::INTERLACE_PLANE);
            # $this->imagick->gaussianBlurImage(0.05, 5);
        }

        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $color
     * @param string|null $format
     * @return Imagick
     * @throws ImagickException
     * @throws \ImagickPixelException
     */
    public function compositeToCanvas(
        int     $width,
        int     $height,
        string  $color = 'white',
        ?string $format = null
    ) {
        $canvas = new Imagick();
        $canvas->newImage($width, $height, new ImagickPixel($color));
        $canvas->setImageFormat($format ?? $this->imageFormat);
        $x = 0;
        $y = 0;

        if ($this->imagick->getImageWidth() > $width || $this->imagick->getImageHeight() > $height) {
            $this->imagick->scaleImage($width, $height, true);
        }

        if ($width > $height) { # горизонтальное
            $x = intval($width / 2 - $this->imagick->getImageWidth() / 2);
        } else { # вертикальное
            $y = intval($height / 2 - $this->imagick->getImageHeight() / 2);
        }

        $canvas->compositeImage($this->imagick, Imagick::COMPOSITE_OVER, $x, $y);

        $this->imagick = $canvas;
    }
}
