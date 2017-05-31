<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:21
 */

namespace App\Http\Controllers;

use App\Http\Responses\TripResponse;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\Trip\TripServiceInterface;
use Illuminate\Http\Request;

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

	/**
	 * @var ResponderServiceInterface
	 */
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

	public function getTrip($trip_id)
	{
		return $this->responderService->fractal($this->tripService->find($trip_id), TripResponse::class);
	}


	public function createTrip(Request $request)
	{
		try {
			$params = $request->all();
			$trip = $this->tripService->create($params);
			return $this->responderService->fractal($trip, TripResponse::class);
		} catch (ValidationException $e) {
			throw $e;
		} catch (\Exception $e) {
			return $this->responderService->errorResponse($e);
		}
	}
}
