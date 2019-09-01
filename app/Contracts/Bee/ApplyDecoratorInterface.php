<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:00:17
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-06-27 11:35:45
 */
namespace App\Contracts\Bee;

interface ApplyDecoratorInterface
{
	public function handle($items, $operator, $updated);
}