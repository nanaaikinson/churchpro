<?php

namespace App\Http\Middleware;

use App\Helpers\KeyCaseConverter;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertResponseToCamelCase
{
  /**
   * Handle an incoming request.
   *
   * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $response = $next($request);

    if ($response instanceof JsonResponse) {
      $response->setData(
        resolve(KeyCaseConverter::class)->convert(
          KeyCaseConverter::CASE_CAMEL,
          json_decode($response->content(), true)
        )
      );
    }

    return $response;
  }
}
