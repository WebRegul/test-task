<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *  title="happyday gallery api",
 *  description="
## внимание, друг!
- тестовый юзер на деве: логин `79999999999`, пароль `123123`
- код верификации на деве всегда `4444`
- чтобы понять как пользоваться документацией -
читай [справочную статью](https://track.webregul.ru/youtrack/articles/HDGL-A-2/%D0%B4%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D1%86%D0%B8%D1%8F-%D0%BF%D0%BE-API)
и смотри видос [там же](https://track.webregul.ru/youtrack/articles/HDGL-A-2/%D0%B4%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D1%86%D0%B8%D1%8F-%D0%BF%D0%BE-API)
- http-ошибки (коды и типы исключений) описаны в [отдельной статье](https://track.webregul.ru/youtrack/articles/HDGL-A-4/)
",
 *  version="1.0.0",
 *  @OA\Contact(
 *    email="happyday@webregul.ru",
 *    name="WebRegul developers team"
 *  )
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     description="используйте JWT-токен, полученный из методов security/login или security/verify",
 *     name="token based auth",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * ),
 * @OA\Tag(
 *   name="security",
 *   description="методы обеспечения доступа и безопасности",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="user",
 *   description="методы работы с пользователями",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="profile",
 *   description="методы работы с профилями",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="member",
 *   description="методы работы с синглтоном мембер",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="gallery",
 *   description="методы работы с галереями",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="cabinet",
 *   description="методы, доступные в кабинете пользователя",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="web",
 *   description="методы, доступные без аутентификации",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="storage",
 *   description="методы работы с хранилищами",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="tariff",
 *   description="методы работы с тарифами",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="payment",
 *   description="методы работы с платежами",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="image",
 *   description="методы работы с изображениями",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="image-editor",
 *   description="методы работы редактора изображений",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="contact",
 *   description="методы работы с контактами",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * ),
 * @OA\Tag(
 *   name="notification",
 *   description="методы работы с уведомлениями",
 *   @OA\ExternalDocumentation(
 *     description="http-ошибки API (коды и типы исключений)",
 *     url="https://track.webregul.ru/youtrack/articles/HDGL-A-4/"
 *   )
 * )
 */

/**
 * @OA\Schema(
 *   schema="RequestValidationErrorsSchema",
 *   title="схема ошибок валидации реквестов",
 *   @OA\Property(
 *     property="success",
 *     description="флаг успешности операции",
 *     type="boolean",
 *     default="false"
 *   ),
 *   @OA\Property(
 *     property="message",
 *     type="string"
 *   ),
 *   @OA\Property(property="errors[]", type="array",
 *     @OA\Items(
 *       @OA\Property(property="request_key[]", type="array",
 *         @OA\Items(
 *           @OA\Property(property="error_messages[]", type="array",
 *             @OA\Items(type="string")
 *           )
 *         )
 *       )
 *     )
 *   )
 * ),
 * @OA\Schema(
 *   schema="SuccessAuthSchema",
 *   title="схема успешной аутентификации",
 *   @OA\Property(
 *     property="access_token",
 *     type="string",
 *     description="JWT-токен"
 *   ),
 *   @OA\Property(
 *     property="token_type",
 *     type="string",
 *     description="тип JWT-токена",
 *     default="bearer"
 *   ),
 *   @OA\Property(
 *     property="expires_in",
 *     type="integer",
 *     description="срок действия JWT-токена",
 *     example="1200"
 *   )
 * )
 */

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    //
}
