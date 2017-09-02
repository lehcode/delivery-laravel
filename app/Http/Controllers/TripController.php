<?php
/**
 * Created by Antony Repin
 * Date: 31.05.2017
 * Time: 3:21
 */

namespace App\Http\Controllers;

use App\Http\Requests\TripRequest;
use App\Http\Responses\CityResponse;
use App\Http\Responses\TripResponse;
use App\Models\City;
use App\Models\User;
use App\Services\Responder\ResponderService;
use App\Services\Trip\TripService;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

		if (!\Auth::user()->hasRole(['carrier', 'admin', 'root'])) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->all(), TripResponse::class, 0 , [true]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function fromCurrentCity()
	{
		if (!\Auth::user()->hasRole(['customer', 'admin', 'root'])) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->getTripsFromCurrentCity(), TripResponse::class);
	}

	/**
	 * @param $city
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function fromCity($city)
	{
		if (!\Auth::user()->hasRole(['customer', 'admin', 'root'])) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->getTripsFromCity($city), TripResponse::class);
	}

	/**
	 * @param Request $request
	 * @param string  $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function get(Request $request, $id)
	{
		if (!\Auth::user()->hasRole(['customer', 'carrier', 'admin', 'root'])) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->byId($request, $id), TripResponse::class);
	}

	/**
	 * @param TripRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function create(TripRequest $request)
	{
		if (!\Auth::user()->hasRole(array_merge(['carrier'], User::ADMIN_ROLES))) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->create($request), TripResponse::class);
	}

	/**
	 * @param Request $request
	 * @param         $startDate
	 * @param         $endDate
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getByDatePeriod(Request $request, $startDate, $endDate)
	{
		if (!\Auth::user()->hasRole(['customer', 'admin', 'root'])) {
			throw new AccessDeniedHttpException("Forbidden");
		}

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
	 */
	public function getByDate($date)
	{
		if (!\Auth::user()->hasRole(['customer', 'admin', 'root'])) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		$d = Date::createFromFormat('Y-m-d', $date);
		return $this->responderService->fractal($this->tripService->getListByDate($d), TripResponse::class);
	}

	/**
	 * @param string $userType
	 * @param int    $cityId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCity($userType = null, $cityId = null)
	{
		if (!\Auth::user()->hasRole(array_merge(['carrier'], User::ADMIN_ROLES))) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->getCity($cityId), CityResponse::class);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCities()
	{
		$roles = array_merge(User::ADMIN_ROLES, [User::ROOT_ROLE], ['carrier', 'customer']);

		if (!\Auth::user()->hasRole($roles)) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->getAllCities(), CityResponse::class);
	}

	/**
	 * @param Request $request
	 * @param string  $search
	 * @param string  $countryCode
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws AccessDeniedHttpException
	 * @throws \Exception
	 */
	public function findCityByName(Request $request, $search, $countryCode)
	{
		if (!\Auth::user()->hasRole(['carrier', 'customer', 'admin', 'root'])) {
			throw new AccessDeniedHttpException("Forbidden");
		}

		return $this->responderService->fractal($this->tripService->findCity($search, $countryCode), CityResponse::class);
	}


}
