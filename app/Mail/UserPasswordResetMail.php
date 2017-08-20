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

class UserPasswordResetMail extends Mailable implements ShouldQueue
{
	use Queueable, SerializesModels;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * User ID
	 *
	 * @var int
	 */
	protected $uuid;

	/**
	 * RestorePassword constructor.
	 * @param User $user
	 * @param string $password
	 */
	public function __construct(User $user, $password)
	{
		$this->password = $password;
		$this->uuid = $user->id;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->markdown('email.reset-password', ['key' => $this->password, 'user_id' => $this->uuid]);
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	public function getUserId(){
		return $this->uuid;
	}
}
