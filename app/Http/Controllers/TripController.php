<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:21
 */

namespace App\Http\Controllers;

use App\Http\Responses\TripDetailsResponse;
use App\Http\Responses\TripResponse;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\Trip\TripServiceInterface;

/**
 * Class TripController
 * @package App\Http\Controllers
 */
class TripController
{
	/**
	 * @var
	 */
	protected $tripService;

	protected $responderService;

	/**
	 * TripController constructor.
	 *
	 * @param TripServiceInterface      $tripServiceInterface
	 * @param ResponderServiceInterface $responderServiceInterface
	 */
	public function __construct(
		TripServiceInterface $tripServiceInterface,
		ResponderServiceInterface $responderServiceInterface
) {
	
		$this->tripService = $tripServiceInterface;
		$this->responderService = $responderServiceInterface;
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getTrips()
	{
		return $this->responderService->fractal($this->tripService->all(), TripResponse::class);
	}
}
