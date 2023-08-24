<?php

namespace App\Http\Middleware;

use App\Helpers\KeyCaseConverter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertRequestToSnakeCase
{
  /**
   * Handle an incoming request.
   *
   * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $request->replace(
      resolve(KeyCaseConverter::class)->convert(
        KeyCaseConverter::CASE_SNAKE,
        $request->all()
      )
    );

    return $next($request);
  }
}
