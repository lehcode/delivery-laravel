<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 16:54
 */

namespace App\Services\Trip;


class TripService
{
	/**
	 * @var TripRepositoryInterface
	 */
	protected $tripRepository;

	public function __construct(TripRepositoryInterface $tripRepositoryInterface)
	{
		$this->tripRepository = $tripRepositoryInterface;
	}

	public function getList() {
		return $this->tripRepository->getBuilder()->enabled();
	}
}
