<?php

namespace Blog\Model\Entity; 

abstract class Entity
{
	
	public function __construct($data[])
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
			$method = 'set'.ucfirst($attribut);

		  if (is_callable([$this, $method]))
		  {
		    $this->$method($value);
		  }
		}
	}

}
