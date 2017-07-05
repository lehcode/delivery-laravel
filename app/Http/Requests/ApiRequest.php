<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 15:05
 */

namespace App\Http\Requests;

use App\Exceptions\MultipleExceptions;
use App\Services\Responder\ResponderServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ApiRequest
 * @package App\Http\Requests
 */
class ApiRequest extends FormRequest
{
	/**
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * @param array $errors
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function response(array $errors)
	{
		$responderService = app()->make(ResponderServiceInterface::class);
		$e = new MultipleExceptions($errors);
		return $responderService->errorResponse($e, 422);
	}

	/**
	 * Validate the class instance.
	 *
	 * @return void
	 */
	public function validate()
	{
		$this->prepareForValidation();

		$instance = $this->getValidatorInstance();

		if (!$this->passesAuthorization()) {
			$this->failedAuthorization();
		} elseif (!$instance->passes()) {
			$this->failedValidation($instance);
		}

		if (method_exists($this, 'validated')) {
			$this->container->call([$this, 'validated']);
		} else {
			return true;
		}
	}

}
