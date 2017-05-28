<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 19:54
 */

namespace App\Http\Controllers\Customer;

/**
 * Class OrderController
 * @package App\Http\Controllers\Customer
 */
class OrderController
{
	/**
	 * @var ResponderServiceInterface
	 */
	protected $responderService;

	/**
	 * @var TripRepositoryInterface
	 */
	protected $tripRepository;

	/**
	 * @var TripServiceInterface
	 */
	protected $tripService;
	/**
	 * OrderController constructor.
	 *
	 * @param ResponderServiceInterface $responderServiceInterface
	 * @param TripServiceInterface      $tripServiceInterface
	 * @param TripRepositoryInterface   $tripRepositoryInterface
	 */
	public function __construct(
		ResponderServiceInterface $responderServiceInterface,
		TripServiceInterface $tripServiceInterface,
		TripRepositoryInterface $tripRepositoryInterface
	) {
		$this->responderService = $responderServiceInterface;
		$this->tripRepository = $tripRepositoryInterface;
		$this->tripService = $tripServiceInterface;
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function trips(Request $request) {
		$trips = $this->tripService->getList();
		$trips = $this->responderService->filterQuery($trips, $request);

		return $this->responderService->fractal($trips, TripResponse::class, $request->get('page'), [false]);
	}

	/**
	 * @param Trip $trip
	 *
	 * @return mixed
	 */
	public function trip(Trip $trip) {
		return $this->responderService->fractal($trip, TripResponse::class, -1);
	}
}
