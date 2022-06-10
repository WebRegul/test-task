<?php

namespace App\Services\Images;

use App\Exceptions\ForbiddenException;
use App\Helpers\Permissions;
use App\Models\Gallery as GalleryModel;
use App\Models\GallerySection as GallerySectionModel;
use App\Models\ImageSections as ImageSectionsModel;
use App\Services\Galleries\Gallery;
use App\Services\User;
use Illuminate\Support\Facades\DB;
use App\Models\Image as ImageModel;
use Illuminate\Support\Arr;
use App\Services\Images\ImageAdapters\AbstractImageAdapter;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use ImagickException;
use Illuminate\Support\Collection;
use RuntimeException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;

class Image
{
    /**
     * Допустимые расширения для файлов изображений
     * @var array
     */
    public const ALLOWED_EXTENSIONS = ['jpeg', 'jpg', 'png'];

    /**
     * Минимальное значение размера изображения
     * @var array
     */
    public const MIN_SIZE = [900, 600];

    protected $userId;

    /**
     * свойство для сбора всех размеров изображений,
     * которые сохраняются методом save
     * @var int
     */
    private $totalImageSize = 0;

    public function __construct()
    {
        $this->imagesSizeLimit = config('utils.images_size_limit');
    }

    /**
     * @param string $userId
     * @param array $ids
     * @return Collection
     */
    public function imagesOrder(string $userId, array $ids): Collection
    {
        $images = DB::transaction(function () use ($userId, $ids) {
            $images = collect();
            foreach ($ids as $key => $imageId) {
                $image = ImageModel::query()
                    ->with('gallery')
                    ->find($imageId);

                if ($image && Arr::has($image, 'gallery.user_id')) {
                    $galleryUserId = Arr::get($image, 'gallery.user_id');
                    if ($userId != $galleryUserId) {
                        throw new ForbiddenException('фото принадлежит другому пользователю');
                    }

                    $image->order = $key;

                    $image->save();

                    $images->put($key, $image);
                }
            }

            return $images;
        });

        return $images;
    }

    /**
     * @param array $images
     * @param string $galleryId
     * @param string|null $sectionId
     * @param string|null $userId
     * @throws \Exception
     */
    public function setImagesSection(
        array   $images,
        string  $galleryId,
        ?string $sectionId = null,
        ?string $userId = null
    ): array {
        $gallery = GalleryModel::query()
            ->find($galleryId);

        if (!Permissions::isOwner($gallery, $userId)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }

        if (empty($sectionId)) {
            $sectionId = (new Gallery($galleryId))->createSection([
                'title' => config('gallery.sections.default_title')
            ])->get('id');
        }
        if (!empty($images)) {
            $oldSection = $this->getImagesSection($images);
        }

        foreach ($images as $imageId) {
            $imageSection = ImageSectionsModel::query()
                ->where('image_id', $imageId)
                ->where('gallery_id', $galleryId)
                //->where('section_id', $sectionId) // изображение в одной галерее может быть только в одной секции
                ->firstOrNew();

            $imageSection->image_id = $imageId;
            $imageSection->gallery_id = $galleryId;
            $imageSection->section_id = $sectionId;

            $imageSection->save();
        }

        $revertId = Str::uuid()->toString();

        if (!empty($images)) {
            Cache::remember(
                'section_uid' . $revertId,
                config('utils.set_section_cache_time'),
                function () use ($images, $oldSection, $sectionId) {
                    return [
                        'images' => $images,
                        'old_section' => $oldSection,
                        'new_section' => $sectionId
                    ];
                }
            );
        }

        if (!empty($oldSection)) {
            GallerySectionModel::query()->find($oldSection)->touch();
        }

        $section = GallerySectionModel::query()->with('images')->find($sectionId);
        $section->touch();

        return [
            'revert_id' => $revertId,
            'section' => $section
        ];
    }

    /**
     * @param array $images
     * @return string
     */
    protected function getImagesSection(array $images): string
    {
        $oldSection = null;
        foreach ($images as $imageId) {
            $imageSection = ImageSectionsModel::where('image_id', $imageId)->first();
            if (!empty($imageSection)) {
                if ($oldSection) {
                    if ($imageSection->section_id != $oldSection) {
                        throw new \Exception('Фотографии для перемещения должны быть из одной секции');
                    }
                }
                $oldSection = $imageSection->section_id;
            }
        }
        return (string)$oldSection;
    }

    /**
     * @param string $revertId
     * @return object
     */
    public function revertSection(string $revertId): object
    {
        $data = Cache::get('section_uid' . $revertId);
        if ($data && Arr::has($data, 'old_section')) {
            $section = GallerySectionModel::find($data['old_section'])->toArray();
            $res = $this->setImagesSection($data['images'], $section['gallery_id'], $data['old_section']);
            return Arr::get($res, 'section');
        } else {
            throw new \Exception('Невозможно отменить перемещение!');
        }
    }

    /**
     * Выполняет ресайз изображений для редактора фотографий
     * @param array $imagesData
     * @param bool $constraits
     * @return array
     * @throws BindingResolutionException
     * @throws ImagickException
     */
    public function smartResizeImageEditor(string $entityType, array $imagesData, bool $constraits = true): array
    {
        # Получаем общие размеры изображения
        $commonSizes = ImageModel::getCommonImageSizes();
        # Получаем размер, до которого нам нужно уменьшать
        $resized = [];
        # Создаем для каждой фото экземпляр адаптера фото

        foreach ($imagesData as $imageData) {
            if (\is_array($imageData) && \count($imageData) > 3) {
                # Проверка по MIME-type
                if (strpos($imageData[3], 'image/') !== 0) {
                    $resized[] = [false, 'Файл `' . $imageData[2] . '` не является изображением и будет пропущен.'];
                    continue;
                }
                try {

                    # В $imageData[0] содержится закодированная base64-строка, представляющая собой изображение
                    /** @var AbstractImageAdapter $image */
                    $image = app()->make(AbstractImageAdapter::class, [
                        'imageSource' => $imageData[0]
                    ]);
                } catch (ImagickException $ie) {
                    if (strpos($ie->getMessage(), 'no decode delegate for this image format') === 0) {
                        $resized[] = [false, 'Файл `' . $imageData[2] . '` имеет неверное расширение и будет пропущен. Допустимые расширения: ' .
                            implode(', ', array_map(function ($item) {
                                return '.' . $item;
                            }, static::ALLOWED_EXTENSIONS))];
                        continue;
                    }

                    throw $ie;
                }

                if ($constraits) {
                    # Проверка на длину изображения
                    if ($image->getDriver()->getImageSize() > $this->imagesSizeLimit) {
                        $error = 'Проверьте, что все файлы имеют размер меньше ' . (floor($this->imagesSizeLimit / 1000000)) . 'МБ';
                        $resized[] = [false, $error];
                        continue;
                    }

                    # Проверка на размер изображения
                    $imageSize = $image->getImageSize();
                    if ($entityType == 'profile' && (!$this->isValidSize($imageSize[0], $imageSize[1]))) {
                        $resized[] = [false, 'Размер файла должен быть не меньше 600 × 600 точек'];
                    } elseif ($entityType != 'profile' && !$this->isValidSize($imageSize[0], $imageSize[1])) {
                        $resized[] = [false, 'Размер файла должен быть не меньше 900 × 600 точек'];
                    }
                }

                # Проверка по истинному формату изображения
                if (!\in_array(strtolower($image->getDriver()->getImageFormat()), static::ALLOWED_EXTENSIONS, true)) {
                    $resized[] = [false, 'Файл `' . $imageData[2] . '` имеет неверное расширение и будет пропущен. Допустимые расширения: ' .
                        implode(', ', array_map(function ($item) {
                            return '.' . $item;
                        }, static::ALLOWED_EXTENSIONS))];
                    continue;
                }
            } else {
                /** @var AbstractImageAdapter $image */
                $image = app()->make(AbstractImageAdapter::class, [
                    'imageSource' => $imageData
                ]);
            }

            $image->enableCompression();
            $resize = [];

            # В зависимости от ориентации фото уменьшаем по измерению
            # Уменьшаем до размера просмотрщика
            $resize[] = $this->smartResizeImage($image, ImageModel::VIEWER_SIZE[0], ImageModel::VIEWER_SIZE[1]);

            # Если среди общих размеров есть размер миньки галереи - уменьшаем до нее также
            if (array_key_exists(ImageModel::GALLERY_MINI_POSTFIX, $commonSizes)) {
                $resize[] = $this->smartFitImage($image, $commonSizes[ImageModel::GALLERY_MINI_POSTFIX]['size'][0], $commonSizes[ImageModel::GALLERY_MINI_POSTFIX]['size'][1]);
            }
            $resized[] = [true, $resize, $imageData[2]];
        }

        if (count($resized) == 1 && $resized[0][0] == false) {
            throw new \Exception($resized[0][1]);
        }
        if (count($imagesData) == 1 && $resized[0][0] == false) {
            throw new \Exception($resized[0][1], 400);
        }

        return $resized;
    }

    /**
     * @param string $imageId
     * @param array $frontSize
     * @param array $frontCoords
     * @param string $position
     * @param string $userId
     *
     * @return Collection
     */
    public function resizeImagesByMidpoint(string $imageId, array $frontSize, array $frontCoords, string $position, string $userId): Collection
    {
        $resized = [];
        $imageModel = ImageModel::query()
            ->with(['gallery', 'imageSections'])
            ->where('id', $imageId)
            ->first();
        $image = $imageModel->getImagePath(ImageModel::ORIGINAL_POSTFIX);
        // $image = file_get_contents($url);
        /** @var AbstractImageAdapter $image */
        $image = app()->make(AbstractImageAdapter::class, [
            'imageSource' => $image
        ]);

        $galleryUser = Arr::get($imageModel, 'gallery.user_id');
        if (!($galleryUser == $userId)) {
            throw new ItemNotFoundException('фото принадлежит другому пользователю');
        }

        if (empty($imageModel)) {
            throw new ItemNotFoundException('неизвестный image_id!');
        }

        $commonSizes = collect($imageModel->getImageSizes());
        $commonSizes->transform(function ($e, $key) {
            $e['key'] = $key;
            return $e;
        });
        $commonSizes = $commonSizes->pluck('size', 'key')->toArray();
        // $images = ImageModel::entities(Arr::get($imageModel, 'gallery.id'), 'gallery', null, null, null)->get();

        $originalSizes = $image->getImageSize();
        $originalCoords = $this->getOriginalCoords($frontCoords, $frontSize, $originalSizes);

//        foreach ($commonSizes as $key => $size) {
//            $imageClone = clone $image;
//            $resize[] = $this->smartMidpointResizeImage(
//                $imageClone,
//                $originalCoords,
//                $originalSizes,
//                $size
//            );
//            // public_paths тут сохраняются
//            $this->save($imageClone, $imageModel, $key);
//        }

        $cropSizes = $imageModel->getCropSizes();
        foreach ($cropSizes as $key => $size) {
            $imageClone = clone $image;
            $resize[] = $this->smartMidpointResizeImage(
                $imageClone,
                $originalCoords,
                $originalSizes,
                $size['size'],
                $size['aspect_ratio']
            );

            $this->save($imageClone, $imageModel, ImageModel::CROP_POSTFIX . '_' . $key);
        }

        $this->updateImageCoords($imageId, $frontCoords);
        $this->updateImagePosition($imageId, $position);
        # Если среди общих размеров есть размер миньки галереи - уменьшаем до нее также
        //  if (array_key_exists(ImageModel::GALLERY_MINI_POSTFIX, $commonSizes)) {
        //      $resize[] = $this->smartFitImage($image, $commonSizes[ImageModel::GALLERY_MINI_POSTFIX]['size'][0], $commonSizes[ImageModel::GALLERY_MINI_POSTFIX]['size'][1]);
        //  }
        $resized[] = [true, $resize, Arr::get($imageModel, 'gallery.filename'), $frontCoords];

        if (count($resized) == 1 && $resized[0][0] == false) {
            throw new \Exception($resized[0][1]);
        }

        return collect($imageModel);
    }

    /**
     * @param string $imageId
     * @param array $coords
     *
     * @return bool
     */
    protected function updateImageCoords(string $imageId, array $coords)
    {
        return ImageModel::where('id', $imageId)->update([
            'midpoint' => json_encode(['x' => $coords[0], 'y' => $coords[1]])
        ]);
    }

    /**
     * @param string $imageId
     * @param string $position
     *
     * @return bool
     */
    protected function updateImagePosition(string $imageId, string $position)
    {
        return ImageModel::where('id', $imageId)->update([
            'position' => $position
        ]);
    }

    /**
     * Вписывает и обрезает изображение в указанные границы
     * @param AbstractImageAdapter $adapter Адаптер изображения
     * @param int $widthBound Граница по ширине
     * @param int $heightBound Граница по высоте
     * @param bool $centring Флаг: нужно ли центрировать результат
     * @param bool $isAvatarMode Флаг: обрабатывается аватара
     * @return string
     */
    protected function smartFitImage(AbstractImageAdapter $adapter, int $widthBound, int $heightBound, bool $centring = false, bool $isAvatarMode = false): string
    {
        # Получаем размеры изображения
        Log::info('fit', ['widthBound' => $widthBound, 'heightBound' => $heightBound]);
        $sizes = $adapter->getImageSize();
        # Считаем отношение измерений контейнера к измерениям изображения
        [$coeffWidth, $coeffHeight] = [$widthBound / $sizes[0], $heightBound / $sizes[1]];
        if ($coeffWidth === $coeffHeight) {
            $resize = $heightBound > $widthBound ? [$widthBound, 0] : [0, $heightBound];
        } else {
            # Получаем отношение ресайза
            $resize = max($coeffWidth, $coeffHeight) === $coeffWidth ? [$widthBound, 0] : [0, $heightBound];
        }
        $adapter->resize(...$resize);
        # Вырезаем из результата область по границам изображения
        [$x, $y] = [0, 0];
        if ($centring) {
            $imageSize = $adapter->getImageSize();
            $x = ($imageSize[0] - $widthBound) / 2;
            $y = $isAvatarMode ? 0 : ($imageSize[1] - $heightBound) / 2;
        }
        Log::info('smartFit', [[$coeffWidth, $coeffHeight], $x, $y, $widthBound, $heightBound]);
        return $adapter->crop($x, $y, $widthBound, $heightBound)->get('data_url');
    }

    /**
     * Выполняет ресайз изображения до нужного размера по наибольшему измерению
     * @param AbstractImageAdapter $adapter
     * @param int $widthBound
     * @param int $heightBound
     * @return string
     */
    protected function smartResizeImage(AbstractImageAdapter $adapter, int $widthBound, int $heightBound): string
    {
        # Получаем размеры изображения
        $sizes = $adapter->getImageSize();
        # Считаем отношение измерений контейнера к измерениям изображения
        [$coeffWidth, $coeffHeight] = [$widthBound / $sizes[0], $heightBound / $sizes[1]];

        if ($coeffWidth === $coeffHeight) {
            $resize = $heightBound > $widthBound ? [$widthBound, 0] : [0, $heightBound];
        } else {
            # Получаем отношение ресайза
            $resize = min($coeffWidth, $coeffHeight) === $coeffWidth ? [$widthBound, 0] : [0, $heightBound];
        }
        //$resize = [$widthBound, $heightBound];
        Log::info('smartResizeImage', [$resize]);
        $adapter = $adapter->resize(...$resize);
        //  # Вырезаем из результата область по границам изображения
        //   [$x, $y] = [0, 0];
        // $imageSize = $newadapter->getImageSize();
        //  $x         = ($imageSize[0] - $widthBound) / 2;
        //$y         = ($imageSize[1] - $heightBound) / 2;
        //  return $adapter->crop(0,0, $widthBound, $heightBound)->get('data_url');
        return $adapter->get('data_url');
    }

    /**
     * пропорциональный перенос координат с фронта в координаты оригинала
     * @param array $frontCoords
     * @param array $frontSize
     * @param array $originalSize
     * @return array
     */
    protected function getOriginalCoords(array $frontCoords, array $frontSize, array $originalSize): array
    {
        $originalSize[0] *= $frontCoords[0] / $frontSize[0];
        $originalSize[1] *= $frontCoords[1] / $frontSize[1];

        return collect($originalSize)->transform(function ($value, $key) {
            return intval(round($value));
        })->toArray();
    }

    /**
     * @param AbstractImageAdapter $adapter
     * @param array $coords
     * @param array $originalSize
     * @param array $size
     * @param bool $aspectRatio
     * @return string
     */
    protected function smartMidpointResizeImage(
        AbstractImageAdapter $adapter,
        array                $coords,
        array                $originalSize,
        array                $size,
        bool                 $aspectRatio = false
    ): string {
        $cropSize = $size;
        $inclusiveness = ($originalSize[0] * $originalSize[1]) / ($cropSize[0] * $cropSize[1]);

        $step = 5;
        $mult = 0.7;
        $inclusivenessMult = [];
        for ($i = $inclusiveness; $i >= $step; $i--) {
            if ($i % $step == 0) {
                $inclusivenessMult[$i] = round(sqrt($i) * $mult);
            }
        }

        foreach ($inclusivenessMult as $key => $value) {
            if ($inclusiveness > $key) {
                $cropSize = collect($cropSize)->transform(function ($item, $key) use ($value) {
                    return round($item *= $value);
                })->toArray();
                break;
            }
        }

        if ($cropSize[0] > $originalSize[0] || $cropSize[1] > $originalSize[1]) {
            $adapter->compositeToCanvas($size[0], $size[1]);
        } else {
            $x1 = $coords[0] - round($cropSize[0] / 2);
            $y1 = $coords[1] - round($cropSize[1] / 2);

            if ($x1 + $size[0] > $originalSize[0]) {
                $x1 = $originalSize[0] - $cropSize[0];
            }
            if ($y1 + $size[1] > $originalSize[1]) {
                $y1 = $originalSize[1] - $cropSize[1];
            }

            if ($x1 < 0) {
                $x1 = 0;
            }
            if ($y1 < 0) {
                $y1 = 0;
            }

            $adapter->crop($x1, $y1, $cropSize[0], $cropSize[1]);
        }

        $adapter->resize(...$size);

        return $adapter->get('data_url');
    }

    /**
     * Получает изображения, связанные с указанной сущностью
     * @param string $entityId Идентификатор сущности
     * @param string $entityType Название сущности
     * @param int|null $creatorId Идентификатор владельца фотографий
     * @param array $fieldsTransform Карта транфсормации свойств изображения
     * @param array $unsetFields Карта полей для удаления
     * @return array
     */
    public function getEntityImages(
        string  $entityId,
        string  $entityType,
        ?string $sectionId = null,
        ?string $creatorId = null,
        array   $fieldsTransform = [],
        array   $unsetFields = []
    ): array {
        if ($entityType == 'profile') {
            $images = ImageModel::entities($entityId, $entityType, null, null, $creatorId)->orderBy('created_at', 'desc')->limit(1)->get()->toArray();
        } else {
            if ($sectionId) {
                $section = GallerySectionModel::query()
                    ->find($sectionId)
                    ->toArray();

                $orderBy = ($section['order_mode'] == 'manual')
                    ? ['order', 'asc']
                    : ['filename', $section['order_mode']];

                $images = ImageModel::query()
                    ->whereHas('imageSections', function ($q) use ($sectionId) {
                        $q->where('section_id', $sectionId);
                    })
                    ->entities($entityId, $entityType, null, null, $creatorId)
                    ->orderBy(...$orderBy)
                    ->get()
                    ->toArray();
            } else {
                $images = ImageModel::has('imageSections')->entities($entityId, $entityType, null, null, $creatorId)->orderBy('created_at', 'desc')->get()->toArray();
            }
        }

        if ($fieldsTransform && \is_array($fieldsTransform) && \count($fieldsTransform)) {
            $this->transformField($images, $fieldsTransform, $unsetFields);
        }

        return $images;
    }

    /**
     * Сохраняет фотографии и связывает их с сущностью
     * @param string $entityId Идентификатор сущности
     * @param string $entityType Тип сущности
     * @param array $imagesData Массив объектов с данными изображений
     * @param int|null $creatorId Отфильтровать по создателю изображения
     * @param string $srcPropertyName Название свойства, по которому располагаются источник данных изображения
     * @param string $srcMiniPropertyName Название свойства, по которому располагаются источник данных изображения просмотрщика
     * @param string $cropBoundsPropertyName Название свойства, по которому располагаются границы кропа изображения
     * @param string $rotateAnglePropertyName Название свойства, по которому располагается угол поворота изображения
     * @param string $aspectRatioPropertyName Название свойства, по которому располагаются пропорции изображения
     * @param string $positionPropertyName Название свойства, по которому располагается позиция изображения в галерее
     * @param string $isMainPropertyName Название свойства, по которому располагается индикатор главного фото
     * @param string $imageDescriptorPropertyName Название свойства, по которому располагается дескриптор изображения (модель записи БД)
     * @return array|bool
     * @throws BindingResolutionException
     */
    public function saveEntityImages(
        string  $entityId,
        string  $entityType,
        array   $imagesData,
        ?string $sectionId = null,
        ?string $userId = null,
        ?int    $creatorId = null,
        string  $srcPropertyName = 'src',
        string  $srcMiniPropertyName = 'src_mini',
        string  $cropBoundsPropertyName = 'crop',
        string  $rotateAnglePropertyName = 'rotate',
        string  $aspectRatioPropertyName = 'aspect_ratio',
        string  $positionPropertyName = 'position',
        string  $isMainPropertyName = 'is_main',
        string  $imageDescriptorPropertyName = '_imageDescriptor'
    ) {
        DB::beginTransaction();
        try {
            # Режим множественной загрузки
            $isMultipleMode = \count($imagesData) > 1;
            [$updates, $inserts, $actualExists] = [[], [], []];
            # 0. Получаем существующие фотографии
            $existingPhotos = ImageModel::entities($entityId, $entityType, null, null, $creatorId)
                ->get()
                ->keyBy('id')
                ->all();

            $errors = [];
            foreach ($imagesData as $imageData) {
                if (\is_array($imageData) && Arr::get($imageData, 'src')) {
                    $image = app()->make(AbstractImageAdapter::class, [
                        'imageSource' => $imageData['src']
                    ]);
                    if ($image->getDriver()->getImageSize() > $this->imagesSizeLimit) {
                        // throw new \Exception('Проверьте, что все файлы имеют размер меньше 15МБ');
                        $errors[] = 'Проверьте, что все файлы имеют размер меньше ' . (floor($this->imagesSizeLimit / 1000000)) . 'МБ';
                    }

                    if (!in_array($image->getDriver()->getImageFormat(), ['JPEG', 'JPG', 'PNG', 'GIF'])) {
                        $errors[] = 'Формат файла должен быть png jpg или gif';
                    }
                    $imageSize = $image->getImageSize();

                    if ($entityType == 'profile' && (!$this->isValidSize($imageSize[0], $imageSize[1]))) {
                        $errors[] = 'Размер файла должен быть не меньше 600 × 600 точек';
                    } elseif ($entityType != 'profile' && !$this->isValidSize($imageSize[0], $imageSize[1])) {
                        $resized[] = [false, 'Размер файла должен быть не меньше 900 × 600 точек'];
                    }
                }

                $fileName = '';
                if ($entityType == ImageModel::GALLERY_ENTITY) {
                    if (\is_array($imageData) && $fileName = Arr::get($imageData, 'filename')) {
                        $fileName = preg_replace('/[\x00-\x1F\x7f-\xFF]/', '', $fileName);
                    } else {
                        $errors[] = 'Необходимо передавать название файла';
                    }
                }

                # 1. Определяем действие, которое будет выполняться над фотографиями
                if (!array_key_exists($imageDescriptorPropertyName, $imageData)) {
                    # Добавление
                    $inserts[] = $imageData;
                }
                if (
                    array_key_exists($imageDescriptorPropertyName, $imageData) &&
                    array_key_exists('id', $imageData[$imageDescriptorPropertyName]) && $imageData[$imageDescriptorPropertyName]['id'] &&
                    array_key_exists($imageData[$imageDescriptorPropertyName]['id'], $existingPhotos)
                ) {
                    # Обновление (возможно)
                    # Проверяем по позиции, границам кропа, аспекту и углу поворота
                    /** @var ImageModel $existedPhoto */
                    $existedPhoto = $existingPhotos[$imageData[$imageDescriptorPropertyName]['id']];

                    if (
                        $existedPhoto->priority !== Arr::get($imageData, $positionPropertyName) ||
                        $existedPhoto->is_main !== Arr::get($imageData, $isMainPropertyName) ||
                        $existedPhoto->rotate_angle !== Arr::get($imageData, $rotateAnglePropertyName) ||
                        $existedPhoto->crop_bounds !== Arr::get($imageData, $cropBoundsPropertyName) ||
                        $existedPhoto->aspect_ratio !== Arr::get($imageData, $aspectRatioPropertyName)
                    ) {
                        $updates[$existedPhoto->id] = $imageData;
                    }

                    $actualExists[] = $existedPhoto->id;
                }
            }

            # 2. Обрабатывает обновления фото
            if (\count($updates)) {
                $this->handleUpdates(
                    $entityId,
                    $entityType,
                    $updates,
                    $existingPhotos,
                    $isMultipleMode,
                    $srcPropertyName,
                    $srcMiniPropertyName,
                    $cropBoundsPropertyName,
                    $rotateAnglePropertyName,
                    $aspectRatioPropertyName,
                    $positionPropertyName,
                    $isMainPropertyName
                );
            }

            $insertsIds = [];
            # 3. Обрабатываем добавление фото
            if (\count($inserts)) {
                $insertsIds = $this->handleInserts(
                    $fileName,
                    $entityId,
                    $entityType,
                    $inserts,
                    $sectionId,
                    $userId,
                    $creatorId,
                    $isMultipleMode,
                    $srcPropertyName,
                    $srcMiniPropertyName,
                    $cropBoundsPropertyName,
                    $rotateAnglePropertyName,
                    $aspectRatioPropertyName,
                    $positionPropertyName,
                    $isMainPropertyName
                );
            }

            # 4. Обрабатываем удаление фото
            $deletes = Arr::except($existingPhotos, $actualExists);
            if (\count($deletes) && $entityType != ImageModel::GALLERY_ENTITY) {
                $this->handleDeletes($entityId, $entityType, $deletes, $isMultipleMode, $userId);
            }

            $this->checkMainPhoto($entityId, $entityType, $creatorId);
            $photos = ImageModel::entities($entityId, $entityType, null, null, $creatorId)
                ->whereIn('id', $insertsIds)
                ->get(['id', 'sizes', 'type', 'item_id', 'priority', 'is_main', 'updated_at'])
                ->toArray();
            $this->transformField(
                $photos,
                ['crop_public_paths' => 'images', 'priority' => 'position'],
                ['item_id', 'type', 'aspect_ratio', 'crop_bounds', 'crop_paths', 'paths', 'rotate_angle', 'crop_public_paths', 'src', 'src_gallery_mini', 'src_xl', 'priority']
            );

            if (count($insertsIds) != count($inserts)) {
                $errors[] = 'Недостаточно места, часть фотографий не была загружена';
            }

            if (empty($errors)) {
                $errors = [
                    'status' => false,
                    'message' => '',
                    'details' => [],
                ];
            } elseif (count($errors) == 1) {
                $errors = [
                    'status' => true,
                    'message' => $errors[0],
                    'details' => [],
                ];
            } else {
                $errors = [
                    'status' => true,
                    'message' => 'Несколько фотографий не было загружено, так как не прошли валидацию',
                    'details' => $errors,
                ];
            }

            $result = collect();
            $result->put('data', $photos);
            $result->put('errors', $errors);

            DB::commit();

            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return false;
    }

    /**
     * @param array $idsToDelete
     * @param string $userId
     * @return array
     */
    public function dropImages(array $idsToDelete, string $userId): array
    {
        Log::debug('drop photo start', [$idsToDelete]);
        [$itemId, $isMain, $isGallery] = DB::transaction(function () use ($idsToDelete, $userId) {
            $result = [null, false, false];
            if (!$idsToDelete || empty($idsToDelete)) {
                return $result;
            }
            foreach ($idsToDelete as $id) {
                $model = ImageModel::query()
                    ->with(['gallery', 'imageSections'])
                    ->find($id);
                if ($model->type === 'profile') {
                    $result[0] = Arr::get($model, 'id');
                    $result[1] = 0;
                    $result[2] = false;
                } else {
                    if (Arr::get($model, 'gallery.user_id') != $userId) {
                        return ['error' => 'фото принадлежит другому пользователю', 'id' => $id];
                    }

                    if (empty($result[1]) && !empty(Arr::get($model, 'is_main'))) {
                        $result[0] = Arr::get($model, 'gallery.id');
                        $result[1] = 1;
                    }

                    $model->imageSections()->delete();
                    $result[2] = true;
                }

                $model->delete();
            }
            return $result;
        });

        if (!empty($isMain)) {
            $this->setMainGalleryPhoto($itemId);
        }

        if ($isGallery) {
            $revertId = Str::uuid()->toString();
            Cache::remember(
                'drop_images_uid' . $revertId,
                config('utils.drop_images_cache_time'),
                function () use ($idsToDelete) {
                    return [
                        'images' => $idsToDelete,
                    ];
                }
            );
        } else {
            $revertId = null;
        }

        Log::debug('drop photo end', [$idsToDelete]);
        return [
            'message' => 'фото удалены',
            'ids' => $idsToDelete,
            'revert_id' => $revertId
        ];
    }

    /**
     * @param string $galleryId
     * @return ImageModel
     */
    protected function setMainGalleryPhoto(string $galleryId): ImageModel
    {
        $image = ImageModel::query()
            ->where('type', ImageModel::GALLERY_ENTITY)
            ->where('item_id', $galleryId)
            ->where('is_main', 1)
            ->firstOrNew();
        if (empty($image->id)) {
            $image = ImageModel::where('type', ImageModel::GALLERY_ENTITY)
                ->where('item_id', $galleryId)
                ->orderBy('created_at', 'desc')
                ->firstOrNew();
            if (!empty($image->id)) {
                $image->is_main = 1;
                $image->save();
                $image->refresh();
                $gallery = new Gallery($galleryId);
                $gallery->setCover($image->id);
            }
        }

        return $image;
    }

    /**
     * @param string $revertId
     * @return array
     * @throws Exception
     */
    public function revertImages(string $revertId): array
    {
        $data = Cache::get('drop_images_uid' . $revertId);
        if ($data && Arr::has($data, 'images')) {
            Log::info('restore', [$data]);
            foreach (Arr::get($data, 'images') as $id) {
                $model = ImageModel::withTrashed()->with(['gallery', 'imageSections'])->find($id);
                if ($model && Arr::has($model, 'gallery.user_id')) {
                    $model->imageSections()->restore();
                    $model->restore();
                }
            }
            return ['success' => true, 'message' => 'фото успешно восстановлены'];
        } else {
            throw new \Exception('Невозможно отменить удаление!');
        }
    }

    /**
     * @param string $entityId
     * @param string $entityType
     * @param array $deletes
     * @param bool $isMultipleMode
     * @param string|null $userId
     * @return array
     */
    protected function handleDeletes(string $entityId, string $entityType, array $deletes, bool $isMultipleMode = false, string $userId = null): array
    {
        [$idsToDelete, $isMainPhotoDeleted] = [[], false];
        /** @var ImageModel $delete */
        foreach ($deletes as $delete) {
            $idsToDelete[] = $delete->id;
        }

        return $this->dropImages($idsToDelete, $userId);
    }

    /**
     * Проверяет и устанавливает (если нужно) главное фото
     * @param string $entityId
     * @param string $entityType
     * @param string|null $creatorId
     * @return bool
     */
    protected function checkMainPhoto(string $entityId, string $entityType, ?string $creatorId = null): bool
    {
        $builder = ImageModel::entities($entityId, $entityType, null, null, $creatorId);
        if (!(clone $builder)->where('is_main', true)->count()) {
            /** @var ImageModel $maxPriorityPhoto */
            $maxPriorityPhoto = $builder->orderBy('priority', 'desc')->first();
            if ($maxPriorityPhoto) {
                $maxPriorityPhoto->update([
                    'is_main' => true
                ]);
            }
        }

        return true;
    }

    /**
     * @param string $filename
     * @param string $entityId
     * @param string $entityType
     * @param array $inserts
     * @param string|null $sectionId
     * @param string|null $userId
     * @param int|null $creatorId
     * @param bool $isMultipleMode
     * @param string $srcPropertyName
     * @param string $srcMiniPropertyName
     * @param string $cropBoundsPropertyName
     * @param string $rotateAnglePropertyName
     * @param string $aspectRatioPropertyName
     * @param string $positionPropertyName
     * @param string $isMainPropertyName
     * @return array
     * @throws BindingResolutionException
     */
    protected function handleInserts(
        string  $filename,
        string  $entityId,
        string  $entityType,
        array   $inserts,
        ?string $sectionId = null,
        ?string $userId = null,
        ?int    $creatorId = null,
        bool    $isMultipleMode = false,
        string  $srcPropertyName = 'src',
        string  $srcMiniPropertyName = 'src_mini',
        string  $cropBoundsPropertyName = 'crop',
        string  $rotateAnglePropertyName = 'rotate',
        string  $aspectRatioPropertyName = 'aspect_ratio',
        string  $positionPropertyName = 'position',
        string  $isMainPropertyName = 'is_main'
    ): array {
        if ($entityType == ImageModel::GALLERY_ENTITY && !$sectionId) {
            throw new \Exception('не передан параметр section_id', 400);
        }

        $insertStatements = [];
        foreach ($inserts as $index => $insert) {
            $transformations = [];
            if (array_key_exists($rotateAnglePropertyName, $insert)) {
                $transformations['rotate_angle'] = $insert[$rotateAnglePropertyName];
            }

            if (array_key_exists($cropBoundsPropertyName, $insert)) {
                $transformations['crop_bounds'] = $insert[$cropBoundsPropertyName];
            }

            if (array_key_exists($aspectRatioPropertyName, $insert)) {
                $transformations['aspect_ratio'] = $insert[$aspectRatioPropertyName];
            }

            //            if (array_key_exists($isMainPropertyName, $insert) && $insert[$isMainPropertyName]) {
            //                $hasMainPhoto = true;
            //            }
            $insertStatements[] = [
                'item_id' => $entityId,
                'is_main' => array_key_exists($isMainPropertyName, $insert) ? $insert[$isMainPropertyName] : 0,
                'type' => $entityType,
                'priority' => array_key_exists($positionPropertyName, $insert) ? $insert[$positionPropertyName] : $index,
                'transformations' => \count($transformations) ? json_encode($transformations) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => (int)$creatorId,
                'filename' => Arr::get($insert, 'filename', ''),
            ];
        }

        // получаем айди изображений
        foreach ($insertStatements as &$data) {
            if ($image = ImageModel::create($data)) {
                $data['id'] = $image->id;
            }
        }

        ImageModel::unguard();

        // получаем экземпляр сервиса юзера и
        // данные о размере его стораджа
        $user = new User($userId);
        $userStorage = $user->getMemberData('storage');
        $availableSpace = Arr::get($userStorage, 'available_space'); # in M
        $occupiedSpace = 0; # in M

        $insertStatementsIds = [];
        foreach ($insertStatements as $index => $insertStatement) {
            // storage is full
            if ($availableSpace <= $occupiedSpace) {
                continue;
            }

            // сброс счетчика объема всех хранимых вариантов
            // пользовательского изображения (автоматически
            // суммируется внутри метода save)
            $this->resetTotalImageSize();

            $insertStatementsIds[] = $insertStatement['id'];
            $insertStatement['transformations'] = json_decode($insertStatement['transformations'], true);

            // Создаем модель для работы с фото
            $insertStatementAsModel = new ImageModel($insertStatement);

            $srcImage = isset($inserts[$index][$srcPropertyName]) ? $inserts[$index][$srcPropertyName] : $inserts[$index][$srcMiniPropertyName];
            if ($srcImage) {
                # Создаем экземпляр адаптера
                /** @var AbstractImageAdapter $image */
                $image = app()->make(AbstractImageAdapter::class, [
                    'imageSource' => $srcImage
                ]);
                // $insertStatementAsModel['sizes'] = ['original' => $image->getDriver()->getImageSize()];

                # Сохраняем оригинал фото
                $this->save($image, $insertStatementAsModel, ImageModel::ORIGINAL_POSTFIX);

                # Сохраняем ресайзы изображения
                $image->enableCompression();
                $this->saveImages($image, $insertStatementAsModel);

                # Сохраняем кроп и ресайзы кропа, если кроп есть
                $this->saveCrops(
                    $image,
                    $insertStatementAsModel,
                    array_key_exists($srcMiniPropertyName, $inserts[$index]) ? $inserts[$index][$srcMiniPropertyName] : null,
                    $insertStatementAsModel->rotate_angle
                );
            }

            $occupiedSpace += $this->getTotalImagesSize() / 1024 / 1024; // to M
        }

        ImageModel::reguard();

        if ($entityType == ImageModel::GALLERY_ENTITY) {
            $this->setImagesSection($insertStatementsIds, $entityId, $sectionId, $userId);

            // переставляем главную фотку, если ее нет
            $this->setMainGalleryPhoto($entityId);
        }

        // todo: перепроставить priority

        return $insertStatementsIds;
    }

    /**
     * @param string string $fileName
     * @param string $entityId
     * @param string $entityType
     * @param array $updates
     * @param array $existingPhotos
     * @param bool $isMultipleMode
     * @param string $srcPropertyName
     * @param string $srcMiniPropertyName
     * @param string $cropBoundsPropertyName
     * @param string $rotateAnglePropertyName
     * @param string $aspectRatioPropertyName
     * @param string $positionPropertyName
     * @param string $isMainPropertyName
     * @return bool
     * @throws BindingResolutionException
     */
    protected function handleUpdates(
        string $entityId,
        string $entityType,
        array  $updates,
        array  $existingPhotos,
        bool   $isMultipleMode = false,
        string $srcPropertyName = 'src',
        string $srcMiniPropertyName = 'src_mini',
        string $cropBoundsPropertyName = 'crop',
        string $rotateAnglePropertyName = 'rotate',
        string $aspectRatioPropertyName = 'aspect_ratio',
        string $positionPropertyName = 'position',
        string $isMainPropertyName = 'is_main'
    ): bool {
        /** @var ImageModel $existedPhoto */
        foreach ($existingPhotos as $key => $existedPhoto) {
            if (!array_key_exists($key, $updates)) {
                continue;
            }

            $propertiesToUpdate = [];
            /** @var AbstractImageAdapter $image */
            $image = app()->make(AbstractImageAdapter::class, [
                'imageSource' => $srcMiniPropertyName && array_key_exists($srcMiniPropertyName, $updates[$key])
                    ? $updates[$key][$srcMiniPropertyName] : $updates[$key][$srcPropertyName]
            ]);

            if (!($srcMiniPropertyName && array_key_exists($srcMiniPropertyName, $updates[$key]))) {
                $image->enableCompression();
            }

            if ($existedPhoto->priority !== ($propertyValue = Arr::get($updates[$key], $positionPropertyName))) {
                $propertiesToUpdate['priority'] = $propertyValue;
            }

            if ($existedPhoto->rotate_angle !== ($propertyValue = Arr::get($updates[$key], $rotateAnglePropertyName))) {
                $propertiesToUpdate['rotate_angle'] = $propertyValue;
            }

            if ($existedPhoto->crop_bounds !== ($propertyValue = Arr::get($updates[$key], $cropBoundsPropertyName))) {
                $propertiesToUpdate['crop_bounds'] = $propertyValue;
            }

            if ($existedPhoto->aspect_ratio !== ($propertyValue = Arr::get($updates[$key], $aspectRatioPropertyName))) {
                $propertiesToUpdate['aspect_ratio'] = $propertyValue;
            }

            if (array_key_exists($isMainPropertyName, $updates[$key])) {
                $propertiesToUpdate['is_main'] = $updates[$key][$isMainPropertyName];
                //                if ($propertiesToUpdate['is_main']) {
                //                    $hasMainPhoto = true;
                //                }
            }

            $existedPhoto->update($propertiesToUpdate);
            // if (array_key_exists('rotate_angle', $propertiesToUpdate) || array_key_exists('crop_bounds', $propertiesToUpdate)) {
            //     $this->saveCrops($image, $existedPhoto, $existedPhoto->src_xl, $existedPhoto->rotate_angle);
            // }
        }

        return true;
    }


    /**
     * @param ImageModel $imageModel
     * @return bool
     */
    public function deleteImagesFiles(ImageModel $imageModel): bool
    {
        $imgsSrcs = array_merge(array_values($imageModel->paths), array_values($imageModel->crop_paths));
        $dirs = [];
        foreach ($imgsSrcs as $src) {
            if ($src && file_exists($src)) {
                try {
                    unlink($src);

                    if (!in_array(dirname($src), $dirs)) {
                        $dirs[] = dirname($src);
                    }
                } catch (Exception $e) {
                    Log::info('deleteImagesFiles error', ['error' => $e]);
                    throw $e;
                }
            }
        }

        try {
            foreach ($dirs as $dir) {
                if (count(scandir($dir)) == 2) { // if empty dir - rmdir
                    rmdir($dir);
                }
            }
        } catch (Exception $e) {
            Log::info('deleteImagesFiles error', ['error' => $e]);
            throw $e;
        }

        return true;
    }

    /**
     * Сохраняет изображение
     * @param AbstractImageAdapter $image
     * @param ImageModel $imageModel
     * @param null|string $postfix
     * @param string $extension
     * @return bool
     */
    protected function save(AbstractImageAdapter $image, ImageModel $imageModel, ?string $postfix = null, string $extension = ImageModel::JPEG_EXTENSION): bool
    {
        $imagePath = $imageModel->getImagePath($postfix, true);
        if (!file_exists($dirname = \dirname($imagePath)) && !mkdir($dirname, 0777, true) && !is_dir($dirname)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dirname));
        }

        $image->save($imagePath, $extension);
        $this->saveImageSizes($image, $imageModel, $postfix);

        chmod($dirname, config('filesystems.disks.users.permissions.dir.public'));
        chmod($imagePath, config('filesystems.disks.users.permissions.file.public'));

        $this->setTotalImagesSize($image->getDriver()->getImageSize());

        return true;
    }

    /**
     * @param int $size
     * @return void
     */
    private function setTotalImagesSize(int $size): void
    {
        $this->totalImageSize += $size;
    }

    /**
     * @return int
     */
    private function getTotalImagesSize(): int
    {
        return $this->totalImageSize;
    }

    /**
     * @return void
     */
    private function resetTotalImageSize(): void
    {
        $this->totalImageSize = 0;
    }

    /**
     * Сохраняет размер изображения
     * @param AbstractImageAdapter $image
     * @param ImageModel $imageModel
     * @param string $postfix
     * @return bool
     */
    protected function saveImageSizes(AbstractImageAdapter $image, ImageModel &$imageModel, string $postfix): bool
    {
        if ($postfix == 'original') {
            $imageModel['sizes'] = ['original' => $image->getDriver()->getImageSize()];
        } elseif ($postfix == 'xl') {
            if (Arr::has($imageModel, 'sizes')) {
                $sizes = $imageModel['sizes'];
                $sizes['web'] = $image->getDriver()->getImageSize();
                $imageModel['sizes'] = $sizes;
            }
        }

        return true;
    }

    /**
     * Сохраняет связанные с сущностью изображения на винчестер
     * @param AbstractImageAdapter $image Адаптер для изображений
     * @param ImageModel $imageModel Модель сохраняемого изображения
     * @param string $extension
     * @return bool
     */
    protected function saveImages(AbstractImageAdapter $image, ImageModel $imageModel, string $extension = ImageModel::JPEG_EXTENSION): bool
    {
        # Теперь необходимо сформировать 4 коллекции изображений: аненными пропорциями, альбомной ориентации, сортировка по ширине desc
        # 2 - с сохраненными пропорциями, книжной ориентации, сортировка
        # 3 - с не сохраненными пропорциями, альбомной ориентации, сортировка по ширине desc
        # 4 - с не сохраненными пропорциями, книжной ориентации, сортировка по высоте desc

        $totalImagesSize = 0;

        # Получаем все размеры изображения, к которым нужно приводить
        $sizesSorted = $this->sortSizes($imageModel->getImageSizes());
        # Сохраняем изображения в нужном порядке
        foreach ($sizesSorted as $groupName => $imageOrientationGroup) {
            # Создаем клон изображения
            $imageClone = clone $image;
            # Пробегаемся по каждому разрешению

            foreach ($imageOrientationGroup as $key => $size) {
                if (strpos($groupName, 'aspect') !== false) {
                    $this->smartResizeImage($imageClone, $size[0], $size[1]);
                    $this->save($imageClone, $imageModel, $key, $extension);
                } else {
                    $this->smartFitImage($imageClone, $size[0], $size[1], true);
                    $this->save($imageClone, $imageModel, $key, $extension);
                    $imageClone = clone $image;
                }
            }
        }

        return true;
    }

    /**
     * Сохраняет связанные с сущностью кропы изображения на винчестер
     * @param AbstractImageAdapter $image Адаптер для изображений
     * @param ImageModel $imageModel Модель сохраняемого изображения
     * @param null|string $srcMini Источник изображения для просмотрщика
     * @param int|null $rotateAngle Угол поворота
     * @param string $extension
     * @return bool
     * @throws BindingResolutionException
     */
    protected function saveCrops(
        AbstractImageAdapter $image,
        ImageModel           $imageModel,
        ?string              $srcMini = null,
        ?int                 $rotateAngle = null,
        string               $extension = ImageModel::JPEG_EXTENSION
    ): bool {
        if ($srcMini) {
            /** @var AbstractImageAdapter $image */
            $image = app()->make(AbstractImageAdapter::class, [
                'imageSource' => $srcMini
            ]);
        } else {
            $image = clone $image;
            $image->enableCompression();
            $this->smartResizeImage($image, ImageModel::VIEWER_SIZE[0], ImageModel::VIEWER_SIZE[1]);
        }

        if ($rotateAngle !== null) {
            $image->rotate($rotateAngle);
        }

        # Если кроп = null, то мы делаем дефолтный кроп
        if (!$imageModel->crop_bounds) {
            $aspectRatio = empty($imageModel->aspect_ratio) ? [null, null] : $imageModel->aspect_ratio;
            $this->getDefaultCropBounds($image, $imageModel, $aspectRatio[0], $aspectRatio[1]);
            if (!$imageModel->exists) {
                $imageModel->exists = true;
            }

            $imageModel->save();
        }

        $crops = $imageModel->getCropSizes();
        if (\count($crops) === 0) {
            # Если нет размеров кропов - ничего не делаем.
            return true;
        }

        Log::info('saveCrops', [$imageModel]);
        Log::info('saveCrops', [$imageModel]);

        $image->crop(
            $imageModel->crop_bounds[0][0],
            $imageModel->crop_bounds[0][1],
            $imageModel->crop_bounds[1][0] - $imageModel->crop_bounds[0][0],
            $imageModel->crop_bounds[1][1] - $imageModel->crop_bounds[0][1]
        );
        $this->save($image, $imageModel, ImageModel::CROP_POSTFIX, $extension);
        # Теперь необходимо сформировать 4 коллекции изображений:
        # 1 - с сохраненными пропорциями, альбомной ориентации, сортировка по ширине desc
        # 2 - с сохраненными пропорциями, книжной ориентации, сортировка по высоте desc
        # 3 - с не сохраненными пропорциями, альбомной ориентации, сортировка по ширине desc
        # 4 - с не сохраненными пропорциями, книжной ориентации, сортировка по высоте desc

        # Получаем все размеры изображения, к которым нужно приводить
        $sizesSorted = $this->sortSizes($crops);
        # Сохраняем изображения в нужном порядке
        foreach ($sizesSorted as $groupName => $imageOrientationGroup) {
            # Создаем клон изображения
            $imageClone = clone $image;
            # Пробегаемся по каждому разрешению
            foreach ($imageOrientationGroup as $key => $size) {
                if (strpos($groupName, 'aspect') !== false) {
                    Log::info('saveCrops cicle', [$groupName, $key, 'resize']);
                    $this->smartResizeImage($imageClone, $size[0], $size[1]);
                    $this->save($imageClone, $imageModel, ImageModel::CROP_POSTFIX . '_' . $key, $extension);
                } else {
                    Log::info('saveCrops cicle', [$groupName, $key, 'fit']);
                    $this->smartFitImage($imageClone, $size[0], $size[1], true, $this->isFitAvatarMode($imageModel));
                    $this->save($imageClone, $imageModel, ImageModel::CROP_POSTFIX . '_' . $key, $extension);
                }
            }
        }

        return true;
    }


    /**
     * Трансформирует поля согласно карте трансформаций
     * @param array $items
     * @param array $fieldsTransformer
     * @param array $unsetFields
     * @return array
     */
    protected function transformField(array &$items, array $fieldsTransformer, array $unsetFields = []): array
    {
        foreach ($items as $index => $item) {
            foreach ($fieldsTransformer as $key => $field) {
                if ($this->isDotNotation($key) && Arr::has($item, $key)) {
                    Arr::set($items[$index], $field, Arr::get($item, $key));
                } elseif (array_key_exists($key, $item)) {
                    $items[$index][$field] = $item[$key];
                }
            }

            foreach ($unsetFields as $field) {
                if ($this->isDotNotation($field) && Arr::has($item, $field)) {
                    Arr::forget($items[$index], $field);
                } elseif (array_key_exists($field, $item)) {
                    unset($items[$index][$field]);
                }
            }
        }

        return $items;
    }

    /***
     * Определяет, нужно ли вписывать изображение с потерями в режиме аватара
     * @param ImageModel $photo
     * @return bool
     */
    protected function isFitAvatarMode(ImageModel $photo): bool
    {
        return $photo->type === ImageModel::PROFILE_ENTITY;
    }

    /**
     * Проверяет, является ли ключ путем в dot notation
     * @param string $keyName
     * @return bool
     */
    protected function isDotNotation(string $keyName): bool
    {
        return strpos($keyName, '.') !== false;
    }

    /**
     * Создает дефолтный кроп с учетом пропорций
     * @param AbstractImageAdapter $image
     * @param ImageModel $photo
     * @param int|null $aspectRatioX
     * @param int|null $aspectRatioY
     * @return void
     */
    protected function getDefaultCropBounds(
        AbstractImageAdapter $image,
        ImageModel           $photo,
        ?int                 $aspectRatioX = null,
        ?int                 $aspectRatioY = null
    ): void {
        if ($aspectRatioX === null || $aspectRatioY === null) {
            # Нужно получить пропорции для правильного кропа
            $sizesSorted = $this->sortSizes($photo->getCropSizes());
            if (array_key_exists('aspectLandscape', $sizesSorted) && !empty($sizesSorted['aspectLandscape'])) {
                [$aspectRatioX, $aspectRatioY] = head($sizesSorted['aspectLandscape']);
            } elseif (array_key_exists('aspectPortrait', $sizesSorted) && !empty($sizesSorted['aspectPortrait'])) {
                [$aspectRatioX, $aspectRatioY] = head($sizesSorted['aspectPortrait']);
            } elseif (array_key_exists('fitLandscape', $sizesSorted) && !empty($sizesSorted['fitLandscape'])) {
                [$aspectRatioX, $aspectRatioY] = head($sizesSorted['fitLandscape']);
            } else {
                throw new RuntimeException('Невозможно высчитать пропорции кропа по умолчанию');
            }
        }

        $imageSize = $image->getImageSize();
        if ($imageSize[0] > $imageSize[1]) {
            $newHeight = $imageSize[1];
            $newWidth = $imageSize[1] * ($aspectRatioX / $aspectRatioY);
        } else {
            $newHeight = $imageSize[0] * ($aspectRatioY / $aspectRatioX);
            $newWidth = $imageSize[0];
        }
        $photo->crop_bounds = [[0, 0], [
            $newWidth,
            $newHeight
        ]];
        Log::info('crop bounds', [
            'imageSize' => $imageSize,
            'newH' => $newHeight,
            'newW' => $newWidth,
            'bounds' => $photo->crop_bounds,
            'sizesSorted' => (!empty($sizesSorted)) ? $sizesSorted : ['no sizes', $aspectRatioX, $aspectRatioY]
        ]);
    }

    /**
     * Сортирует размеры от наибольшего к наименьшему
     * @param array $sizesSource
     * @return array
     */
    protected function sortSizes(array $sizesSource): array
    {
        $sizesSorted = [
            'aspectLandscape' => [],
            'aspectPortrait' => [],
            'fitLandscape' => [],
            'fitPortrait' => [],
        ];

        # Разбиваем по категориям
        foreach ($imagesSizes = $sizesSource as $key => $sizes) {
            if ($sizes['aspect_ratio']) {
                $sizes['size'][0] >= $sizes['size'][1] ? $sizesSorted['aspectLandscape'][$key] = $sizes['size'] :
                    $sizesSorted['aspectPortrait'][$key] = $sizes['size'];
            }

            if (!$sizes['aspect_ratio']) {
                $sizes['size'][0] >= $sizes['size'][1] ? $sizesSorted['fitLandscape'][$key] = $sizes['size'] :
                    $sizesSorted['fitPortrait'][$key] = $sizes['size'];
            }
        }

        # Сортируем каждую категорию
        foreach (array_keys($sizesSorted) as $sizeName) {
            uasort($sizesSorted[$sizeName], function ($first, $second) {
                if ($first[0] === $second[0] && $first[1] === $second[1]) {
                    return 0;
                }

                # Это корректною Нужна сортировка по убыванию
                return ($first[0] >= $second[0] && $first[1] >= $second[1]) ? -1 : 1;
            });
        }

        return $sizesSorted;
    }

    public function saveNewEntityWithSmartFitImage(int $entityId, string $entityType, string $src, $isMain = 1)
    {

        //        if ($this->isEntityExists($entityId, $entityType)) return false;
        $photo = ImageModel::create($insertStatements[] = [
            'item_id' => $entityId,
            'is_main' => $isMain,
            'type' => $entityType,
            'priority' => 1,
            'transformations' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $image = app()->make(AbstractImageAdapter::class, [
            'imageSource' => $src
        ]);
        $this->save($image, $photo, ImageModel::CROP_POSTFIX);
        $sizesSorted = $this->sortSizes($photo->getCropSizes());
        foreach ($sizesSorted as $groupName => $imageOrientationGroup) {
            # Создаем клон изображения
            $imageClone = clone $image;
            # Пробегаемся по каждому разрешению
            foreach ($imageOrientationGroup as $key => $size) {
                if (strpos($groupName, 'aspect') !== false) {
                    Log::info('saveNewEntityWithSmartFitImage', [$groupName, $key, 'resize']);
                    $this->smartResizeImage($imageClone, $size[0], $size[1]);
                    $this->save($imageClone, $photo, $key);
                } else {
                    Log::info('saveNewEntityWithSmartFitImage', [$groupName, $key, 'fit']);
                    $this->smartFitImage($imageClone, $size[0], $size[1], true);
                    $this->save($imageClone, $photo, 'crop_' . $key);
                    $imageClone = clone $image;
                }
            }
        }
    }

    /**
     * Пересохраняет все изображения, связанные с фото
     * @param mixed|ImageModel|int $photoOrId
     * @param string $extension
     * @param string $oldExtension
     * @param bool $isDeleteOrigins
     * @return bool
     * @throws BindingResolutionException
     */
    public function resaveWithExtension($photoOrId, string $extension = ImageModel::JPEG_EXTENSION, string $oldExtension = ImageModel::PNG_EXTENSION, bool $isDeleteOrigins = true): bool
    {
        if (!($photoOrId instanceof ImageModel)) {
            $photoOrId = ImageModel::query()->find($photoOrId);
        }
        # Пересохраняем оригинал изображения
        $photoOrId->setExtension($oldExtension);
        if ($photoOrId->src) {
            # Создаем экземпляр адаптера
            /** @var AbstractImageAdapter $image */
            $image = app()->make(AbstractImageAdapter::class, [
                'imageSource' => $photoOrId->src
            ]);
            $photoOrId->setExtension($extension);
            # Сохраняем оригинал фото
            $this->save($image, $photoOrId, null, $extension);
            # Применяем компрессию
            $image->enableCompression();
            # Сохраняем ресайзы изображения
            $this->saveImages($image, $photoOrId, $extension);
            # Сохраняем кроп и ресайзы кропа, если кроп есть
            $this->saveCrops($image, $photoOrId, $photoOrId->src_xl, $photoOrId->rotate_angle);
        }

        $photoOrId->setExtension($oldExtension);
        # Удаляем старые изображения
        if ($isDeleteOrigins) {
            $this->deleteImagesFiles($photoOrId);
        }

        return true;
    }

    /**
     * Проверяет, удовлетворяют ли размеры изображения минимальным значениям
     * @param int $width
     * @param int $height
     * @return bool
     */
    protected function isValidSize(int $width, int $height): bool
    {
        return static::MIN_SIZE[0] <= $width && $height >= static::MIN_SIZE[1];
    }

    /**
     * Вычисляет размер изображений до обработки
     * @param string $image
     * @return double
     * */
    public function getImageSize(string $image)
    {
        $lenght = strlen($image);
        $type = (substr($image, -2) == '==') ? 2 : 1;
        $size = ($lenght * (3 / 4)) - $type;
        return $size;
    }
}
