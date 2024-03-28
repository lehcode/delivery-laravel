<?php

/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:28
 */

namespace App\Services\Rating;

use App\Models\Destination;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Trip;
use App\Models\User;
use App\Repositories\Rating\RatingRepositoryInterface;

class RatingService implements RatingServiceInterface
{
    /**
     * @var RatingRepositoryInterface
     */
    protected $ratingRepository;

    /**
     * RatingService constructor.
     * @param RatingRepositoryInterface $ratingRepository
     */
    public function __construct(RatingRepositoryInterface $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

    /**
     * @param User $user
     * @param Order $order
     * @param $score
     * @param null $comment
     * @param array $reasons
     * @return Rating|\Illuminate\Database\Eloquent\Model
     */
    public function rateDriver(User $user, Order $order, $score, $comment = null, array $reasons = []) {
        return $this->ratingRepository->rate($user, null, null, $order, $score, $comment, $reasons);
    }

    /**
     * @param User $user
     * @param Order $order
     * @param $score
     * @param null $comment
     * @return Rating|\Illuminate\Database\Eloquent\Model
     */
    public function rateCustomer(User $user, Order $order, $score, $comment = null) {
        return $this->ratingRepository->rate($user, null, null, $order, $score, $comment);
    }

    /**
     * @param Destination $destination
     * @param Order $order
     * @param $score
     * @return Rating|\Illuminate\Database\Eloquent\Model
     */
    public function rateDestination(Destination $destination, Order $order, $score) {
        return $this->ratingRepository->rate(null, $destination, null, $order, $score);
    }

    /**
     * @param Trip $trip
     * @param Order $order
     * @param $score
     * @return Rating|\Illuminate\Database\Eloquent\Model
     */
    public function rateTrip(Trip $trip, Order $order, $score) {
        return $this->ratingRepository->rate(null, null, $trip, $order, $score);
    }

    /**
     * @param User $user
     * @return int
     */
    public function getUserRating(User $user) {
        return $this->ratingRepository->getRating('user_id', $user->id);
    }

    /**
     * @param Destination $destination
     * @return int
     */
    public function getDestinationRating(Destination $destination) {
        return $this->ratingRepository->getRating('destination_id', $destination->id);
    }

    /**
     * @param Trip $trip
     * @return int
     */
    public function getTripRating(Trip $trip) {
        return $this->ratingRepository->getRating('trip_id', $trip->id);
    }
}
