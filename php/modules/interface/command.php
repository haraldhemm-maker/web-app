<?php
/**
 * php/modules/interface/command.php
 *
 * Command Interface - Interface der Command-Control-Schicht.
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */
namespace HaHeLa;
use HaHeLa\HttpRequestModel as HttpRequest;
use HaHeLa\HttpResponseModel as HttpResponse;


/**
 * Command Interface - Interface der Command-Control-Schicht.
 */
interface CommandInterface
{
  /**
   * Kommando ausfuehren.
   *
   * @param HttpRequest $request
   * @param HttpResponse $response
   * @return object
   */
  public function execute (HttpRequest $request, HttpResponse $response);
}