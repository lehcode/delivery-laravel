<?php
/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 19:54
 */

namespace App\Http\Controllers;

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

	public function getOrders()
	{
		
	}
	
	public function getOrder()
	{
		//
	}
}
