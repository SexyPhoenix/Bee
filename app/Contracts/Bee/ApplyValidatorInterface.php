<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:00:17
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-06-14 16:45:15
 */
namespace App\Contracts\Bee;

interface ApplyValidatorInterface
{
	public function validate($item, $updated);
}