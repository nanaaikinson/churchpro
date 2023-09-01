<?php

namespace App\Traits;

use App\Actions\TransformPaginationResponseAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
  /**
   * Success Response.
   *
   * @param mixed $data
   * @param int $statusCode
   * @return JsonResponse
   */
  private function successResponse(mixed $data, int $statusCode = Response::HTTP_OK): JsonResponse
  {
    return new JsonResponse($data, $statusCode);
  }

  /**
   * Error Response.
   *
   * @param mixed $data
   * @param string $message
   * @param int $statusCode
   * @return JsonResponse
   */
  private function errorResponse(mixed $data, string $message = '', int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
  {
    if (!$message) {
      $message = Response::$statusTexts[$statusCode];
    }

    $data = [
      'message' => $message,
      'errors' => $data,
    ];

    return new JsonResponse($data, $statusCode);
  }

  /**
   * Response with status code 200.
   *
   * @param mixed $data
   * @return JsonResponse
   */
  public function okResponse(mixed $data): JsonResponse
  {
    return $this->successResponse($data);
  }

  /**
   * Response with status code 201.
   *
   * @param mixed $data
   * @return JsonResponse
   */
  public function createdResponse(mixed $data): JsonResponse
  {
    return $this->successResponse($data, Response::HTTP_CREATED);
  }

  /**
   * Response with status code 204.
   *
   * @return JsonResponse
   */
  public function noContentResponse(): JsonResponse
  {
    return $this->successResponse([], Response::HTTP_NO_CONTENT);
  }

  /**
   * Response with status code 400.
   *
   * @param mixed $data
   * @param string $message
   * @return JsonResponse
   */
  public function badRequestResponse(mixed $data, string $message = ''): JsonResponse
  {
    return $this->errorResponse($data, $message, Response::HTTP_BAD_REQUEST);
  }

  /**
   * Response with status code 401.
   *
   * @param mixed $data
   * @param string $message
   * @return JsonResponse
   */
  public function unauthorizedResponse(mixed $data, string $message = ''): JsonResponse
  {
    return $this->errorResponse($data, $message, Response::HTTP_UNAUTHORIZED);
  }

  /**
   * Response with status code 403.
   *
   * @param mixed $data
   * @param string $message
   * @return JsonResponse
   */
  public function forbiddenResponse(mixed $data, string $message = ''): JsonResponse
  {
    return $this->errorResponse($data, $message, Response::HTTP_FORBIDDEN);
  }

  /**
   * Response with status code 404.
   *
   * @param mixed $data
   * @param string $message
   * @return JsonResponse
   */
  public function notFoundResponse(mixed $data, string $message = ''): JsonResponse
  {
    return $this->errorResponse($data, $message, Response::HTTP_NOT_FOUND);
  }

  /**
   * Response with status code 409.
   *
   * @param mixed $data
   * @param string $message
   * @return JsonResponse
   */
  public function conflictResponse(mixed $data, string $message = ''): JsonResponse
  {
    return $this->errorResponse($data, $message, Response::HTTP_CONFLICT);
  }

  /**
   * Response with status code 422.
   *
   * @param mixed $data
   * @param string $message
   * @return JsonResponse
   */
  public function unprocessableResponse(mixed $data, string $message = ''): JsonResponse
  {
    return $this->errorResponse($data, $message, Response::HTTP_UNPROCESSABLE_ENTITY);
  }

  /**
   * Pagination Response.
   *
   * @param LengthAwarePaginator $data
   * @param string $message
   * @return JsonResponse
   */
  public function paginationResponse(LengthAwarePaginator $data, string $message = ''): JsonResponse
  {
    $pagination = $this->paginatorTransformer($data);

    return $this->successResponse($pagination);
  }

  public function dataResponse(mixed $data, string $message = '', bool $created = false): JsonResponse
  {
    $statusCode = $created ? Response::HTTP_CREATED : Response::HTTP_OK;

    return $this->successResponse([
      'data' => $data,
      'message' => $message
    ], $statusCode);
  }

  public function messageResponse(string $message = ''): JsonResponse
  {
    return $this->successResponse([
      'message' => $message
    ]);
  }

  public function paginatorTransformer(LengthAwarePaginator $data): array
  {
    $content = $data->getCollection();

    // Show from 1 to 10 of 100 items
    $from = ($data->currentPage() - 1) * $data->perPage() + 1;
    $to = $from + $data->count() - 1;
    $of = $data->total();
    $showing = "Showing {$from} to {$to} of {$of} items";

    $pagination = [
      'data' => $content,
      'meta' => [
        'show' => $showing,
        'total' => $data->total(),
        'count' => $data->count(),
        'per_page' => $data->perPage(),
        'current_page' => $data->currentPage(),
        'total_pages' => $data->lastPage(),
        'links' => [
          'first' => $data->url(1),
          'last' => $data->url($data->lastPage()),
          'prev' => $data->previousPageUrl(),
          'next' => $data->nextPageUrl(),
        ],
      ]
    ];

    return $pagination;
  }
}