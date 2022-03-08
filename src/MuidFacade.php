<?php

namespace Rawaby88\Muid;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rawaby88\Muid\Skeleton\SkeletonClass
 */
class MuidFacade extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static
	function getFacadeAccessor ()
	{
		return 'muid';
	}
}
