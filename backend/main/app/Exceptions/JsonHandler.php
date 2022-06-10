<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Validation\ValidationException;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\QueryException;

/**
 * Class JsonHandler
 * @package App\Exceptions
 */
class JsonHandler
{
    /**
     * @var \Throwable
     */
    private $exception;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * JsonHandler constructor.
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
        $this->setError();
    }

    /**
     *
     */
    private function setError(): void
    {
        // документация по кодам
        // https://track.webregul.ru/youtrack/articles/HDGL-A-4/
        $message = $this->exception->getMessage();
        $errors = [];
        $code = 0;

        if ($this->exception instanceof ItemNotFoundException) {
            $code = 404;
        } elseif ($this->exception instanceof ValidationException) {
            $code = 422;
            if ($response = $this->exception->getResponse()) {
                $failed = collect($this->exception->validator->failed());
                $content = json_decode($response->getContent());

                /**
                 * если exception не имеет кастомного сообщения заменяем дефолтное
                 * на первую ошибку (для тех мест на фронте где не нужен массив всех ошибок)
                 */
                if ($content->message == 'The given data was invalid.') {
                    $message = Arr::first(Arr::flatten($content->errors));
                }

                $errors = $content->errors;

                $detect404 = collect($failed->values()->filter(function ($item, $key) {
                    return Arr::exists($item, 'Exists')
                        || Arr::exists($item, 'String');
                })->toArray())->isNotEmpty();

                if (preg_match('/не существует/ui', $message)) {
                    $detect404 = true;
                }

                if ($detect404) {
                    $code = 404;
                }
            }
        } elseif ($this->exception instanceof InvalidFormatException) {
            $code = 422;
        } elseif ($this->exception instanceof QueryException) {
            if (preg_match('/constraint\sfails/i', $message)) {
                $code = 409;
            }
        } elseif ($this->exception instanceof AuthenticationException) {
            $code = 401;
        } elseif ($this->exception instanceof AuthorizationException) {
            $code = 401;
        }

        $this->errorCode = $code ?: $this->exception->getCode();
        $this->errorMessage = $message;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function checkError(): bool
    {
        return !empty($this->errorCode);
    }

    /**
     * @param int $code
     * @return int
     */
    protected function getHttpCode(int $code): int
    {
        return in_array($code, array_keys(Response::$statusTexts)) ? $code : 520;
    }

    /**
     * @param int|null $code
     * @return JsonResponse
     */
    public function jsonException(?int $code = null): JsonResponse
    {
        $result = [
            'success' => false,
            'message' => $this->errorMessage
        ];

        if (!App::environment(['production'])) {
            $result['from'] = sprintf('%s:%s', $this->exception->getFile(), $this->exception->getLine());
        }

        if (!empty($this->errors)) {
            $result['errors'] = $this->errors;
        }

        $code = $this->getHttpCode((int)$code ?: (int)$this->errorCode);

        return response()->json($result, $code);
    }
}
