<?php
declare(strict_types=1);

namespace App\Traits;

use Exception;
use JsonSerializable;
use function response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Arrayable;

use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

trait HtqApiResponse
{
    private ?array $_api_helpers_defaultSuccessData = ['payload' => ['success' => true]];

    public function setDefaultSuccessResponse(?array $content = null): self
    {
        $this->_api_helpers_defaultSuccessData = $content ?? [];
        return $this;
    }

    public function respondOk(string $message): JsonResponse
    {
        return $this->respondSuccess([
            'payload' => ['success' => $message]
        ]);
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $contents
     */
    public function respondSuccess($contents = null): JsonResponse
    {
        $contents = $this->wrapArray($contents);

        $data = [] === $contents
            ? $this->_api_helpers_defaultSuccessData
            : $contents;

        return $this->apiResponse($data);
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     */
    public function respondCreated($data = null): JsonResponse
    {
        return $this->apiResponse(
            $this->wrapArray($data),
            Response::HTTP_CREATED
        );
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     */
    public function respondNoContent($data = null): JsonResponse
    {
        return $this->apiResponse($this->wrapArray($data), Response::HTTP_NO_CONTENT);
    }

    public function respondError(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            [
                'payload' => [
                    'errors' => ['400' => $message ?? 'Error']
                ]
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    public function respondUnAuthenticated(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            [
                'payload' => [
                    'errors' => ['401' => $message ?? 'Unauthenticated']
                ]
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function respondForbidden(?string $message = null): JsonResponse
    {
        return $this->apiResponse(
            [
                'payload' => [
                    'errors' => ['403' => $message ?? 'Forbidden']
                ]
            ],
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound(
        $message,
        ?string $key = '404'
    ): JsonResponse {
        return $this->apiResponse(
            [
                'payload' => [
                    'errors' => [$key => $this->morphMessage($message)]
                ]
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @param string|\Exception $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondFailedValidation(
        $message,
        ?string $key = '422'
    ): JsonResponse {
        return $this->apiResponse(
            [
                'payload' => [
                    'errors' => [$key => $this->morphMessage($message)]
                ]
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @param Arrayable $message
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondFailedValidations(
        Validator $validator,
        ?string $key = 'errors'
    ): JsonResponse {
        return $this->apiResponse(
            [
                'payload' => [$key => $this->morphToArray($validator->errors())]
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function respondTeapot(): JsonResponse
    {
        return $this->apiResponse(
            [
                'payload' => ['message' => 'I\'m a teapot']
            ],
            Response::HTTP_I_AM_A_TEAPOT
        );
    }

    private function apiResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }


    private function wrapArray($data)
    {
        $data = $this->morphToArray($data) ?? [];
        if (!array_key_exists('data', $data)
            && !array_key_exists('payload', $data)) {
            $data = ['data' => $data];
        }

        return $data;
    }

    /**
     * @param array|Arrayable|JsonSerializable|null $data
     * @return array|null
     */
    private function morphToArray($data)
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        if ($data instanceof JsonSerializable) {
            return $data->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param string|\Exception $message
     * @return string
     */
    private function morphMessage($message): string
    {
        return $message instanceof Exception
          ? $message->getMessage()
          : $message;
    }
}
