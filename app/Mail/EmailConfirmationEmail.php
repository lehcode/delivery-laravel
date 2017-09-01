<?php

/**
 * Created by Antony Repin
 * Date: 27.05.2017
 * Time: 3:57
 */

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailConfirmationMail extends Mailable implements ShouldQueue
{
	use Queueable, SerializesModels;

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var int
	 */
	protected $uuid;

	/**
	 * @var User\
	 */
	protected $user;

	/**
	 * RestorePassword constructor.
	 * @param User $user
	 * @param string $key
	 */
	public function __construct(User $user, $key)
	{
		$this->key = $key;
		$this->uuid = $user->id;
		$this->user = $user;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->markdown('email.email-confirmation', ['key' => $this->key, 'user_id' => $this->uuid]);
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function getUuid()
	{
		return $this->uuid;
	}
}
