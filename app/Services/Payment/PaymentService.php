<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 16:54
 */

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use App\Repositories\Payment\PaymentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\BaseServiceInterface;

/**
 * Class PaymentService
 * @package App\Services\Payment
 */
class PaymentService implements PaymentServiceInterface
{
	/**
	 * @var PaymentRepository
	 */
	protected $paymentRepository;

	/**
	 * PaymentService constructor.
	 *
	 * @param PaymentRepository $paymentRepository
	 */
	public function __construct(PaymentRepository $paymentRepository)
	{
		$this->paymentRepository = $paymentRepository;
	}

	/**
	 * @param array $tripData
	 *
	 * @return mixed
	 */
	public function create(array $tripData)
	{
		return DB::transaction(function () use ($tripData) {
			$trip = Trip::create($tripData);
			return $trip;
		});
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all()
	{
		return $this->paymentRepository->all();
	}

	/**
	 * @return mixed
	 */
	public function userPayments()
	{
		return $this->paymentRepository->userPayments();
	}

	/**
	 * @param int $id
	 *
	 * @return Trip
	 */
	public function item($id)
	{
		return $this->tripRepository->find($id);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function paymentTypes()
	{
		return PaymentType::all();
	}

	public function edit(Payment $payment, array $params)
	{
		return false;
	}

	

}
