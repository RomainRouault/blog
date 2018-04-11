<?php
/**
*Abstract Entity used to hydrate child classes entities 
*
*/

namespace Blog\Model\Entity; 

abstract class Entity
{
	
	public function __construct($data)
	{
		if (!empty($data)) 
		{
		  $this->hydrate($data);
		}
	}

	public function hydrate($data)
	{
		foreach ($data as $attribut => $value)
		{
			//protect from xss for string values
			if (is_string($value))
			{
				$safeValue = htmlspecialchars($value);
			}

			//if not string
			else
			{
				$safeValue = $value;
			}

			//get the right method
			$method = 'set'.ucfirst($attribut);

		  if (is_callable([$this, $method]))
		  {
		    $this->$method($safeValue);
		  }
		}
	}

}
