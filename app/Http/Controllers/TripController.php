<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:21
 */

namespace App\Http\Controllers;

use App\Http\Requests\TripRequest;
use App\Http\Responses\TripResponse;
use App\Services\Responder\ResponderService;
use App\Services\Trip\TripService;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

/**
 * Class TripController
 * @package App\Http\Controllers
 */
class TripController extends Controller
{
	/**
	 * @var TripService
	 */
	protected $tripService;

	/**
	 * TripController constructor.
	 *
	 * @param TripService      $tripService
	 * @param ResponderService $responderService
	 */
	public function __construct(
		TripService $tripService,
		ResponderService $responderService
	)
	{
		$this->tripService = $tripService;
		$this->responderService = $responderService;
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function all()
	{
		return $this->responderService->fractal($this->tripService->all(), TripResponse::class);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function fromCurrentCity()
	{
		return $this->responderService->objectResponse($this->tripService->getTripsFromCurrentCity());
	}

	/**
	 * @param $city
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function fromCity($city)
	{
		return $this->responderService->fractal($this->tripService->getTripsFromCity($city), TripResponse::class);
	}

	/**
	 * @param integer $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function get($id)
	{
		return $this->responderService->fractal($this->tripService->item($id), TripResponse::class);
	}

	/**
	 * @param TripRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ValidationException
	 */
	public function create(TripRequest $request)
	{
		$params = $request->except(['XDEBUG_SESSION_START']);
		$trip = $this->tripService->create($params);
		return $this->responderService->fractal($trip, TripResponse::class);
	}

	/**
	 * @param Request $request
	 * @param string  $startDate
	 * @param string  $endDate
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function getByDatePeriod(Request $request, $startDate, $endDate)
	{
		$dates = [
			'start' => Date::createFromFormat('Y-m-d', $startDate),
			'end' => Date::createFromFormat('Y-m-d', $endDate),
		];
		return $this->responderService->fractal($this->tripService->getListByStartAndEnd($dates), TripResponse::class);
	}

	/**
	 * @param $date
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function getByDate($date)
	{
		$d = Date::createFromFormat('Y-m-d', $date);
		return $this->responderService->fractal($this->tripService->getListByDate($d), TripResponse::class);
	}

	/**
	 * @param null $userType
	 * @param null $cityId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCity($userType = null, $cityId = null)
	{
		return $this->responderService->objectResponse($this->tripService->getCity($cityId));
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCities()
	{
		return $this->responderService->objectResponse($this->tripService->getAllCities());
	}

	/**
	 * @param string     $search
	 * @param string $countryCode
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function findCityByName($userType, $search, $countryCode)
	{
		return $this->responderService->objectResponse($this->tripService->findCity($search, $countryCode));
	}


}
